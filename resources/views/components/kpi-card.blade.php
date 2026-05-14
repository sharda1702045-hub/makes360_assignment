@props(['label', 'value', 'trend' => null, 'trendUp' => true, 'icon' => null])

<div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <span class="text-sm font-medium text-slate-500">{{ $label }}</span>
        @if($icon)
            <div class="p-2 bg-slate-50 rounded-lg text-indigo-600">
                {!! $icon !!}
            </div>
        @endif
    </div>
    <div class="flex items-end justify-between">
        <h3 class="text-2xl font-bold text-slate-900">{{ $value }}</h3>
        @if($trend)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $trendUp ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                {{ $trendUp ? '↑' : '↓' }} {{ $trend }}
            </span>
        @endif
    </div>
</div>
