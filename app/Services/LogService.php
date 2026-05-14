<?php

namespace App\Services;

use App\Models\WebhookLog;
use Illuminate\Support\Facades\DB;

class LogService
{
    public function getLogs($filters = [], $perPage = 20)
    {
        $query = WebhookLog::query();

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('message_id', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('event_type', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['severity']) && $filters['severity'] !== 'All Severities') {
            if ($filters['severity'] === 'Error') {
                $query->where(function($q) {
                    $q->where('event_type', 'like', '%bounce%')
                      ->orWhere('event_type', 'like', '%fail%');
                });
            } else if ($filters['severity'] === 'Info') {
                $query->where('event_type', 'not like', '%bounce%')
                      ->where('event_type', 'not like', '%fail%');
            }
        }

        return $query->latest()->paginate($perPage)->through(function($log) {
            return [
                'id' => $log->id,
                'level' => str_contains($log->event_type, 'bounce') || str_contains($log->event_type, 'fail') ? 'ERROR' : 'INFO',
                'module' => 'WEBHOOK',
                'message' => 'Processed ' . $log->event_type . ' event',
                'time' => $log->created_at->diffForHumans(),
                'request_id' => $log->message_id,
            ];
        });
    }

    public function getLogMetrics()
    {
        $total = WebhookLog::count();
        $errors = WebhookLog::where('event_type', 'like', '%bounce%')->orWhere('event_type', 'like', '%fail%')->count();
        $health = $total > 0 ? round((($total - $errors) / $total) * 100, 1) . '%' : '100%';

        return [
            'total' => $total,
            'errors' => $errors,
            'warnings' => WebhookLog::where('event_type', 'like', '%delay%')->count(),
            'webhook_logs' => $total,
            'queue_jobs' => DB::table('jobs')->count() + DB::table('failed_jobs')->count(),
            'health' => $health
        ];
    }

    public function getLogDetails($id)
    {
        $log = WebhookLog::findOrFail($id);
        
        return [
            'id' => $log->id,
            'level' => str_contains($log->event_type, 'bounce') || str_contains($log->event_type, 'fail') ? 'ERROR' : 'INFO',
            'module' => 'WEBHOOK',
            'message' => 'Processed ' . $log->event_type . ' event',
            'timestamp' => $log->created_at->toDateTimeString(),
            'request_id' => $log->message_id,
            'context' => [
                'event_type' => $log->event_type,
                'provider' => 'Amazon SES',
                'environment' => 'Production',
                'server_node' => 'worker-node-' . str_pad($log->id % 10, 2, '0', STR_PAD_LEFT)
            ],
            'stack_trace' => 'N/A',
            'payload' => json_encode($log->payload, JSON_PRETTY_PRINT),
        ];
    }
}
