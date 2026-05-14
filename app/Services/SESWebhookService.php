<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\SuppressionList;
use App\Models\BounceLog;
use App\Models\EmailEvent;
use Illuminate\Support\Facades\DB;

class SESWebhookService
{
    /**
     * Handle a bounce event.
     */
    public function handleBounce(array $data): void
    {
        $bounce = $data['bounce'];
        
        DB::transaction(function () use ($bounce) {
            foreach ($bounce['bouncedRecipients'] as $recipient) {
                $email = $recipient['emailAddress'];
                
                // 1. Log the bounce
                BounceLog::create([
                    'email' => $email,
                    'type' => $bounce['bounceType'], // Permanent or Transient
                    'subtype' => $bounce['bounceSubType'],
                    'reason' => $recipient['diagnosticCode'] ?? null
                ]);

                // 2. Suppress if Hard Bounce (Permanent)
                if ($bounce['bounceType'] === 'Permanent') {
                    $this->suppress($email, 'bounce');
                }
            }
        });
    }

    /**
     * Handle a complaint event.
     */
    public function handleComplaint(array $data): void
    {
        $complaint = $data['complaint'];
        
        DB::transaction(function () use ($complaint) {
            foreach ($complaint['complainedRecipients'] as $recipient) {
                $this->suppress($recipient['emailAddress'], 'complaint');
            }
        });
    }

    /**
     * Handle a delivery or engagement event.
     */
    public function handleEngagement(array $data, string $type): void
    {
        EmailEvent::create([
            'message_id' => $data['mail']['messageId'],
            'type' => $type,
            'metadata' => $data,
            'occurred_at' => now(),
        ]);
    }

    /**
     * Add email to suppression list and deactivate contact.
     */
    protected function suppress(string $email, string $reason): void
    {
        SuppressionList::updateOrCreate(
            ['email' => $email],
            [
                'reason' => $reason,
                'occurred_at' => now()
            ]
        );

        Contact::where('email', $email)->update(['status' => 'inactive']);
    }
}
