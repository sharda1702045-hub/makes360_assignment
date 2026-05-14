<x-app-layout pageTitle="Design Template">
    <div x-data="templateEditor()" class="flex flex-col lg:flex-row h-[calc(100vh-160px)] -m-8">
        <!-- Editor Sidebar -->
        <div class="w-full lg:w-1/2 bg-white border-r border-slate-200 overflow-y-auto p-8">
            <div class="space-y-8">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-800">Template Editor</h2>
                    <div class="flex space-x-2">
                        <button class="px-4 py-2 bg-slate-50 text-slate-600 text-xs font-bold rounded-lg border border-slate-200">Save Draft</button>
                        <button @click="save()" :disabled="isLoading" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 disabled:opacity-50 flex items-center">
                            <span x-show="!isLoading">Finish Design</span>
                            <span x-show="isLoading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Basic Meta -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Template Name</label>
                        <input type="text" x-model="name" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Category</label>
                        <select class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                            <option>Marketing</option>
                            <option>Onboarding</option>
                            <option>Transactional</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email Subject</label>
                    <input type="text" x-model="subject" placeholder="Welcome to the platform, @{{first_name}}!" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                </div>

                <!-- HTML Editor -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email Content (HTML)</label>
                        <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Changes Saved</span>
                    </div>
                    <textarea 
                        x-model="content" 
                        class="w-full h-96 p-4 font-mono text-sm bg-slate-900 text-indigo-400 border border-slate-800 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none shadow-inner"
                        placeholder="Enter your email HTML here..."
                    ></textarea>
                </div>

                <!-- Placeholders -->
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                    <h3 class="text-xs font-bold text-slate-800 mb-4 uppercase tracking-widest">Smart Placeholders</h3>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="placeholder in placeholders">
                            <button 
                                @click="insertPlaceholder(placeholder)"
                                class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-600 hover:border-indigo-400 hover:text-indigo-600 transition-all flex items-center"
                            >
                                <span class="mr-1 text-indigo-500">{</span>
                                <span x-text="placeholder"></span>
                                <span class="ml-1 text-indigo-500">}</span>
                            </button>
                        </template>
                    </div>
                    <p class="mt-4 text-[10px] text-slate-400 italic">Click a placeholder to insert it into the subject or content.</p>
                </div>
            </div>
        </div>

        <!-- Live Preview Panel -->
        <div class="flex-1 bg-slate-50 overflow-y-auto p-12">
            <div class="max-w-2xl mx-auto space-y-8">
                <!-- Device Switcher -->
                <div class="flex items-center justify-center space-x-2">
                    <button @click="device = 'desktop'" :class="device === 'desktop' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-400'" class="p-2 rounded-lg transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </button>
                    <button @click="device = 'mobile'" :class="device === 'mobile' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-400'" class="p-2 rounded-lg transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </button>
                </div>

                <!-- Subject Preview -->
                <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Preview Subject</p>
                    <p class="text-sm font-bold text-slate-800" x-text="renderText(subject)"></p>
                </div>

                <!-- Email Container -->
                <div 
                    class="bg-white border border-slate-200 rounded-2xl shadow-xl transition-all duration-300 mx-auto overflow-hidden"
                    :class="device === 'mobile' ? 'max-w-[375px]' : 'w-full'"
                >
                    <div class="h-8 bg-slate-50 border-b border-slate-100 flex items-center px-4 space-x-1.5">
                        <div class="h-2 w-2 rounded-full bg-slate-200"></div>
                        <div class="h-2 w-2 rounded-full bg-slate-200"></div>
                        <div class="h-2 w-2 rounded-full bg-slate-200"></div>
                    </div>
                    <div class="p-8 min-h-[500px] overflow-y-auto" x-html="renderText(content)"></div>
                </div>
            </div>
        </div>

        <!-- Notification Toast -->
        <div 
            x-show="notification.show" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-4"
            class="fixed bottom-8 left-8 z-50 px-6 py-3 rounded-2xl shadow-2xl flex items-center space-x-3 border"
            :class="notification.type === 'success' ? 'bg-emerald-500 border-emerald-400 text-white' : 'bg-rose-500 border-rose-400 text-white'"
            style="display: none;"
        >
            <svg x-show="notification.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <svg x-show="notification.type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-sm font-bold" x-text="notification.message"></span>
        </div>
    </div>

    <script>
        function templateEditor() {
            return {
                name: 'New Newsletter Template',
                subject: 'Hello @{{first_name}}! Check our updates.',
                content: '<h1>Hi @{{first_name}},</h1><p>We have some exciting news to share from @{{company}}.</p><p>Click below to see the details:</p><br><a href="#" style="background:#4f46e5; color:white; padding:12px 24px; border-radius:8px; text-decoration:none; font-weight:bold;">View Updates</a>',
                device: 'desktop',
                placeholders: ['first_name', 'last_name', 'email', 'company', 'campaign_name', 'unsubscribe_link'],
                isLoading: false,
                notification: { show: false, message: '', type: 'success' },
                
                init() {
                    // Placeholder for future logic
                },

                async save() {
                    this.isLoading = true;
                    try {
                        const response = await fetch('{{ route('templates.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                name: this.name,
                                subject: this.subject,
                                content: this.content
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.showNotification(result.message, 'success');
                            setTimeout(() => {
                                window.location.href = result.redirect;
                            }, 1000);
                        } else {
                            this.showNotification('Failed to save template', 'error');
                        }
                    } catch (error) {
                        this.showNotification('An error occurred during saving', 'error');
                    } finally {
                        this.isLoading = false;
                    }
                },

                showNotification(message, type) {
                    this.notification = { show: true, message, type };
                    setTimeout(() => { this.notification.show = false; }, 3000);
                },

                renderText(text) {
                    let rendered = text;
                    const demoValues = {
                        'first_name': 'Alex',
                        'last_name': 'Smith',
                        'email': 'alex@example.com',
                        'company': 'BEMP Inc.',
                        'campaign_name': 'Summer Sale 2024',
                        'unsubscribe_link': '#'
                    };

                    Object.keys(demoValues).forEach(key => {
                        const regex = new RegExp('@{{' + key + '}}', 'g');
                        rendered = rendered.replace(regex, `<span class="text-indigo-600 font-bold bg-indigo-50 px-1 rounded">${demoValues[key]}</span>`);
                    });

                    return rendered;
                },

                insertPlaceholder(p) {
                    this.content += '@{{' + p + '}}';
                }
            }
        }
    </script>
</x-app-layout>
