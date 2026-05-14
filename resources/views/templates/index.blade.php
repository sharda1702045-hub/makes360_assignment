<x-app-layout pageTitle="Email Templates">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-sm text-slate-500">Create and manage reusable email layouts for your campaigns.</p>
        </div>
        <a href="{{ route('templates.create') }}" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Template
        </a>
    </div>

    <form action="{{ route('templates.index') }}" method="GET" class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search templates..." class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="flex items-center space-x-2">
            <button type="submit" class="px-4 py-2 text-xs font-bold text-indigo-600 bg-indigo-50 rounded-lg">Apply Filters</button>
            <a href="{{ route('templates.index') }}" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-50 rounded-lg text-center">Clear</a>
        </div>
    </form>

    <!-- Template Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($templates as $template)
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
            <!-- Thumbnail Placeholder -->
            <div class="bg-indigo-50 h-48 flex items-center justify-center relative">
                <div class="absolute inset-0 bg-slate-900 opacity-0 group-hover:opacity-40 transition-opacity flex items-center justify-center space-x-2">
                    <a href="{{ route('templates.edit', $template->id) }}" class="p-2 bg-white rounded-lg text-slate-900 hover:bg-indigo-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    </a>
                    <a href="{{ route('templates.show', $template->id) }}" class="p-2 bg-white rounded-lg text-slate-900 hover:bg-indigo-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </a>
                </div>
                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            
            <!-- Details -->
            <div class="p-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold rounded uppercase">Marketing</span>
                    <span class="text-[10px] text-slate-400 font-medium">New</span>
                </div>
                <h3 class="font-bold text-slate-900 mb-1 group-hover:text-indigo-600 transition-colors">{{ $template->name }}</h3>
                <p class="text-xs text-slate-500">Updated {{ $template->updated_at->diffForHumans() }}</p>
            </div>
        </div>
        @endforeach

        <!-- Create New Card -->
        <a href="{{ route('templates.create') }}" class="border-2 border-dashed border-slate-200 rounded-2xl h-full min-h-[300px] flex flex-col items-center justify-center group hover:border-indigo-400 hover:bg-indigo-50 transition-all">
            <div class="h-12 w-12 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 group-hover:bg-white group-hover:text-indigo-600 transition-colors mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </div>
            <span class="text-sm font-bold text-slate-500 group-hover:text-indigo-600 transition-colors">Create from scratch</span>
        </a>
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $templates->appends(request()->query())->links() }}
    </div>
</x-app-layout>
