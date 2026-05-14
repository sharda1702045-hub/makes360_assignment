<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignStat;
use Illuminate\Support\Facades\DB;

class CampaignService
{
    public function getAllCampaigns($filters = [], $perPage = 15)
    {
        $query = Campaign::with('stats')->latest();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['status']) && $filters['status'] !== 'All Statuses') {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function getGlobalKPIs()
    {
        return [
            'total_campaigns' => Campaign::count(),
            'total_sent' => Campaign::sum('sent_count'),
            'active_batches' => Campaign::where('status', 'Processing')->count(),
        ];
    }

    public function getCampaignDetails($id)
    {
        return Campaign::with(['stats', 'template'])->findOrFail($id);
    }

    public function getPerformanceMetrics($id)
    {
        $campaign = Campaign::with(['stats', 'template', 'contactList'])->findOrFail($id);
        $stats = $campaign->stats;
        
        $sent = $stats->sent_count ?? 0;
        $delivered = $stats->delivered_count ?? 0;

        return [
            'id' => $campaign->id,
            'name' => $campaign->name,
            'subject' => $campaign->subject,
            'status' => $campaign->status,
            'sent' => $sent,
            'delivered' => $delivered,
            'opened' => $stats->open_count ?? 0,
            'clicked' => $stats->click_count ?? 0,
            'bounced' => $stats->bounce_count ?? 0,
            'complained' => $stats->complaint_count ?? 0,
            'audience' => $campaign->contactList->name ?? 'N/A',
            'template' => $campaign->template->name ?? 'N/A',
        ];
    }

    public function getFormData()
    {
        return [
            'templates' => \App\Models\Template::all(),
            'contact_lists' => \App\Models\ContactList::all(),
        ];
    }

    public function createCampaign(array $data)
    {
        $campaign = Campaign::create([
            'user_id' => auth()->id() ?? 1,
            'template_id' => $data['template_id'],
            'contact_list_id' => $data['contact_list_id'],
            'name' => $data['name'],
            'subject' => $data['subject'],
            'status' => $data['schedule'] === 'later' ? 'Scheduled' : 'Draft',
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'sent_count' => 0
        ]);

        if ($data['schedule'] === 'immediate') {
            \App\Jobs\ProcessCampaignJob::dispatch($campaign);
        }

        return $campaign;
    }
}
