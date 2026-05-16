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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email',
        ]);

        $validated['user_id'] = auth()->id() ?? 1;
        $contact = $this->audienceService->createContact($validated);

        return response()->json([
            'success' => true,
            'message' => 'Contact created successfully',
            'contact' => $contact
        ]);
    }
}
