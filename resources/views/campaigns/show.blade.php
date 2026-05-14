<x-app-layout pageTitle="Campaign Analysis">
    <!-- Summary Header -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center space-x-6">
                <div class="h-16 w-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">{{ $campaign['name'] }}</h2>
                    <p class="text-sm text-slate-500">Subject: <span class="font-medium text-slate-700">"{{ $campaign['subject'] }}"</span></p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full uppercase tracking-wider">
                    {{ $campaign['status'] }}
                </span>
                <a href="{{ route('campaigns.edit', $campaign['id']) }}" class="p-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-500 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Engagement Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-kpi-card label="Emails Sent" :value="number_format($campaign['sent'])" />
        <x-kpi-card label="Open Rate" value="{{ $campaign['delivered'] > 0 ? round(($campaign['opened'] / $campaign['delivered']) * 100, 1) : 0 }}%" trend="2.1%" :trendUp="true" />
        <x-kpi-card label="Click Rate" value="{{ $campaign['delivered'] > 0 ? round(($campaign['clicked'] / $campaign['delivered']) * 100, 1) : 0 }}%" trend="0.5%" :trendUp="true" />
        <x-kpi-card label="Bounce Rate" value="{{ $campaign['sent'] > 0 ? round(($campaign['bounced'] / $campaign['sent']) * 100, 1) : 0 }}%" trend="0.1%" :trendUp="false" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Delivery Breakdown -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Delivery Lifecycle</h3>
            </div>
            <div class="p-8">
                <div class="space-y-8">
                    <!-- Progress Bar Example -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-slate-600 uppercase tracking-wide">Delivered Successfully</span>
                            <span class="text-sm font-bold text-emerald-600">{{ number_format($campaign['delivered']) }}</span>
                        </div>
                        <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500" style="width: {{ $campaign['sent'] > 0 ? ($campaign['delivered'] / $campaign['sent']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2 text-rose-600">
                            <span class="text-sm font-medium uppercase tracking-wide">Bounced</span>
                            <span class="text-sm font-bold">{{ number_format($campaign['bounced']) }}</span>
                        </div>
                        <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-rose-500" style="width: {{ $campaign['sent'] > 0 ? ($campaign['bounced'] / $campaign['sent']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2 text-slate-400">
                            <span class="text-sm font-medium uppercase tracking-wide">Spam Complaints</span>
                            <span class="text-sm font-bold">{{ number_format($campaign['complained']) }}</span>
                        </div>
                        <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-slate-400" style="width: {{ $campaign['sent'] > 0 ? ($campaign['complained'] / $campaign['sent']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meta Information -->
        <div class="space-y-6">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-slate-800 mb-4">Configuration</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Target Audience</p>
                        <p class="text-sm font-medium text-slate-700">{{ $campaign['audience'] }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Used Template</p>
                        <p class="text-sm font-medium text-slate-700">{{ $campaign['template'] }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Sender Identity</p>
                        <p class="text-sm font-medium text-slate-700">marketing@company.com</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-200 p-6 text-white">
                <h3 class="font-bold mb-2">Need to resend?</h3>
                <p class="text-xs text-indigo-100 mb-4">You can target the users who didn't open this campaign with a follow-up.</p>
                <button class="w-full py-3 bg-white text-indigo-600 text-xs font-bold rounded-xl hover:bg-indigo-50 transition-colors">
                    Create Retargeting Campaign
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
