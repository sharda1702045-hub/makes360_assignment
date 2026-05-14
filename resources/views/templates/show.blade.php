<x-app-layout pageTitle="Template Preview">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 h-full">
        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-slate-900">{{ $template->name }}</h2>
                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[10px] font-bold rounded uppercase">Marketing</span>
                </div>
                
                <div class="space-y-4 mb-8 text-sm">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Subject Line</p>
                        <p class="text-slate-700">{{ $template->subject }}</p>
                    </div>
                </div>

                <div class="flex flex-col space-y-3">
                    <a href="{{ route('templates.edit', $template->id) }}" class="w-full py-3 bg-indigo-600 text-white text-xs font-bold rounded-xl text-center hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-100">Edit Design</a>
                    <form action="{{ route('templates.duplicate', $template->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-50 transition-colors">Duplicate Template</button>
                    </form>
                    <form action="{{ route('templates.destroy', $template->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this template?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-3 bg-rose-50 text-rose-600 border border-rose-100 text-xs font-bold rounded-xl hover:bg-rose-100 transition-colors">Delete Template</button>
                    </form>
                </div>
            </div>

            <!-- Stats -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-slate-800 mb-4 text-sm">Performance Stats</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Open Rate</p>
                        <p class="text-lg font-black text-slate-900">{{ $template->aggregated_stats->open_rate }}%</p>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Click Rate</p>
                        <p class="text-lg font-black text-slate-900">{{ $template->aggregated_stats->click_rate }}%</p>
                    </div>
                </div>
                <p class="mt-4 text-[10px] text-slate-400 italic">Aggregated from {{ $template->campaigns->count() }} campaigns.</p>
            </div>
        </div>

        <!-- Preview Area -->
        <div class="lg:col-span-2 bg-slate-50 rounded-2xl p-8 border border-slate-200 overflow-y-auto" x-data="{ device: 'desktop' }">
            <div class="flex items-center justify-between mb-8">
                <h3 class="font-bold text-slate-800">Visual Preview</h3>
                <div class="flex space-x-1 p-1 bg-white rounded-xl shadow-sm border border-slate-200">
                    <button @click="device = 'desktop'" :class="device === 'desktop' ? 'bg-indigo-600 text-white' : 'text-slate-400'" class="px-4 py-2 text-xs font-bold rounded-lg transition-all">Desktop</button>
                    <button @click="device = 'mobile'" :class="device === 'mobile' ? 'bg-indigo-600 text-white' : 'text-slate-400'" class="px-4 py-2 text-xs font-bold rounded-lg transition-all">Mobile</button>
                </div>
            </div>

            <div 
                class="bg-white border border-slate-200 rounded-2xl shadow-xl transition-all duration-300 mx-auto overflow-hidden"
                :class="device === 'mobile' ? 'max-w-[375px]' : 'w-full'"
            >
                <div class="h-8 bg-slate-50 border-b border-slate-100 flex items-center px-4 space-x-1.5">
                    <div class="h-2 w-2 rounded-full bg-slate-200"></div>
                    <div class="h-2 w-2 rounded-full bg-slate-200"></div>
                    <div class="h-2 w-2 rounded-full bg-slate-200"></div>
                </div>
                <div class="p-12 min-h-[600px]">
                    {!! $template->body_html !!}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
