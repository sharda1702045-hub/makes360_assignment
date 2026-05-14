<x-app-layout pageTitle="Import History">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Complete Audit Trail</h3>
            <a href="{{ route('imports.export') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Export Logs</a>
        </div>
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="px-6 py-4">File Details</th>
                    <th class="px-6 py-4">Volume Metrics</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Completed At</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($imports as $import)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-bold text-slate-900">{{ $import['filename'] }}</p>
                                <p class="text-[10px] text-slate-500 font-mono">ID: #{{ $import['id'] }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-6">
                            <div>
                                <p class="text-xs font-bold text-slate-900">{{ number_format($import->total_rows) }}</p>
                                <p class="text-[10px] text-slate-400 uppercase">Total</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-emerald-600">{{ number_format($import->processed_rows - $import->failed_rows) }}</p>
                                <p class="text-[10px] text-slate-400 uppercase">Success</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-rose-600">{{ $import->failed_rows }}</p>
                                <p class="text-[10px] text-slate-400 uppercase">Failed</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $import['status'] === 'Completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-indigo-100 text-indigo-800 animate-pulse' }}">
                            {{ $import['status'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-500">
                        {{ $import['created_at'] }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button class="p-2 text-slate-400 hover:text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
