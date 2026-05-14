<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Services\AudienceService;

class AudienceController extends Controller
{
    protected $audienceService;

    public function __construct(AudienceService $audienceService)
    {
        $this->audienceService = $audienceService;
    }

    public function index(Request $request)
    {
        $contacts = $this->audienceService->getContacts($request->all());
        $kpis = $this->audienceService->getAudienceKPIs();
        $contact_lists = $this->audienceService->getContactLists();
        return view('audience.index', compact('contacts', 'kpis', 'contact_lists'));
    }

    public function show($id)
    {
        $contact = $this->audienceService->getContactDetails($id);
        $all_lists = \App\Models\ContactList::all();
        return view('audience.show', compact('contact', 'all_lists'));
    }
}
