<x-app-layout pageTitle="System Observability">
    <div class="bg-slate-900 -m-8 p-8 min-h-screen">
        <!-- Observability Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-white">Log Explorer</h2>
                <p class="text-sm text-slate-400">Audit trail and system events across all modules.</p>
            </div>
            <div class="flex items-center space-x-3">
                <div class="flex items-center px-4 py-2 bg-slate-800 rounded-xl border border-slate-700">
                    <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse mr-2"></div>
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-widest">Live Feed</span>
                </div>
                <a href="{{ route('system-logs.export') }}" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition-colors">
                    Export Logs
                </a>
            </div>
        </div>

        <!-- Metric Bar -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-slate-800 p-4 rounded-xl border border-slate-700">
                <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Total Events</p>
                <h4 class="text-xl font-bold text-white">{{ number_format($metrics['total']) }}</h4>
            </div>
            <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 border-l-rose-500 border-l-4">
                <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Errors (Total)</p>
                <h4 class="text-xl font-bold text-rose-400">{{ number_format($metrics['errors']) }}</h4>
            </div>
            <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 border-l-amber-500 border-l-4">
                <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Warnings</p>
                <h4 class="text-xl font-bold text-amber-400">{{ $metrics['warnings'] }}</h4>
            </div>
            <div class="bg-slate-800 p-4 rounded-xl border border-slate-700">
                <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Webhook Logs</p>
                <h4 class="text-xl font-bold text-slate-300">{{ number_format($metrics['webhook_logs']) }}</h4>
            </div>
            <div class="bg-slate-800 p-4 rounded-xl border border-slate-700">
                <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Queue Jobs</p>
                <h4 class="text-xl font-bold text-slate-300">{{ number_format($metrics['queue_jobs']) }}</h4>
            </div>
            <div class="bg-slate-800 p-4 rounded-xl border border-slate-700">
                <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">System Health</p>
                <h4 class="text-xl font-bold text-emerald-400">{{ $metrics['health'] }}</h4>
            </div>
        </div>

        <!-- Search & Filter -->
        <form action="{{ route('system-logs.index') }}" method="GET" class="bg-slate-800 p-4 rounded-xl border border-slate-700 mb-6 flex flex-wrap items-center gap-4">
            <div class="relative flex-1 min-w-[300px]">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by message or request ID..." class="block w-full pl-10 pr-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-sm text-slate-300 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>
            <select name="severity" onchange="this.form.submit()" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs font-bold text-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <option value="">All Severities</option>
                <option value="Error" {{ request('severity') == 'Error' ? 'selected' : '' }}>Error</option>
                <option value="Info" {{ request('severity') == 'Info' ? 'selected' : '' }}>Info</option>
            </select>
            <select name="module" onchange="this.form.submit()" class="px-3 py-2 bg-slate-900 border border-slate-900 border border-slate-700 rounded-lg text-xs font-bold text-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <option value="">All Modules</option>
                <option value="Webhook" {{ request('module') == 'Webhook' ? 'selected' : '' }}>Webhook</option>
                <option value="Campaign" {{ request('module') == 'Campaign' ? 'selected' : '' }}>Campaign</option>
            </select>
            <button type="submit" class="hidden">Filter</button>
        </form>

        <!-- Logs Table -->
        <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden shadow-2xl">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-900 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        <th class="px-6 py-4">Timestamp</th>
                        <th class="px-6 py-4">Severity</th>
                        <th class="px-6 py-4">Module</th>
                        <th class="px-6 py-4">Message</th>
                        <th class="px-6 py-4">Request ID</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700 font-mono text-[13px]">
                    @foreach($logs as $log)
                    <tr class="hover:bg-slate-750 transition-colors group">
                        <td class="px-6 py-4 text-slate-500">{{ $log['time'] }}</td>
                        <td class="px-6 py-4">
                            @php
                                $levelColor = match($log['level']) {
                                    'ERROR' => 'text-rose-400 bg-rose-400/10 border-rose-400/20',
                                    'CRITICAL' => 'text-rose-500 bg-rose-500/20 border-rose-500/30',
                                    'WARNING' => 'text-amber-400 bg-amber-400/10 border-amber-400/20',
                                    default => 'text-indigo-400 bg-indigo-400/10 border-indigo-400/20'
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded border {{ $levelColor }} text-[10px] font-bold uppercase">
                                {{ $log['level'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-slate-400">{{ $log['module'] }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-slate-200">{{ $log['message'] }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-indigo-500">{{ $log['request_id'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('system-logs.show', $log['id']) }}" class="text-slate-500 hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 bg-slate-900 border-t border-slate-700">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
