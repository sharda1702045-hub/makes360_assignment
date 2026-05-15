<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignStat;
use App\Models\CampaignEmail;
use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProcessCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle(MailService $mailService)
    {
        $campaign = $this->campaign;

        // Update status to Processing
        $campaign->update(['status' => 'Processing']);

        // Ensure stats record exists
        $stats = CampaignStat::firstOrCreate(
            ['campaign_id' => $campaign->id],
            ['sent_count' => 0, 'delivered_count' => 0]
        );

        $contacts = $campaign->contactList->contacts;
        $campaign->update(['total_recipients' => $contacts->count()]);

        foreach ($contacts as $contact) {
            try {
                $messageId = $mailService->sendCampaignEmail($campaign, $contact);

                // Record the specific email entry
                DB::table('campaign_emails')->insert([
                    'campaign_id' => $campaign->id,
                    'contact_id' => $contact->id,
                    'status' => 'sent',
                    'message_id' => $messageId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update counts
                $campaign->increment('sent_count');
                $stats->increment('sent_count');
                $stats->increment('delivered_count'); // Assume delivered for now

            } catch (\Throwable $e) {
                Log::error("Failed to send email to {$contact->email}: " . $e->getMessage());
                $campaign->increment('failed_count');
            }
        }

        // Finalize status
        $campaign->update(['status' => 'Completed']);
    }
}
