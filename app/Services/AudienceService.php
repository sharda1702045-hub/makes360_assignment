<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\EmailEvent;
use Illuminate\Support\Facades\DB;

class AudienceService
{
    public function getContacts($filters = [], $perPage = 15)
    {
        $query = Contact::latest();

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('first_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('last_name', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->paginate($perPage);
    }

    public function getAudienceKPIs()
    {
        return [
            'total_contacts' => Contact::count(),
            'active_subscribers' => Contact::where('status', 'active')->count(),
            'suppressed' => Contact::where('status', 'suppressed')->count(),
            'unsubscribed' => Contact::where('status', 'unsubscribed')->count(),
        ];
    }

    public function getContactLists()
    {
        return \App\Models\ContactList::latest()->get();
    }

    public function getContactsInList($listId, $perPage = 15)
    {
        return Contact::whereHas('lists', function($query) use ($listId) {
            $query->where('contact_list_id', $listId);
        })->latest()->paginate($perPage);
    }

    public function getContactDetails($id)
    {
        $contact = Contact::with('lists')->findOrFail($id);
        
        $activity = EmailEvent::join('campaign_emails', 'email_events.message_id', '=', 'campaign_emails.message_id')
            ->where('campaign_emails.contact_id', $id)
            ->select('email_events.*', 'campaign_emails.campaign_id')
            ->latest('email_events.created_at')
            ->take(20)
            ->get()
            ->map(function ($event) {
                return [
                    'event' => ucfirst($event->type ?? 'activity') . ' in campaign #' . $event->campaign_id,
                    'time' => $event->created_at->diffForHumans(),
                    'type' => $event->type ?? 'default'
                ];
            });

        // Add virtual properties for UI
        $score = ($contact->id % 5) + 1;
        $contact->engagement = $score > 3 ? 'High' : ($score > 1 ? 'Medium' : 'Low');
        $contact->activity = $activity;
        
        return $contact;
    }

    public function createContact(array $data)
    {
        return Contact::create([
            'user_id' => $data['user_id'] ?? 1,
            'email' => $data['email'],
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
            'status' => 'active',
        ]);
    }
}
