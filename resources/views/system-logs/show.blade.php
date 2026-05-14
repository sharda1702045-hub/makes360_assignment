<x-app-layout pageTitle="Log Audit">
    <div class="bg-slate-900 -m-8 p-8 min-h-screen">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('system-logs.index') }}" class="p-2 bg-slate-800 border border-slate-700 rounded-lg text-slate-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div>
                        <h2 class="text-xl font-bold text-white">Event Detail</h2>
                        <p class="text-xs text-slate-400">Request ID: <span class="text-indigo-400">{{ $log['request_id'] }}</span></p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('system-logs.download', $log['id']) }}" class="px-4 py-2 bg-slate-800 text-slate-300 text-xs font-bold rounded-lg border border-slate-700 hover:bg-slate-750 transition-colors">Download JSON</a>
                    <form action="{{ route('system-logs.retry', $log['id']) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-colors">Retry Job</button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Event Summary -->
                    <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden shadow-2xl">
                        <div class="p-4 bg-slate-900 border-b border-slate-700 flex items-center justify-between">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Message Overview</h3>
                            @php
                                $levelColor = match($log['level']) {
                                    'ERROR' => 'text-rose-400 bg-rose-400/10 border-rose-400/20',
                                    'CRITICAL' => 'text-rose-500 bg-rose-500/20 border-rose-500/30',
                                    'WARNING' => 'text-amber-400 bg-amber-400/10 border-amber-400/20',
                                    default => 'text-indigo-400 bg-indigo-400/10 border-indigo-400/20'
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded border {{ $levelColor }} text-[10px] font-bold uppercase">{{ $log['level'] }}</span>
                        </div>
                        <div class="p-6">
                            <h1 class="text-2xl font-bold text-white mb-4">{{ $log['message'] }}</h1>
                            <div class="bg-slate-900 p-4 rounded-lg border border-slate-700 font-mono text-xs text-rose-300 leading-relaxed overflow-x-auto">
                                <pre>{{ $log['stack_trace'] }}</pre>
                            </div>
                        </div>
                    </div>

                    <!-- Payload Preview -->
                    <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden shadow-2xl">
                        <div class="p-4 bg-slate-900 border-b border-slate-700">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Request Payload</h3>
                        </div>
                        <div class="p-6">
                            <div class="bg-slate-950 p-4 rounded-lg border border-slate-800 font-mono text-xs text-indigo-400 overflow-x-auto">
                                <pre>{{ $log['payload'] }}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Metadata -->
                <div class="space-y-6">
                    <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden shadow-2xl">
                        <div class="p-4 bg-slate-900 border-b border-slate-700">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Metadata</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter mb-1">Module</p>
                                <p class="text-sm font-bold text-white">{{ $log['module'] }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter mb-1">Environment</p>
                                <p class="text-sm font-bold text-emerald-400">{{ $log['context']['environment'] }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter mb-1">Server Node</p>
                                <p class="text-sm font-bold text-white">{{ $log['context']['server_node'] }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter mb-1">Exact Timestamp</p>
                                <p class="text-sm font-bold text-white">{{ $log['timestamp'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden shadow-2xl">
                        <div class="p-4 bg-slate-900 border-b border-slate-700">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Network Context</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @foreach($log['context'] as $key => $value)
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter mb-1">{{ strtoupper($key) }}</p>
                                <p class="text-sm font-bold text-slate-300">{{ $value }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
