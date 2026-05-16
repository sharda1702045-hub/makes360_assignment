<x-app-layout pageTitle="Audience Management">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-kpi-card label="Total Contacts" value="{{ number_format($kpis['total_contacts']) }}" trend="{{ $kpis['total_contacts'] > 0 ? '+1' : '0' }}" :trendUp="true" />
        <x-kpi-card label="Active Subscribers" value="{{ number_format($kpis['active_subscribers']) }}" />
        <x-kpi-card label="Suppressed" value="{{ number_format($kpis['suppressed']) }}" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>' />
        <x-kpi-card label="Unsubscribed" value="{{ number_format($kpis['unsubscribed']) }}" trend="0.05%" :trendUp="false" />
    </div>

    <!-- Tabs & Search -->
    <div class="mb-8" x-data="audienceManager()">
        <div class="flex items-center justify-between mb-6 gap-4">
            <div class="flex p-1 bg-slate-100 rounded-xl">
                <button 
                    @click="activeTab = 'contacts'" 
                    :class="activeTab === 'contacts' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                    class="px-6 py-2 text-sm font-bold rounded-lg transition-all"
                >
                    All Contacts
                </button>
                <button 
                    @click="activeTab = 'lists'" 
                    :class="activeTab === 'lists' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                    class="px-6 py-2 text-sm font-bold rounded-lg transition-all"
                >
                    Audience Lists
                </button>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('imports.index') }}" class="px-4 py-2.5 bg-slate-50 text-slate-600 text-sm font-bold rounded-xl border border-slate-200 hover:bg-slate-100 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Import
                </a>
                <button @click="showContactModal = true" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Contact
                </button>
                <button class="p-2.5 text-slate-500 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                </button>
            </div>
        </div>

        <!-- Contacts Tab -->
        <div x-show="activeTab === 'contacts'">
            <form action="{{ route('audience.index') }}" method="GET" class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <div class="relative flex-1 max-w-md">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by email or name..." class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" class="px-4 py-2.5 bg-slate-50 text-slate-600 text-sm font-bold rounded-xl border border-slate-200 hover:bg-slate-100 transition-colors">Apply Filters</button>
            </form>

    <!-- Contacts Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="px-6 py-4">Contact</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Engagement</th>
                    <th class="px-6 py-4">Added On</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($contacts as $contact)
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
                        <a href="{{ route('audience.show', $contact->id) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-tighter">View / Assign Group</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
            {{ $contacts->appends(request()->query())->links() }}
        </div>
    </div>
        </div>

        <!-- Lists Tab -->
        <div x-show="activeTab === 'lists'" style="display: none;">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($contact_lists as $list)
                <div class="bg-white border border-slate-200 rounded-2xl p-6 hover:shadow-lg transition-all group relative">
                    <div class="flex items-start justify-between mb-4">
                        <div class="h-12 w-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <button @click.stop="deleteList({{ $list->id }})" class="text-slate-400 hover:text-rose-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                    <a href="{{ route('contact-lists.show', $list->id) }}" class="block">
                        <h4 class="text-lg font-bold text-slate-900 mb-1 group-hover:text-indigo-600 transition-colors">{{ $list->name }}</h4>
                        <p class="text-sm text-slate-500 mb-6">{{ number_format($list->total_contacts) }} Contacts</p>
                    </a>
                    <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Created {{ $list->created_at->diffForHumans() }}</span>
                        <a href="{{ route('campaigns.create', ['list_id' => $list->id]) }}" class="text-xs font-bold text-indigo-600 hover:indigo-800">Use in Campaign →</a>
                    </div>
                </div>
                @endforeach

                <!-- Create New List Card -->
                <button @click="showCreateModal = true" class="bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl p-6 flex flex-col items-center justify-center hover:border-indigo-300 hover:bg-white transition-all group">
                    <div class="h-12 w-12 rounded-full bg-white shadow-sm flex items-center justify-center text-slate-400 group-hover:text-indigo-600 mb-4 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-slate-500 group-hover:text-indigo-600 transition-colors">Create New List</span>
                </button>
            </div>
        </div>

        <!-- Create Contact Modal -->
        <div 
            x-show="showContactModal" 
            class="fixed inset-0 z-50 overflow-y-auto" 
            style="display: none;"
        >
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showContactModal = false"></div>
                
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 overflow-hidden">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-slate-900">Add New Contact</h3>
                        <p class="text-sm text-slate-500">Manually add a single contact to your audience.</p>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-700 uppercase">First Name</label>
                                <input 
                                    type="text" 
                                    x-model="newContact.first_name" 
                                    placeholder="John" 
                                    class="block w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                                >
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-700 uppercase">Last Name</label>
                                <input 
                                    type="text" 
                                    x-model="newContact.last_name" 
                                    placeholder="Doe" 
                                    class="block w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                                >
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase">Email Address</label>
                            <input 
                                type="email" 
                                x-model="newContact.email" 
                                placeholder="john@example.com" 
                                class="block w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                            >
                        </div>
                        
                        <div class="flex items-center justify-end space-x-3 pt-4">
                            <button @click="showContactModal = false" class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-slate-700">Cancel</button>
                            <button 
                                @click="createContact()" 
                                :disabled="!newContact.email || isSubmitting" 
                                class="px-6 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 disabled:opacity-50 transition-all flex items-center"
                            >
                                <span x-show="!isSubmitting">Add Contact</span>
                                <span x-show="isSubmitting" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Adding...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create List Modal -->
        <div 
            x-show="showCreateModal" 
            class="fixed inset-0 z-50 overflow-y-auto" 
            style="display: none;"
        >
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showCreateModal = false"></div>
                
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 overflow-hidden">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-slate-900">Create Audience List</h3>
                        <p class="text-sm text-slate-500">Give your new audience a descriptive name.</p>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase">List Name</label>
                            <input 
                                type="text" 
                                x-model="newList.name" 
                                placeholder="e.g., Summer Lead Gen 2024" 
                                class="block w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                            >
                        </div>
                        
                        <div class="flex items-center justify-end space-x-3 pt-4">
                            <button @click="showCreateModal = false" class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-slate-700">Cancel</button>
                            <button 
                                @click="createList()" 
                                :disabled="!newList.name || isSubmitting" 
                                class="px-6 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 disabled:opacity-50 transition-all flex items-center"
                            >
                                <span x-show="!isSubmitting">Create List</span>
                                <span x-show="isSubmitting" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Creating...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Notification -->
        <div 
            x-show="notification.show" 
            class="fixed bottom-8 right-8 z-50 bg-emerald-500 text-white px-6 py-3 rounded-2xl shadow-2xl flex items-center space-x-3 border border-emerald-400"
            style="display: none;"
        >
            <span class="text-sm font-bold" x-text="notification.message"></span>
        </div>
    </div>

    <script>
        function audienceManager() {
            return {
                activeTab: 'contacts',
                showCreateModal: false,
                showContactModal: false,
                isSubmitting: false,
                newList: { name: '' },
                newContact: { first_name: '', last_name: '', email: '' },
                notification: { show: false, message: '' },
                
                async createContact() {
                    this.isSubmitting = true;
                    try {
                        const response = await fetch('{{ route('audience.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.newContact)
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            this.showNotification(result.message);
                            this.showContactModal = false;
                            this.newContact = { first_name: '', last_name: '', email: '' };
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            // Basic error handling for duplicates
                            alert(result.message || 'Error creating contact');
                        }
                    } catch (error) {
                        console.error('Error creating contact:', error);
                    } finally {
                        this.isSubmitting = false;
                    }
                },
                
                async createList() {
                    this.isSubmitting = true;
                    try {
                        const response = await fetch('{{ route('contact-lists.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.newList)
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            this.showNotification(result.message);
                            this.showCreateModal = false;
                            this.newList.name = '';
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    } catch (error) {
                        console.error('Error creating list:', error);
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                async deleteList(id) {
                    if (!confirm('Are you sure you want to delete this audience list? Contacts within the list will not be deleted.')) return;
                    
                    try {
                        const response = await fetch(`/contact-lists/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        
                        const result = await response.json();
                        if (result.success) {
                            this.showNotification(result.message);
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    } catch (error) {
                        console.error('Error deleting list:', error);
                    }
                },
                
                showNotification(message) {
                    this.notification = { show: true, message };
                    setTimeout(() => this.notification.show = false, 3000);
                }
            }
        }
    </script>
</x-app-layout>
