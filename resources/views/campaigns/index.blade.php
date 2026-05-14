<x-app-layout pageTitle="Campaigns">
    <!-- Header Actions -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-sm text-slate-500">Manage and monitor your email marketing efforts.</p>
        </div>
        <a href="{{ route('campaigns.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Create Campaign
        </a>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-kpi-card label="Total Campaigns" value="{{ number_format($kpis['total_campaigns']) }}" trend="+{{ $kpis['total_campaigns'] > 0 ? 1 : 0 }}" :trendUp="true" />
        <x-kpi-card label="Emails Sent" value="{{ number_format($kpis['total_sent']) }}" trend="Live" :trendUp="true" />
        <x-kpi-card label="Active Processing" value="{{ $kpis['active_batches'] }}" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>' />
    </div>

    <!-- Filters & Search -->
    <form action="{{ route('campaigns.index') }}" method="GET" class="bg-white p-4 border border-slate-200 rounded-2xl mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search campaigns..." class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
        </div>
        <div class="flex items-center space-x-2">
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-slate-200 rounded-xl text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="All Statuses">All Statuses</option>
                <option value="Sent" {{ request('status') === 'Sent' ? 'selected' : '' }}>Sent</option>
                <option value="Processing" {{ request('status') === 'Processing' ? 'selected' : '' }}>Processing</option>
                <option value="Scheduled" {{ request('status') === 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-50 text-slate-600 text-xs font-bold rounded-xl border border-slate-200 hover:bg-slate-100">Apply</button>
            <a href="{{ route('campaigns.index') }}" class="p-2 text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </a>
        </div>
    </form>

    <!-- Campaigns Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="px-6 py-4">Campaign Details</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Performance</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($campaigns as $campaign)
                <tr class="hover:bg-slate-50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $campaign->name }}</span>
                            <span class="text-[11px] text-slate-500">Created on {{ $campaign->created_at->format('M d, Y') }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColor = match($campaign->status) {
                                'Sent' => 'bg-emerald-100 text-emerald-800',
                                'Processing' => 'bg-indigo-100 text-indigo-800',
                                'Scheduled' => 'bg-amber-100 text-amber-800',
                                'Failed' => 'bg-rose-100 text-rose-800',
                                default => 'bg-slate-100 text-slate-800'
                            };
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $statusColor }}">
                            {{ $campaign->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center space-x-6">
                            <div class="text-center">
                                <p class="text-xs font-bold text-slate-900">{{ number_format($campaign->sent_count) }}</p>
                                <p class="text-[10px] text-slate-500 uppercase">Sent</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs font-bold text-slate-900">
                                    {{ $campaign->sent_count > 0 ? round((($campaign->stats->open_count ?? 0) / $campaign->sent_count) * 100, 1) : 0 }}%
                                </p>
                                <p class="text-[10px] text-slate-500 uppercase">Open</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('campaigns.show', $campaign->id) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Pagination -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
            {{ $campaigns->appends(request()->query())->links() }}
        </div>
    </div>
</x-app-layout>
