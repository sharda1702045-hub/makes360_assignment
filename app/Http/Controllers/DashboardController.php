<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;
    protected $analyticsService;

    public function __construct(DashboardService $dashboardService, AnalyticsService $analyticsService)
    {
        $this->dashboardService = $dashboardService;
        $this->analyticsService = $analyticsService;
    }

    public function index()
    {
        $kpis = $this->dashboardService->getKpis();
        $recentCampaigns = $this->dashboardService->getRecentCampaigns();
        $queueHealth = $this->dashboardService->getQueueHealth();
        $deliveryTrend = $this->analyticsService->getDeliveryTrend();
        $engagementTrend = $this->analyticsService->getEngagementTrend();
        
        return view('dashboard', compact(
            'kpis', 
            'recentCampaigns', 
            'queueHealth', 
            'deliveryTrend', 
            'engagementTrend'
        ));
    }
}
