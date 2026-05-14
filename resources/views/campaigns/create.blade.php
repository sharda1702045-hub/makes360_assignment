<x-app-layout pageTitle="Create Campaign">
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('campaigns.store') }}" method="POST" class="space-y-8" x-data="{ schedule: 'immediate' }">
            @csrf
            <!-- Basic Information -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-800">Campaign Details</h3>
                    <p class="text-xs text-slate-500">Provide the core identity of your email campaign.</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase">Campaign Name</label>
                            <input type="text" name="name" required placeholder="e.g., Summer Sale 2024" class="block w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase">Email Subject</label>
                            <input type="text" name="subject" required placeholder="What will they see in their inbox?" class="block w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuration -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-800">Targeting & Design</h3>
                    <p class="text-xs text-slate-500">Select who will receive this and what they will see.</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase">Select Audience</label>
                            <select name="contact_list_id" required class="block w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option value="">-- Choose Audience --</option>
                                @foreach($contact_lists as $list)
                                    <option value="{{ $list->id }}">{{ $list->name }} ({{ number_format($list->total_contacts) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase">Select Template</label>
                            <select name="template_id" required class="block w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option value="">-- Choose Template --</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scheduling -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-800">Schedule</h3>
                    <p class="text-xs text-slate-500">Decide when to blast your message.</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="flex items-center space-x-8">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="radio" name="schedule" value="immediate" x-model="schedule" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-slate-700">Send Immediately</span>
                        </label>
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="radio" name="schedule" value="later" x-model="schedule" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-slate-700">Schedule for Later</span>
                        </label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-show="schedule === 'later'" x-cloak>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase">Dispatch Date & Time</label>
                            <input type="datetime-local" name="scheduled_at" :required="schedule === 'later'" class="block w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end space-x-4 pt-4">
                <a href="{{ route('campaigns.index') }}" class="px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-700">Cancel</a>
                <button type="submit" class="px-8 py-3 bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all">
                    Launch Campaign
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
