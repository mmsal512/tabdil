<x-app-layout>
    <x-slot name="header">
        {{ __('AI Studio') }}
    </x-slot>

    <style>
        /* AI Studio Animations */
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(102, 126, 234, 0.3); }
            50% { box-shadow: 0 0 40px rgba(102, 126, 234, 0.5); }
        }
        .ai-gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #6B8DD6 100%);
            background-size: 200% 200%;
            animation: gradient-shift 4s ease infinite;
        }
        .stat-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .stat-card:hover {
            transform: translateY(-4px);
        }
        .tab-btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .tab-btn.active {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        .chat-bubble {
            animation: fadeInUp 0.3s ease-out;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .tool-icon {
            animation: float 3s ease-in-out infinite;
        }
    </style>

    <!-- Header Banner -->
    <div class="mb-8 relative overflow-hidden rounded-2xl ai-gradient-bg p-8">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#grid)" />
            </svg>
        </div>
        <div class="relative flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2 flex items-center gap-3">
                    <span class="tool-icon inline-block">🤖</span>
                    {{ __('AI Studio') }}
                </h1>
                <p class="text-white/80">{{ __('Explore powerful AI tools for content and productivity') }}</p>
            </div>
            <div class="hidden md:flex items-center gap-6">
                <div class="text-center">
                    <p class="text-3xl font-bold text-white">{{ number_format($stats['total_requests']) }}</p>
                    <p class="text-sm text-white/70">{{ __('Total Requests') }}</p>
                </div>
                <div class="w-px h-12 bg-white/20"></div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-white">{{ number_format($stats['today_requests']) }}</p>
                    <p class="text-sm text-white/70">{{ __('Today') }}</p>
                </div>
                <div class="w-px h-12 bg-white/20"></div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-white">{{ number_format($stats['total_tokens']) }}</p>
                    <p class="text-sm text-white/70">{{ __('Total Tokens') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Stats -->
    <div class="grid grid-cols-3 gap-3 mb-6 md:hidden">
        <div class="stat-card bg-white rounded-xl shadow-lg border border-gray-100 p-4 text-center">
            <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 mb-2">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                </svg>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ number_format($stats['total_requests']) }}</p>
            <p class="text-xs text-gray-500">{{ __('Total') }}</p>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-lg border border-gray-100 p-4 text-center">
            <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 mb-2">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ number_format($stats['today_requests']) }}</p>
            <p class="text-xs text-gray-500">{{ __('Today') }}</p>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-lg border border-gray-100 p-4 text-center">
            <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 mb-2">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
                </svg>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ number_format($stats['total_tokens']) }}</p>
            <p class="text-xs text-gray-500">{{ __('Tokens') }}</p>
        </div>
    </div>

    <!-- AI Tools Container -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden" x-data="{ activeTab: 'chat' }">
        <!-- Tab Navigation - Modern Style -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-4">
            <nav class="flex flex-wrap gap-2" aria-label="Tabs">
                <button @click="activeTab = 'chat'" 
                    :class="activeTab === 'chat' ? 'ai-gradient-bg text-white shadow-lg active' : 'bg-white text-gray-600 hover:bg-gray-50 shadow'" 
                    class="tab-btn flex items-center gap-2 px-5 py-3 text-sm font-medium rounded-xl">
                    <span class="text-lg">💬</span>
                    <span class="hidden sm:inline">{{ __('AI Chat') }}</span>
                </button>
                <button @click="activeTab = 'summarize'" 
                    :class="activeTab === 'summarize' ? 'ai-gradient-bg text-white shadow-lg active' : 'bg-white text-gray-600 hover:bg-gray-50 shadow'" 
                    class="tab-btn flex items-center gap-2 px-5 py-3 text-sm font-medium rounded-xl">
                    <span class="text-lg">📝</span>
                    <span class="hidden sm:inline">{{ __('Summarizer') }}</span>
                </button>
                <button @click="activeTab = 'title'" 
                    :class="activeTab === 'title' ? 'ai-gradient-bg text-white shadow-lg active' : 'bg-white text-gray-600 hover:bg-gray-50 shadow'" 
                    class="tab-btn flex items-center gap-2 px-5 py-3 text-sm font-medium rounded-xl">
                    <span class="text-lg">🏷️</span>
                    <span class="hidden sm:inline">{{ __('Title Generator') }}</span>
                </button>
                <button @click="activeTab = 'translate'" 
                    :class="activeTab === 'translate' ? 'ai-gradient-bg text-white shadow-lg active' : 'bg-white text-gray-600 hover:bg-gray-50 shadow'" 
                    class="tab-btn flex items-center gap-2 px-5 py-3 text-sm font-medium rounded-xl">
                    <span class="text-lg">🌐</span>
                    <span class="hidden sm:inline">{{ __('Translator') }}</span>
                </button>
                <button @click="activeTab = 'sentiment'" 
                    :class="activeTab === 'sentiment' ? 'ai-gradient-bg text-white shadow-lg active' : 'bg-white text-gray-600 hover:bg-gray-50 shadow'" 
                    class="tab-btn flex items-center gap-2 px-5 py-3 text-sm font-medium rounded-xl">
                    <span class="text-lg">😊</span>
                    <span class="hidden sm:inline">{{ __('Sentiment') }}</span>
                </button>
                <button @click="activeTab = 'custom'" 
                    :class="activeTab === 'custom' ? 'ai-gradient-bg text-white shadow-lg active' : 'bg-white text-gray-600 hover:bg-gray-50 shadow'" 
                    class="tab-btn flex items-center gap-2 px-5 py-3 text-sm font-medium rounded-xl">
                    <span class="text-lg">⚡</span>
                    <span class="hidden sm:inline">{{ __('Custom Prompt') }}</span>
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Chat Tab -->
            <div x-show="activeTab === 'chat'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
                <div class="space-y-4">
                    <div id="chat-messages" class="h-80 overflow-y-auto rounded-2xl p-4 bg-gradient-to-b from-gray-50 to-white border border-gray-200 space-y-3">
                        <div class="text-center">
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-full text-gray-500 text-sm">
                                <span class="tool-icon">🤖</span>
                                {{ __('Start a conversation with AI...') }}
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <input type="text" id="chat-input" placeholder="{{ __('Type your message...') }}" 
                            class="flex-1 rounded-xl border-gray-200 bg-gray-50 focus:border-purple-500 focus:ring-purple-500 focus:bg-white transition-all">
                        <button onclick="sendChat()" class="px-6 py-3 ai-gradient-bg text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all hover:scale-105">
                            {{ __('Send') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summarize Tab -->
            <div x-show="activeTab === 'summarize'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
                <div class="space-y-4">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                            <span class="text-lg">📝</span>
                            {{ __('Text to Summarize') }}
                        </label>
                        <textarea id="summarize-input" rows="6" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:border-purple-500 focus:ring-purple-500 focus:bg-white transition-all" placeholder="{{ __('Paste your text here...') }}"></textarea>
                    </div>
                    <div class="flex gap-3">
                        <select id="summarize-lang" class="rounded-xl border-gray-200 bg-gray-50 focus:border-purple-500 focus:ring-purple-500">
                            <option value="en">English</option>
                            <option value="ar">العربية</option>
                        </select>
                        <button onclick="runSummarize()" class="px-6 py-3 ai-gradient-bg text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all hover:scale-105">
                            {{ __('Summarize') }}
                        </button>
                    </div>
                    <div id="summarize-output" class="hidden p-4 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl border border-purple-100"></div>
                </div>
            </div>

            <!-- Title Generator Tab -->
            <div x-show="activeTab === 'title'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
                <div class="space-y-4">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                            <span class="text-lg">🏷️</span>
                            {{ __('Content') }}
                        </label>
                        <textarea id="title-input" rows="6" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:border-purple-500 focus:ring-purple-500 focus:bg-white transition-all" placeholder="{{ __('Enter your content to generate title and description...') }}"></textarea>
                    </div>
                    <button onclick="runTitleGen()" class="px-6 py-3 ai-gradient-bg text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all hover:scale-105">
                        {{ __('Generate') }}
                    </button>
                    <div id="title-output" class="hidden p-4 bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl border border-amber-100"></div>
                </div>
            </div>

            <!-- Translator Tab -->
            <div x-show="activeTab === 'translate'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
                <div class="space-y-4">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                            <span class="text-lg">🌐</span>
                            {{ __('Text to Translate') }}
                        </label>
                        <textarea id="translate-input" rows="4" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:border-purple-500 focus:ring-purple-500 focus:bg-white transition-all" placeholder="{{ __('Enter text...') }}"></textarea>
                    </div>
                    <div class="flex gap-3">
                        <select id="translate-target" class="rounded-xl border-gray-200 bg-gray-50 focus:border-purple-500 focus:ring-purple-500">
                            <option value="ar">{{ __('To Arabic') }}</option>
                            <option value="en">{{ __('To English') }}</option>
                        </select>
                        <button onclick="runTranslate()" class="px-6 py-3 ai-gradient-bg text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all hover:scale-105">
                            {{ __('Translate') }}
                        </button>
                    </div>
                    <div id="translate-output" class="hidden p-4 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl border border-blue-100"></div>
                </div>
            </div>

            <!-- Sentiment Tab -->
            <div x-show="activeTab === 'sentiment'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
                <div class="space-y-4">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                            <span class="text-lg">😊</span>
                            {{ __('Text to Analyze') }}
                        </label>
                        <textarea id="sentiment-input" rows="4" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:border-purple-500 focus:ring-purple-500 focus:bg-white transition-all" placeholder="{{ __('Enter text to analyze sentiment...') }}"></textarea>
                    </div>
                    <button onclick="runSentiment()" class="px-6 py-3 ai-gradient-bg text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all hover:scale-105">
                        {{ __('Analyze') }}
                    </button>
                    <div id="sentiment-output" class="hidden p-4 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border border-emerald-100"></div>
                </div>
            </div>

            <!-- Custom Prompt Tab -->
            <div x-show="activeTab === 'custom'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
                <div class="space-y-4">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                            <span class="text-lg">⚙️</span>
                            {{ __('System Prompt (Optional)') }}
                        </label>
                        <textarea id="custom-system" rows="2" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:border-purple-500 focus:ring-purple-500 focus:bg-white transition-all" placeholder="{{ __('Define AI behavior...') }}"></textarea>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                            <span class="text-lg">⚡</span>
                            {{ __('Your Prompt') }}
                        </label>
                        <textarea id="custom-input" rows="4" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:border-purple-500 focus:ring-purple-500 focus:bg-white transition-all" placeholder="{{ __('Enter your prompt...') }}"></textarea>
                    </div>
                    <button onclick="runCustom()" class="px-6 py-3 ai-gradient-bg text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all hover:scale-105">
                        {{ __('Run') }}
                    </button>
                    <div id="custom-output" class="hidden p-4 bg-gradient-to-br from-rose-50 to-pink-50 rounded-xl border border-rose-100"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay - Modern -->
    <div id="ai-loading" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 flex flex-col items-center gap-4 shadow-2xl">
            <div class="relative">
                <div class="w-16 h-16 rounded-full ai-gradient-bg animate-pulse"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
            <span class="text-gray-700 font-medium text-lg">{{ __('AI is thinking...') }}</span>
        </div>
    </div>

    @push('scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        function showLoading() {
            document.getElementById('ai-loading').classList.remove('hidden');
        }
        
        function hideLoading() {
            document.getElementById('ai-loading').classList.add('hidden');
        }
        
        async function aiRequest(tool, input, options = {}) {
            showLoading();
            try {
                const response = await fetch('/api/ai/run', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ tool, input, ...options })
                });
                return await response.json();
            } catch (error) {
                return { success: false, error: error.message };
            } finally {
                hideLoading();
            }
        }
        
        async function sendChat() {
            const input = document.getElementById('chat-input');
            const messages = document.getElementById('chat-messages');
            const message = input.value.trim();
            if (!message) return;
            
            messages.innerHTML += `<div class="chat-bubble flex justify-end"><div class="ai-gradient-bg text-white rounded-2xl px-4 py-3 max-w-xs shadow-lg">${message}</div></div>`;
            input.value = '';
            
            const result = await aiRequest('chat', message);
            if (result.success) {
                messages.innerHTML += `<div class="chat-bubble flex justify-start"><div class="bg-white border border-gray-200 rounded-2xl px-4 py-3 max-w-md shadow">${result.output}</div></div>`;
            } else {
                messages.innerHTML += `<div class="chat-bubble flex justify-start"><div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-4 py-3">${result.error}</div></div>`;
            }
            messages.scrollTop = messages.scrollHeight;
        }
        
        document.getElementById('chat-input').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendChat();
        });
        
        async function runSummarize() {
            const input = document.getElementById('summarize-input').value;
            const lang = document.getElementById('summarize-lang').value;
            const result = await aiRequest('summarize', input, { language: lang });
            const output = document.getElementById('summarize-output');
            output.classList.remove('hidden');
            output.innerHTML = result.success ? result.output : `<span class="text-red-600">${result.error}</span>`;
        }
        
        async function runTitleGen() {
            const input = document.getElementById('title-input').value;
            const result = await aiRequest('title', input);
            const output = document.getElementById('title-output');
            output.classList.remove('hidden');
            output.innerHTML = result.success ? result.output : `<span class="text-red-600">${result.error}</span>`;
        }
        
        async function runTranslate() {
            const input = document.getElementById('translate-input').value;
            const target = document.getElementById('translate-target').value;
            const result = await aiRequest('translate', input, { options: { target_language: target } });
            const output = document.getElementById('translate-output');
            output.classList.remove('hidden');
            output.innerHTML = result.success ? result.output : `<span class="text-red-600">${result.error}</span>`;
        }
        
        async function runSentiment() {
            const input = document.getElementById('sentiment-input').value;
            const result = await aiRequest('sentiment', input);
            const output = document.getElementById('sentiment-output');
            output.classList.remove('hidden');
            output.innerHTML = result.success ? result.output : `<span class="text-red-600">${result.error}</span>`;
        }
        
        async function runCustom() {
            const input = document.getElementById('custom-input').value;
            const system = document.getElementById('custom-system').value;
            const result = await aiRequest('custom', input, { options: { system_prompt: system } });
            const output = document.getElementById('custom-output');
            output.classList.remove('hidden');
            output.innerHTML = result.success ? result.output : `<span class="text-red-600">${result.error}</span>`;
        }
    </script>
    @endpush
</x-app-layout>
