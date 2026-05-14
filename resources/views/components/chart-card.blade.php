@props(['title', 'id', 'subtitle' => null])

<div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-sm h-full">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
            @if($subtitle)
                <p class="text-sm text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>
        <div class="flex space-x-2">
            <button class="px-3 py-1 text-xs font-medium text-slate-600 bg-slate-50 rounded-md border border-slate-200">7D</button>
            <button class="px-3 py-1 text-xs font-medium text-white bg-indigo-600 rounded-md border border-indigo-700">30D</button>
        </div>
    </div>
    <div id="{{ $id }}" class="min-h-[300px]"></div>
</div>
