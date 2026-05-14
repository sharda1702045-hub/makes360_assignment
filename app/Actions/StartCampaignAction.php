<?php

namespace App\Actions;

use App\Models\Campaign;
use App\Models\Contact;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class StartCampaignAction
{
    /**
     * Execute the action.
     */
    public function execute(Campaign $campaign): void
    {
        if ($campaign->status !== 'draft' && $campaign->status !== 'scheduled') {
            throw new \Exception("Campaign is already in progress or completed.");
        }

        $campaign->update([
            'status' => 'processing',
            'total_recipients' => $campaign->total_recipients ?: $this->getRecipientCount($campaign),
        ]);

        // We use Job Batching to track progress and handle completion.
        $batch = Bus::batch([])
            ->then(function ($batch) use ($campaign) {
                $campaign->update(['status' => 'completed']);
                Log::info("Campaign {$campaign->id} completed successfully.");
            })
            ->catch(function ($batch, $e) use ($campaign) {
                $campaign->update(['status' => 'failed']);
                Log::error("Campaign {$campaign->id} failed: " . $e->getMessage());
            })
            ->finally(function ($batch) use ($campaign) {
                // Any final cleanup
            })
            ->name("Campaign Dispatch: " . $campaign->name)
            ->dispatch();

        $campaign->update(['batch_id' => $batch->id]);

        // Chunking the contacts to avoid memory issues and dispatching jobs to the batch.
        // In a real app, we might query the contact_list_mapping pivot.
        Contact::whereHas('lists', function ($query) use ($campaign) {
            $query->where('contact_list_id', $campaign->contact_list_id); // Assuming campaign has list_id
        })->chunk(500, function ($contacts) use ($batch, $campaign) {
            $jobs = [];
            foreach ($contacts as $contact) {
                $jobs[] = new SendEmailJob($campaign, $contact);
            }
            $batch->add($jobs);
        });
    }

    protected function getRecipientCount(Campaign $campaign): int
    {
        return Contact::whereHas('lists', function ($query) use ($campaign) {
            $query->where('contact_list_id', $campaign->contact_list_id);
        })->count();
    }
}
