<x-app-layout pageTitle="Edit Campaign">
    <div class="max-w-4xl mx-auto">
        <form action="#" class="space-y-8">
            <!-- Basic Information -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-800">Edit Details</h3>
                    <p class="text-xs text-slate-500">Update the core identity of your email campaign.</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase">Campaign Name</label>
                            <input type="text" value="{{ $campaign['name'] }}" class="block w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase">Email Subject</label>
                            <input type="text" value="{{ $campaign['subject'] }}" class="block w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuration -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-800">Targeting & Design</h3>
                    <p class="text-xs text-slate-500">Update audience or template selection.</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase">Select Audience</label>
                            <select class="block w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option @if($campaign['audience_id'] == 12) selected @endif>Active Customers (12,402)</option>
                                <option>New Signups (1,200)</option>
                                <option>Inactive Users (5,400)</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 uppercase">Select Template</label>
                            <select class="block w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option @if($campaign['template_id'] == 5) selected @endif>Flash Sale Template</option>
                                <option>Newsletter Template</option>
                                <option>Product Update Template</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end space-x-4 pt-4">
                <a href="{{ route('campaigns.index') }}" class="px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-700">Cancel</a>
                <button type="submit" class="px-8 py-3 bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all">
                    Update Campaign
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
