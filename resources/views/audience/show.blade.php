<x-app-layout pageTitle="Contact Profile">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8 text-center">
                <div class="h-24 w-24 bg-indigo-50 rounded-full mx-auto flex items-center justify-center text-indigo-600 text-3xl font-bold mb-4">
                    {{ substr($contact->first_name, 0, 1) }}{{ substr($contact->last_name, 0, 1) }}
                </div>
                <h2 class="text-xl font-bold text-slate-900">{{ $contact->first_name }} {{ $contact->last_name }}</h2>
                <p class="text-sm text-slate-500 mb-6">{{ $contact->email }}</p>
                
                <div class="flex items-center justify-center space-x-2">
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full uppercase">
                        {{ $contact->status }}
                    </span>
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-[10px] font-bold rounded-full uppercase">
                        {{ $contact->engagement }} Engagement
                    </span>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-slate-800 mb-4 text-sm">System Properties</h3>
                <div class="space-y-4 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Unique ID</span>
                        <span class="font-mono text-slate-700">#CTR-{{ str_pad($contact->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Created At</span>
                        <span class="text-slate-700">{{ $contact->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Last Updated</span>
                        <span class="text-slate-700">{{ $contact->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Custom Attributes -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-slate-800 mb-4 text-sm">Extended Metadata</h3>
                <div class="space-y-3">
                    @forelse($contact->attributes ?? [] as $key => $value)
                        <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-tighter">{{ str_replace('_', ' ', $key) }}</span>
                            <span class="text-xs text-slate-700 font-medium">{{ is_array($value) ? json_encode($value) : $value }}</span>
                        </div>
                    @empty
                        <p class="text-[10px] text-slate-400 italic text-center py-4">No additional metadata attributes found for this contact.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-slate-800 mb-4 text-sm">Manage Audience</h3>
                <div class="space-y-4" x-data="contactActions({{ $contact->id }})">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Assign to List</label>
                        <select x-model="selectedListId" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">Select a list...</option>
                            @foreach($all_lists as $list)
                                <option value="{{ $list->id }}">{{ $list->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button 
                        @click="addToList()" 
                        :disabled="!selectedListId || isSubmitting"
                        class="w-full py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 disabled:opacity-50 transition-all"
                    >
                        <span x-show="!isSubmitting">Add to List</span>
                        <span x-show="isSubmitting">Adding...</span>
                    </button>
                    
                    <button class="w-full py-2.5 bg-rose-50 text-rose-600 border border-rose-100 rounded-xl text-xs font-bold hover:bg-rose-100 transition-colors mt-2">
                        Suppress Contact
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Timeline -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Activity Feed -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800">Engagement Timeline</h3>
                    <div class="flex space-x-2">
                        <button class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-lg">All Activity</button>
                        <button class="text-xs font-bold text-slate-500 hover:bg-slate-50 px-3 py-1 rounded-lg">Emails Only</button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="relative space-y-8 before:absolute before:inset-0 before:ml-4 before:-translate-x-px before:h-full before:w-0.5 before:bg-slate-100">
                        @forelse($contact->activity as $activity)
                        <div class="relative flex items-center justify-between group">
                            <div class="flex items-center">
                                <div class="absolute left-0 h-8 w-8 bg-white border-2 border-slate-100 rounded-full flex items-center justify-center transition-colors group-hover:border-indigo-400">
                                    <div class="h-2 w-2 rounded-full {{ $activity['type'] === 'open' ? 'bg-indigo-500' : ($activity['type'] === 'click' ? 'bg-emerald-500' : 'bg-slate-300') }}"></div>
                                </div>
                                <div class="ml-12">
                                    <p class="text-sm font-medium text-slate-800">{{ $activity['event'] }}</p>
                                    <p class="text-[10px] text-slate-400 uppercase">{{ $activity['time'] }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="py-4 text-center text-slate-400 text-xs italic">No email activity recorded for this contact yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-slate-800 mb-4">Audience Memberships</h3>
                <div class="flex flex-wrap gap-2">
                    @forelse($contact->lists as $list)
                        <a href="{{ route('contact-lists.show', $list->id) }}" class="px-3 py-1 bg-indigo-50 text-indigo-600 text-xs font-medium rounded-lg hover:bg-indigo-100 transition-colors">{{ $list->name }}</a>
                    @empty
                        <span class="text-xs text-slate-400 italic">No list memberships</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        function contactActions(contactId) {
            return {
                selectedListId: '',
                isSubmitting: false,
                
                async addToList() {
                    this.isSubmitting = true;
                    try {
                        const response = await fetch(`/contact-lists/${this.selectedListId}/attach`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ contact_id: contactId })
                        });
                        
                        const result = await response.json();
                        if (result.success) {
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Error adding to list:', error);
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
