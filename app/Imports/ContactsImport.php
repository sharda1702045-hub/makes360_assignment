<?php

namespace App\Imports;

use App\Models\Contact;
use App\Models\ImportJob;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactsImport implements ToModel, WithChunkReading, WithBatchInserts, WithHeadingRow, ShouldQueue, WithEvents
{
    use RemembersRowNumber;

    public function __construct(protected ImportJob $importJob)
    {
        $this->importJob->update(['status' => 'processing', 'started_at' => now()]);
    }

    public function model(array $row)
    {
        // Simple duplicate detection (in-process)
        // For production 1M+, use upsert instead of individual model returns
        
        if (empty($row['email']) || !filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            $this->logError("Invalid email at row {$this->getRowNumber()}");
            $this->importJob->increment('failed_rows');
            return null;
        }

        $this->importJob->increment('processed_rows');

        return new Contact([
            'user_id' => $this->importJob->user_id,
            'email' => $row['email'],
            'first_name' => $row['first_name'] ?? null,
            'last_name' => $row['last_name'] ?? null,
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                $this->importJob->update([
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
            },
            ImportFailed::class => function (ImportFailed $event) {
                $this->importJob->update([
                    'status' => 'failed',
                    'completed_at' => now()
                ]);
            },
        ];
    }

    protected function logError($message)
    {
        $logs = $this->importJob->error_log ?? [];
        $logs[] = $message;
        $this->importJob->update(['error_log' => $logs]);
    }
}
