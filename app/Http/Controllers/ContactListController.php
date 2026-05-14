<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactList;

class ContactListController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $list = ContactList::create([
            'user_id' => auth()->id() ?? 1,
            'name' => $validated['name'],
            'total_contacts' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Audience list created successfully',
            'list' => $list
        ]);
    }

    public function show($id)
    {
        $list = ContactList::findOrFail($id);
        $audienceService = app(\App\Services\AudienceService::class);
        $contacts = $audienceService->getContactsInList($id);
        
        return view('audience.list-show', compact('list', 'contacts'));
    }

    public function destroy($id)
    {
        $list = ContactList::findOrFail($id);
        $list->delete();

        return response()->json([
            'success' => true,
            'message' => 'Audience list deleted successfully'
        ]);
    }

    public function attachContact(Request $request, $id)
    {
        $request->validate([
            'contact_id' => 'required|exists:contacts,id'
        ]);

        $list = ContactList::findOrFail($id);
        
        $exists = \Illuminate\Support\Facades\DB::table('contact_list_mapping')
            ->where('contact_id', $request->contact_id)
            ->where('contact_list_id', $id)
            ->exists();

        if (!$exists) {
            \Illuminate\Support\Facades\DB::table('contact_list_mapping')->insert([
                'contact_id' => $request->contact_id,
                'contact_list_id' => $id
            ]);
            $list->increment('total_contacts');
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact added to list successfully'
        ]);
    }
}
