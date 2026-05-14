<x-app-layout pageTitle="Operational Dashboard">
    <!-- KPI Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-kpi-card label="Total Contacts" value="{{ number_format($kpis['total_contacts']) }}" trend="{{ $kpis['total_contacts'] > 0 ? '+1' : '0' }}" :trendUp="true" />
        <x-kpi-card label="Emails Sent" value="{{ number_format($kpis['total_sent']) }}" trend="{{ $kpis['total_sent'] > 0 ? '+5%' : '0%' }}" :trendUp="true" />
        <x-kpi-card label="Avg. Open Rate" value="{{ $kpis['avg_open_rate'] }}%" trend="1.2%" :trendUp="true" />
        <x-kpi-card label="Active Campaigns" value="{{ $kpis['active_campaigns'] }}" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>' />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <x-chart-card title="Delivery Performance" id="deliveryChart" subtitle="Success vs Bounces over last 30 days" />
        <x-chart-card title="Engagement Trends" id="engagementChart" subtitle="Opens and Clicks activity" />
    </div>

    <!-- Bottom Row: Recent Activity & Queue Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">Recent Campaigns</h3>
                <a href="{{ route('campaigns.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">View All</a>
            </div>
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Campaign Name</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Sent</th>
                        <th class="px-6 py-3">Open Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentCampaigns as $campaign)
                    <tr class="text-sm hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4 font-medium text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $campaign['name'] }}</td>
                        <td class="px-6 py-4">
                            @php
                                $statusColor = match($campaign['status']) {
                                    'completed' => 'bg-emerald-100 text-emerald-800',
                                    'processing' => 'bg-indigo-100 text-indigo-800',
                                    'failed' => 'bg-rose-100 text-rose-800',
                                    'draft' => 'bg-slate-100 text-slate-800',
                                    default => 'bg-slate-100 text-slate-800'
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full {{ $statusColor }} text-[10px] font-bold uppercase">{{ $campaign['status'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ number_format($campaign['sent']) }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $campaign['open_rate'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">No campaigns found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- System Health -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-slate-800 mb-6">Queue Health</h3>
            <div class="space-y-6">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-slate-500">Mailing Queue</span>
                        <span class="text-xs font-bold text-slate-900">{{ number_format($queueHealth['mailing']) }} jobs</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500 transition-all duration-1000" style="width: {{ min($queueHealth['mailing'], 100) }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-slate-500">Import Queue</span>
                        <span class="text-xs font-bold text-slate-900">{{ number_format($queueHealth['imports']) }} jobs</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 transition-all duration-1000" style="width: {{ min($queueHealth['imports'], 100) }}%"></div>
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex items-center space-x-2 text-xs font-medium text-slate-500 mb-4">
                        <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <span>Throughput: {{ $queueHealth['throughput'] }} emails/sec</span>
                    </div>
                    <div class="flex items-center space-x-2 text-xs font-medium text-rose-500 mb-4">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Failed Jobs: {{ $queueHealth['failed'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delivery Chart
            new ApexCharts(document.querySelector("#deliveryChart"), {
                series: [{
                    name: 'Delivered',
                    data: @json($deliveryTrend['values'])
                }],
                chart: { height: 300, type: 'area', toolbar: { show: false }, zoom: { enabled: false } },
                colors: ['#4f46e5'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth' },
                xaxis: { categories: @json($deliveryTrend['labels']) },
            }).render();

            // Engagement Chart
            new ApexCharts(document.querySelector("#engagementChart"), {
                series: [{
                    name: 'Opens',
                    data: @json($engagementTrend['opens'])
                }, {
                    name: 'Clicks',
                    data: @json($engagementTrend['clicks'])
                }],
                chart: { height: 300, type: 'bar', toolbar: { show: false } },
                colors: ['#6366f1', '#10b981'],
                plotOptions: { bar: { columnWidth: '45%', borderRadius: 4 } },
                dataLabels: { enabled: false },
                xaxis: { categories: @json($engagementTrend['labels']) },
            }).render();
        });
    </script>
</x-app-layout>
