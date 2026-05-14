<x-app-layout pageTitle="Contact Imports">
    <div x-data="importManager()" x-init="init()">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Upload & Active Processing -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Upload Zone -->
                <form action="{{ route('imports.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <input type="file" name="file" id="fileInput" class="hidden" accept=".csv" onchange="document.getElementById('uploadForm').submit()">
                    <div class="bg-white border-2 border-dashed border-slate-200 rounded-2xl p-12 text-center hover:border-indigo-400 transition-colors cursor-pointer group" onclick="document.getElementById('fileInput').click()">
                        <div class="h-16 w-16 bg-indigo-50 rounded-2xl mx-auto flex items-center justify-center text-indigo-600 mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Upload your contact file</h3>
                        <p class="text-sm text-slate-500 mb-6">Drag and drop your .csv file here.</p>
                        <button type="button" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-100 hover:bg-indigo-700">Browse Files</button>
                        <div class="mt-4 flex items-center justify-center space-x-4">
                            <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Max size: 50MB | Supported: CSV</p>
                            <span class="text-slate-300">|</span>
                            <a href="{{ asset('samples/contacts_sample.csv') }}" class="text-[10px] text-indigo-600 hover:text-indigo-800 uppercase tracking-widest font-bold flex items-center" onclick="event.stopPropagation()">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Download Sample CSV
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Active Progress Widget (Conditional) -->
                <div x-show="activeImport" x-cloak class="bg-slate-900 rounded-2xl p-8 text-white shadow-2xl relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h3 class="text-xl font-bold mb-1">Processing Audience Import</h3>
                                <p class="text-xs text-slate-400">Filename: <span class="font-mono text-indigo-400" x-text="activeImport ? activeImport.filename : ''"></span></p>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-black text-indigo-400" x-text="status.percentage + '%'"></span>
                                <p class="text-[10px] text-slate-500 uppercase font-bold">Progress</p>
                            </div>
                        </div>

                        <!-- Main Progress Bar -->
                        <div class="h-4 bg-slate-800 rounded-full mb-8 overflow-hidden">
                            <div class="h-full bg-indigo-500 transition-all duration-500 ease-out" :style="'width: ' + status.percentage + '%'"></div>
                        </div>

                        <!-- Multi-Stage Indicators -->
                        <div class="grid grid-cols-4 gap-4 mb-8">
                            <div class="text-center">
                                <div class="h-1 w-full mb-2 rounded-full" :class="['Pending', 'Validating', 'Inserting', 'Completed'].includes(status.status) ? 'bg-indigo-500' : 'bg-slate-700'"></div>
                                <p class="text-[9px] font-bold uppercase" :class="['Pending', 'Validating', 'Inserting', 'Completed'].includes(status.status) ? 'text-indigo-400' : 'text-slate-500'">Queued</p>
                            </div>
                            <div class="text-center">
                                <div class="h-1 w-full mb-2 rounded-full" :class="['Validating', 'Inserting', 'Completed'].includes(status.status) ? 'bg-indigo-500' : 'bg-slate-700'"></div>
                                <p class="text-[9px] font-bold uppercase" :class="['Validating', 'Inserting', 'Completed'].includes(status.status) ? 'text-indigo-400' : 'text-slate-500'">Validating</p>
                            </div>
                            <div class="text-center">
                                <div class="h-1 w-full mb-2 rounded-full relative overflow-hidden" :class="['Inserting', 'Completed'].includes(status.status) ? 'bg-indigo-500' : 'bg-slate-700'">
                                    <div x-show="status.status === 'Inserting'" class="h-full bg-indigo-400 absolute" :style="'width: ' + status.percentage + '%'"></div>
                                </div>
                                <p class="text-[9px] font-bold uppercase" :class="['Inserting', 'Completed'].includes(status.status) ? 'text-indigo-400' : 'text-slate-500'">Inserting</p>
                            </div>
                            <div class="text-center">
                                <div class="h-1 w-full mb-2 rounded-full" :class="status.status === 'Completed' ? 'bg-emerald-500' : 'bg-slate-700'"></div>
                                <p class="text-[9px] font-bold uppercase" :class="status.status === 'Completed' ? 'text-emerald-400' : 'text-slate-500'">Completed</p>
                            </div>
                        </div>

                        <!-- Sub-Metrics -->
                        <div class="flex items-center justify-between pt-6 border-t border-slate-800">
                            <div class="flex space-x-8">
                                <div>
                                    <p class="text-[10px] text-slate-500 uppercase font-bold mb-1">Processed</p>
                                    <p class="text-sm font-bold text-white" x-text="status.processed.toLocaleString()"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-500 uppercase font-bold mb-1">Duplicates</p>
                                    <p class="text-sm font-bold text-amber-500" x-text="status.duplicates || 0"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-500 uppercase font-bold mb-1">Failed</p>
                                    <p class="text-sm font-bold text-rose-500" x-text="status.failed"></p>
                                </div>
                            </div>
                            <button class="px-4 py-2 bg-slate-800 text-slate-400 text-xs font-bold rounded-lg hover:text-white transition-colors">
                                Cancel Import
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats & Sidebar -->
            <div class="space-y-6">
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-slate-800 mb-6">Recent Import Activity</h3>
                    <div class="space-y-4">
                        @foreach($recentActivity as $activity)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center">
                                <div class="h-2 w-2 rounded-full {{ $activity->status === 'Completed' ? 'bg-emerald-500' : 'bg-indigo-500' }} mr-2"></div>
                                <span class="text-slate-700">{{ $activity->filename }}</span>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $activity->created_at->format('d M') }}</span>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('imports.history') }}" class="block w-full text-center py-3 bg-slate-50 text-slate-600 text-xs font-bold rounded-xl mt-6 hover:bg-slate-100 transition-colors border border-slate-100">
                        View Full History
                    </a>
                </div>

                <div class="bg-indigo-600 rounded-2xl p-6 text-white">
                    <h4 class="font-bold mb-2">Import Optimization</h4>
                    <p class="text-xs text-indigo-100 mb-4 leading-relaxed">
                        Our import engine uses chunk-based queueing. This allows us to process files with 100k+ rows without timing out or crashing.
                    </p>
                    <div class="flex items-center text-[10px] font-bold uppercase text-indigo-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Queue Strategy: FIFO-Balanced
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function importManager() {
            return {
                activeImport: @json($activeImport),
                status: {
                    percentage: {{ $activeImport ? round(($activeImport->processed_rows / max($activeImport->total_rows, 1)) * 100) : 0 }},
                    processed: {{ $activeImport->processed_rows ?? 0 }},
                    total: {{ $activeImport->total_rows ?? 0 }},
                    failed: {{ $activeImport->failed_rows ?? 0 }},
                    duplicates: {{ $activeImport->duplicate_rows ?? 0 }}
                },

                init() {
                    if (this.activeImport) {
                        this.pollStatus();
                    }
                },

                pollStatus() {
                    const pollInterval = setInterval(async () => {
                        try {
                            const res = await fetch(`/imports/${this.activeImport.id}/status`);
                            const data = await res.json();
                            this.status = data;
                            
                            if (this.status.status === 'Completed' || this.status.percentage >= 100) {
                                clearInterval(pollInterval);
                                setTimeout(() => {
                                    this.activeImport = null;
                                    window.location.reload(); // Refresh to show in history
                                }, 2000);
                            }
                        } catch (e) {
                            console.error('Failed to poll status');
                        }
                    }, 3000);
                }
            }
        }
    </script>
</x-app-layout>
