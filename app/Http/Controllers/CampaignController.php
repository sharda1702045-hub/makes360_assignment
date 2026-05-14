<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\CampaignService;

class CampaignController extends Controller
{
    protected $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    public function index(Request $request)
    {
        $campaigns = $this->campaignService->getAllCampaigns($request->all());
        $kpis = $this->campaignService->getGlobalKPIs();
        return view('campaigns.index', compact('campaigns', 'kpis'));
    }

    public function create()
    {
        $formData = $this->campaignService->getFormData();
        return view('campaigns.create', $formData);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'template_id' => 'required|exists:templates,id',
            'contact_list_id' => 'required|exists:contact_lists,id',
            'schedule' => 'required|in:immediate,later',
            'scheduled_at' => 'required_if:schedule,later|nullable|date',
        ]);

        $campaign = $this->campaignService->createCampaign($validated);

        return redirect()->route('campaigns.index')->with('success', 'Campaign created successfully.');
    }

    public function show($id)
    {
        $campaign = $this->campaignService->getPerformanceMetrics($id);
        return view('campaigns.show', compact('campaign'));
    }

    public function edit($id)
    {
        $campaign = $this->campaignService->getCampaignDetails($id);
        return view('campaigns.edit', compact('campaign'));
    }
}
