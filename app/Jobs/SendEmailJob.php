<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\CampaignEmail;
use App\Services\MailService;
use Illuminate\Bus\Batchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\RateLimited;

class SendEmailJob implements ShouldQueue
{
    use Batchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public function __construct(
        public Campaign $campaign,
        public Contact $contact
    ) {}

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        return [new RateLimited('emails')];
    }

    public function handle(MailService $mailService): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        try {
            $messageId = $mailService->sendCampaignEmail($this->campaign, $this->contact);

            CampaignEmail::create([
                'campaign_id' => $this->campaign->id,
                'contact_id' => $this->contact->id,
                'status' => 'sent',
                'message_id' => $messageId,
            ]);

            $this->campaign->increment('sent_count');
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }

    public function failed(\Throwable $exception): void
    {
        CampaignEmail::create([
            'campaign_id' => $this->campaign->id,
            'contact_id' => $this->contact->id,
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);

        $this->campaign->increment('failed_count');
    }
}
