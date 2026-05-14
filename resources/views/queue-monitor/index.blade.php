<x-app-layout pageTitle="Infrastructure Monitoring">
    <div x-data="queueMonitor()" x-init="init()">
        <!-- Status Bar -->
        <div class="flex items-center justify-between mb-8 bg-slate-900 p-4 rounded-2xl border border-slate-800 shadow-2xl">
            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-2">
                    <div class="h-3 w-3 rounded-full" :class="data.overview.horizon_status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500'"></div>
                    <span class="text-xs font-bold text-white uppercase tracking-widest">Horizon: <span x-text="data.overview.horizon_status"></span></span>
                </div>
                <div class="h-8 w-px bg-slate-800"></div>
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Redis Memory: <span class="text-white" x-text="data.overview.redis_memory"></span></span>
                </div>
            </div>
            <div class="text-xs font-mono text-slate-500">
                Last Refresh: <span x-text="lastRefresh"></span>
            </div>
        </div>

        <!-- KPI Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 border border-slate-200 rounded-2xl shadow-sm">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pending Jobs</p>
                <h3 class="text-3xl font-black text-slate-900" x-text="data.overview.pending"></h3>
            </div>
            <div class="bg-white p-6 border border-slate-200 rounded-2xl shadow-sm">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Processing</p>
                <h3 class="text-3xl font-black text-indigo-600" x-text="data.overview.processing"></h3>
            </div>
            <div class="bg-white p-6 border border-slate-200 rounded-2xl shadow-sm">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Failed (24h)</p>
                <h3 class="text-3xl font-black text-rose-600" x-text="data.overview.failed"></h3>
            </div>
            <div class="bg-white p-6 border border-slate-200 rounded-2xl shadow-sm">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Queue Latency</p>
                <h3 class="text-3xl font-black text-slate-900"><span x-text="data.overview.latency"></span>ms</h3>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-slate-800">Job Throughput</h3>
                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-lg">LIVE</span>
                </div>
                <div id="throughputChart" class="min-h-[300px]"></div>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-slate-800 mb-6">Queue Distribution</h3>
                <div id="distributionChart" class="min-h-[300px]"></div>
            </div>
        </div>

        <!-- Workers & Failed Jobs -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Active Workers -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800">Active Workers</h3>
                    <span class="text-xs font-bold text-slate-500" x-text="data.overview.workers + ' total nodes'"></span>
                </div>
                <div class="divide-y divide-slate-100">
                    <template x-for="worker in data.workers" :key="worker.id">
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <div class="flex items-center">
                                <div class="h-8 w-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-bold text-slate-900" x-text="worker.id"></p>
                                    <p class="text-[10px] text-slate-500 uppercase tracking-tighter" x-text="'Uptime: ' + worker.uptime"></p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-6">
                                <div class="text-right">
                                    <p class="text-xs font-bold text-slate-900" x-text="worker.jobs"></p>
                                    <p class="text-[10px] text-slate-500 uppercase">Jobs</p>
                                </div>
                                <div class="h-8 w-px bg-slate-100"></div>
                                <div class="flex items-center space-x-2">
                                    <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                                    <span class="text-[10px] font-bold text-slate-700 uppercase" x-text="worker.status"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Failed Jobs -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800">Recent Failures</h3>
                    <button class="text-[10px] font-bold text-rose-600 hover:text-rose-800 uppercase tracking-widest">Retry All</button>
                </div>
                <div class="divide-y divide-slate-100 text-sm">
                    <template x-for="job in data.failed_jobs" :key="job.id">
                        <div class="p-4 hover:bg-rose-50 transition-colors group">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-slate-900" x-text="job.job"></span>
                                <span class="text-[10px] text-slate-400 font-medium" x-text="job.failed_at"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <code class="text-[10px] text-rose-600 bg-rose-50 px-2 py-0.5 rounded" x-text="job.exception"></code>
                                <button class="opacity-0 group-hover:opacity-100 text-[10px] font-bold text-indigo-600 uppercase">Retry</button>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="p-4 bg-slate-50 text-center">
                    <a href="#" class="text-xs font-bold text-slate-500 hover:text-slate-700">View Full Failed Jobs Audit</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function queueMonitor() {
            return {
                data: @json($stats),
                lastRefresh: new Date().toLocaleTimeString(),
                throughputChart: null,
                distributionChart: null,

                init() {
                    this.initCharts();
                    setInterval(() => this.fetchMetrics(), 5000);
                },

                async fetchMetrics() {
                    try {
                        const response = await fetch('{{ route('queue-monitor.metrics') }}');
                        this.data = await response.json();
                        this.lastRefresh = new Date().toLocaleTimeString();
                        this.updateCharts();
                    } catch (e) {
                        console.error('Failed to fetch metrics');
                    }
                },

                initCharts() {
                    this.throughputChart = new ApexCharts(document.querySelector("#throughputChart"), {
                        series: [{
                            name: 'Throughput',
                            data: this.data.throughput_history.map(h => h.value)
                        }],
                        chart: { height: 300, type: 'line', animations: { enabled: true }, toolbar: { show: false } },
                        colors: ['#4f46e5'],
                        stroke: { curve: 'smooth', width: 4 },
                        xaxis: { categories: this.data.throughput_history.map(h => h.time) },
                        grid: { borderColor: '#f1f5f9' }
                    });
                    this.throughputChart.render();

                    this.distributionChart = new ApexCharts(document.querySelector("#distributionChart"), {
                        series: this.data.queues.map(q => q.jobs),
                        chart: { type: 'donut', height: 300 },
                        labels: this.data.queues.map(q => q.name),
                        colors: ['#4f46e5', '#6366f1', '#f43f5e'],
                        legend: { position: 'bottom' },
                        dataLabels: { enabled: false },
                        plotOptions: { pie: { donut: { size: '75%' } } }
                    });
                    this.distributionChart.render();
                },

                updateCharts() {
                    this.throughputChart.updateSeries([{
                        data: this.data.throughput_history.map(h => h.value)
                    }]);
                    this.throughputChart.updateOptions({
                        xaxis: { categories: this.data.throughput_history.map(h => h.time) }
                    });

                    this.distributionChart.updateSeries(this.data.queues.map(q => q.jobs));
                }
            }
        }
    </script>
</x-app-layout>
