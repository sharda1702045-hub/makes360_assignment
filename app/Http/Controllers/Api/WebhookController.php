<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebhookLog;
use App\Jobs\ProcessSesWebhookJob;
use Illuminate\Http\JsonResponse;

class WebhookController extends Controller
{
    /**
     * Handle incoming SES webhooks via SNS.
     */
    public function handleSes(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!$payload) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        // 1. Log the raw webhook for auditing
        $log = WebhookLog::create([
            'provider' => 'ses',
            'payload' => $payload,
        ]);

        // 2. Handle SNS Subscription Confirmation
        if (($payload['Type'] ?? '') === 'SubscriptionConfirmation') {
            // In a real app, use AWS SDK or verify URL before visiting
            file_get_contents($payload['SubscribeURL']);
            return response()->json(['message' => 'Subscription confirmed']);
        }

        // 3. Dispatch for async processing
        ProcessSesWebhookJob::dispatch($log)->onQueue('webhooks');

        return response()->json(['message' => 'Event received and queued']);
    }
}
