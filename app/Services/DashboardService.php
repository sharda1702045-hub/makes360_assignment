<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Campaign;
use App\Models\CampaignStat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getKpis()
    {
        return Cache::remember('dashboard_kpis', 300, function () {
            $totalContacts = Contact::count();
            $activeContacts = Contact::where('status', 'active')->count();
            $suppressed = Contact::where('status', 'suppressed')->count();
            
            $stats = CampaignStat::selectRaw('
                SUM(sent_count) as total_sent,
                SUM(open_count) as total_opens,
                SUM(click_count) as total_clicks,
                SUM(bounce_count) as total_bounces
            ')->first();

            $totalSent = $stats->total_sent ?? 0;
            $openRate = $totalSent > 0 ? round(($stats->total_opens / $totalSent) * 100, 2) : 0;
            $bounceRate = $totalSent > 0 ? round(($stats->total_bounces / $totalSent) * 100, 2) : 0;

            return [
                'total_contacts' => $totalContacts,
                'active_contacts' => $activeContacts,
                'suppressed_contacts' => $suppressed,
                'total_campaigns' => Campaign::count(),
                'active_campaigns' => Campaign::where('status', 'processing')->count(),
                'total_sent' => $totalSent,
                'avg_open_rate' => $openRate,
                'bounce_rate' => $bounceRate,
                'failed_jobs' => DB::table('failed_jobs')->count(),
            ];
        });
    }

    public function getRecentCampaigns()
    {
        return Campaign::with('stats')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($campaign) {
                $stats = $campaign->stats;
                return [
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'status' => $campaign->status,
                    'sent' => $stats->sent_count ?? 0,
                    'open_rate' => ($stats && $stats->sent_count > 0)
                        ? round(($stats->open_count / $stats->sent_count) * 100, 1) . '%' 
                        : '0%',
                ];
            });
    }

    public function getQueueHealth()
    {
        return [
            'mailing' => DB::table('jobs')->where('queue', 'default')->count(),
            'imports' => DB::table('jobs')->where('queue', 'imports')->count(),
            'failed' => DB::table('failed_jobs')->count(),
            'throughput' => 84 // Static for now, can be calculated from logs if needed
        ];
    }
}
