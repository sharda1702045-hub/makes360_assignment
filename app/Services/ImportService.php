<?php

namespace App\Services;

use App\Models\ImportJob;
use Illuminate\Support\Facades\DB;

class ImportService
{
    public function getImportStatus($id)
    {
        $job = ImportJob::findOrFail($id);
        
        $percentage = $job->total_rows > 0 
            ? round(($job->processed_rows / $job->total_rows) * 100) 
            : 0;

        return [
            'id' => $job->id,
            'status' => $job->status,
            'percentage' => $percentage,
            'processed' => $job->processed_rows,
            'total' => $job->total_rows,
            'failed' => $job->failed_rows,
            'duplicates' => $job->duplicate_rows,
            'current_chunk' => ceil($job->processed_rows / 1000), // Assuming 1k chunk size
            'total_chunks' => ceil($job->total_rows / 1000),
        ];
    }

    public function getHistory($limit = 10)
    {
        return ImportJob::latest()->take($limit)->get();
    }

    public function getLatestActiveImport()
    {
        return ImportJob::whereIn('status', ['Pending', 'Processing', 'Validating', 'Inserting'])->latest()->first();
    }

    public function processCsv(ImportJob $job, $filePath, $contactListId = null)
    {
        $job->update(['status' => 'Validating']);
        
        if (!file_exists($filePath)) {
            $job->update(['status' => 'Failed', 'error_log' => ['file' => 'File not found at ' . $filePath]]);
            return;
        }

        $file = fopen($filePath, 'r');
        
        // Normalize headers
        $rawHeaders = fgetcsv($file);
        if (!$rawHeaders) {
            $job->update(['status' => 'Failed', 'error_log' => ['file' => 'Empty or invalid CSV file']]);
            fclose($file);
            return;
        }
        
        $headers = array_map(function($h) {
            return strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h))); // Remove non-printable chars/BOM and lowercase
        }, $rawHeaders);

        $rowCount = 0;
        
        // Count total rows
        while (fgetcsv($file) !== false) {
            $rowCount++;
        }
        $job->update(['total_rows' => $rowCount]);
        
        rewind($file);
        fgetcsv($file); // skip header again

        $job->update(['status' => 'Inserting']);

        $successCount = 0;
        $failedCount = 0;
        $duplicateCount = 0;
        $errors = [];

        while (($row = fgetcsv($file)) !== false) {
            // Skip empty rows
            if (empty($row) || (count($row) === 1 && empty($row[0]))) {
                continue;
            }

            try {
                if (count($headers) !== count($row)) {
                    throw new \Exception("Column mismatch: Expected " . count($headers) . " but got " . count($row));
                }

                $data = array_combine($headers, $row);
                
                if (empty($data['email'])) {
                    throw new \Exception("Missing email column");
                }

                $email = trim($data['email']);
                
                $exists = \App\Models\Contact::where('email', $email)
                    ->where('user_id', $job->user_id)
                    ->exists();

                if ($exists) {
                    $duplicateCount++;
                }

                $contact = \App\Models\Contact::updateOrCreate(
                    ['email' => $email, 'user_id' => $job->user_id],
                    [
                        'first_name' => $data['first_name'] ?? ($data['firstname'] ?? null),
                        'last_name' => $data['last_name'] ?? ($data['lastname'] ?? null),
                        'status' => 'active'
                    ]
                );

                if ($contactListId) {
                    // Check if already in list to avoid double counting
                    $alreadyInList = DB::table('contact_list_mapping')
                        ->where('contact_id', $contact->id)
                        ->where('contact_list_id', $contactListId)
                        ->exists();

                    if (!$alreadyInList) {
                        DB::table('contact_list_mapping')->insert([
                            'contact_id' => $contact->id,
                            'contact_list_id' => $contactListId
                        ]);
                        DB::table('contact_lists')->where('id', $contactListId)->increment('total_contacts');
                    }
                }
                
                $successCount++;
            } catch (\Exception $e) {
                $failedCount++;
                \Illuminate\Support\Facades\Log::error("Import Error: " . $e->getMessage(), [
                    'job_id' => $job->id,
                    'row' => $row
                ]);
                if (count($errors) < 10) { // Log first 10 errors
                    $errors[] = "Row " . ($successCount + $failedCount) . ": " . $e->getMessage();
                }
            }

            // Update progress occasionally or at the end for performance, 
            // but for feedback, every 100 rows is good.
            if (($successCount + $failedCount) % 100 == 0) {
                $job->update([
                    'processed_rows' => $successCount + $failedCount,
                    'failed_rows' => $failedCount,
                    'duplicate_rows' => $duplicateCount,
                    'error_log' => !empty($errors) ? $errors : null
                ]);
            }
        }

        fclose($file);
        
        $job->update([
            'status' => 'Completed',
            'processed_rows' => $successCount + $failedCount,
            'failed_rows' => $failedCount,
            'duplicate_rows' => $duplicateCount,
            'error_log' => !empty($errors) ? $errors : null
        ]);
    }

    public function exportHistory()
    {
        $imports = ImportJob::all();
        
        $csvHeader = ['ID', 'Filename', 'Type', 'Status', 'Total Rows', 'Processed Rows', 'Failed Rows', 'Created At'];
        
        $callback = function() use ($imports, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);

            foreach ($imports as $import) {
                fputcsv($file, [
                    $import->id,
                    $import->filename,
                    $import->type,
                    $import->status,
                    $import->total_rows,
                    $import->processed_rows,
                    $import->failed_rows,
                    $import->created_at,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=import_history.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }
}
