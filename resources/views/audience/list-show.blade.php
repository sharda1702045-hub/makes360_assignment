<x-app-layout pageTitle="Audience List: {{ $list->name }}">
    <div class="flex items-center justify-between mb-8">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3 text-xs font-medium text-slate-500 uppercase tracking-wider">
                    <li class="inline-flex items-center">
                        <a href="{{ route('audience.index') }}" class="hover:text-indigo-600 transition-colors">Audience</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="text-slate-900">Lists</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold text-slate-900">{{ $list->name }}</h2>
            <p class="text-sm text-slate-500 mt-1">Showing all contacts currently assigned to this audience group.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('campaigns.create', ['list_id' => $list->id]) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors flex items-center shadow-lg shadow-indigo-100">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                Launch Campaign
            </a>
        </div>
    </div>

    <!-- Contacts Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ number_format($list->total_contacts) }} Members Found</h3>
        </div>
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="px-6 py-4">Contact</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Engagement</th>
                    <th class="px-6 py-4">Added On</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($contacts as $contact)
                <tr class="hover:bg-slate-50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold text-xs">
                                {{ substr($contact->first_name, 0, 1) }}{{ substr($contact->last_name, 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $contact->first_name }} {{ $contact->last_name }}</p>
                                <p class="text-xs text-slate-500">{{ $contact->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColor = match($contact->status) {
                                'active' => 'bg-emerald-100 text-emerald-800',
                                'unsubscribed' => 'bg-amber-100 text-amber-800',
                                'suppressed' => 'bg-rose-100 text-rose-800',
                                default => 'bg-slate-100 text-slate-800'
                            };
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $statusColor }}">
                            {{ $contact->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex -space-x-1">
                                @php $score = ($contact->id % 5) + 1; @endphp
                                @for($i = 0; $i < 5; $i++)
                                    <div class="h-1.5 w-1.5 rounded-full {{ $i < $score ? 'bg-indigo-500' : 'bg-slate-200' }}"></div>
                                @endfor
                            </div>
                            <span class="ml-2 text-xs text-slate-600">{{ $score > 3 ? 'High' : ($score > 1 ? 'Medium' : 'Low') }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-500">
                        {{ $contact->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('audience.show', $contact->id) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-tighter">Profile →</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <p class="text-slate-500 font-medium">No contacts in this list yet.</p>
                            <a href="{{ route('imports.index', ['list_id' => $list->id]) }}" class="text-sm font-bold text-indigo-600 mt-2 hover:underline">Import contacts to this list</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($contacts->hasPages())
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
            {{ $contacts->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
