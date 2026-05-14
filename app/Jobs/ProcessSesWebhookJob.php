<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\WebhookLog;
use App\Services\SESWebhookService;

class ProcessSesWebhookJob implements ShouldQueue
{
    use Queueable;

    public function __construct(protected WebhookLog $log) {}

    public function handle(SESWebhookService $service): void
    {
        $payload = $this->log->payload;
        
        // SNS sends the actual SES event inside the 'Message' field as a JSON string
        $message = json_decode($payload['Message'], true);
        
        if (!$message) {
            return;
        }

        $eventType = $message['eventType'] ?? null;

        match ($eventType) {
            'Bounce' => $service->handleBounce($message),
            'Complaint' => $service->handleComplaint($message),
            'Delivery' => $service->handleEngagement($message, 'delivery'),
            'Open' => $service->handleEngagement($message, 'open'),
            'Click' => $service->handleEngagement($message, 'click'),
            default => logger("Unhandled SES event type: {$eventType}"),
        };

        $this->log->update(['processed_at' => now()]);
    }
}
