<aside 
    class="w-64 bg-slate-900 flex-shrink-0 flex flex-col transition-all duration-300"
    :class="sidebarOpen ? 'ml-0' : '-ml-64 lg:ml-0 lg:w-20'"
>
    <!-- Logo -->
    <div class="h-16 flex items-center px-6 bg-slate-950">
        <div class="h-8 w-8 bg-indigo-500 rounded-lg flex items-center justify-center shrink-0">
            <span class="text-white font-bold text-xl">B</span>
        </div>
        <span class="ml-3 text-white font-bold text-lg overflow-hidden whitespace-nowrap" x-show="sidebarOpen">BEMP</span>
    </div>

    <!-- Nav -->
    <nav class="flex-1 py-6 px-4 space-y-2 overflow-y-auto">
        <x-nav-link :href="route('dashboard')" icon="dashboard" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
        <x-nav-link :href="route('campaigns.index')" icon="campaigns" :active="request()->routeIs('campaigns.*')">Campaigns</x-nav-link>
        <x-nav-link :href="route('templates.index')" icon="templates" :active="request()->routeIs('templates.*')">Templates</x-nav-link>
        <x-nav-link :href="route('audience.index')" icon="contacts" :active="request()->routeIs('audience.*')">Audience</x-nav-link>
        <x-nav-link :href="route('imports.index')" icon="analytics" :active="request()->routeIs('imports.*')">Imports</x-nav-link>
        
        <div class="pt-6 pb-2">
            <span class="px-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider overflow-hidden" x-show="sidebarOpen">Infrastructure</span>
        </div>
        
        <x-nav-link :href="route('queue-monitor.index')" icon="queue" :active="request()->routeIs('queue-monitor.*')">Queue Monitor</x-nav-link>
        <x-nav-link :href="route('system-logs.index')" icon="logs" :active="request()->routeIs('system-logs.*')">System Logs</x-nav-link>
    </nav>

    <!-- Footer / User -->
    <div class="p-4 bg-slate-950 mt-auto">
        <div class="flex items-center">
            <div class="h-8 w-8 rounded bg-slate-800 shrink-0"></div>
            <div class="ml-3 overflow-hidden" x-show="sidebarOpen">
                <p class="text-xs font-bold text-white">Architect User</p>
                <p class="text-[10px] text-slate-500">Admin Account</p>
            </div>
        </div>
    </div>
</aside>
