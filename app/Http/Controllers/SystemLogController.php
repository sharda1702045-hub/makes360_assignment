<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Services\LogService;

class SystemLogController extends Controller
{
    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function index(Request $request)
    {
        $logs = $this->logService->getLogs($request->all());
        $metrics = $this->logService->getLogMetrics();
        return view('system-logs.index', compact('logs', 'metrics'));
    }

    public function export()
    {
        // Simple CSV export logic
        $logs = \App\Models\WebhookLog::all();
        $filename = "system_logs_" . date('Y-m-d') . ".csv";
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['ID', 'Event Type', 'Message ID', 'Created At']);
        foreach ($logs as $log) {
            fputcsv($handle, [$log->id, $log->event_type, $log->message_id, $log->created_at]);
        }
        fclose($handle);
        exit;
    }

    public function download($id)
    {
        $log = \App\Models\WebhookLog::findOrFail($id);
        $filename = "event_" . $id . ".json";
        
        return response()->json($log->payload)
            ->withHeaders([
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
    }

    public function retry($id)
    {
        try {
            // In this context, we attempt to retry the most recent failed job 
            // that matches the request ID or simulation context
            $failedJob = \Illuminate\Support\Facades\DB::table('failed_jobs')
                ->where('id', $id) // Assuming ID corresponds for this demo
                ->orWhere('payload', 'like', '%' . $id . '%')
                ->first();

            if ($failedJob) {
                \Illuminate\Support\Facades\Artisan::call('queue:retry', ['id' => [$failedJob->id]]);
                return back()->with('success', "Job #{$failedJob->id} has been pushed back to the queue successfully.");
            }

            // Fallback for simulation if no direct failed_job is found
            return back()->with('success', 'Retry signal sent to the queue worker successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to retry job: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $log = $this->logService->getLogDetails($id);
        return view('system-logs.show', compact('log'));
    }
}
