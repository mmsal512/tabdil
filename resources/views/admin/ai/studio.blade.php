<x-app-layout>
    <x-slot name="header">
        {{ __('AI Studio') }}
    </x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 rounded-lg bg-primary-500 p-3">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Total Requests') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_requests']) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 rounded-lg bg-emerald-500 p-3">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Today') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['today_requests']) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 rounded-lg bg-purple-500 p-3">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Total Tokens') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_tokens']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Tools Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100" x-data="{ activeTab: 'chat' }">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="flex flex-wrap gap-2 p-4" aria-label="Tabs">
                <button @click="activeTab = 'chat'" :class="activeTab === 'chat' ? 'bg-primary-100 text-primary-700' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors">
                    💬 {{ __('AI Chat') }}
                </button>
                <button @click="activeTab = 'summarize'" :class="activeTab === 'summarize' ? 'bg-primary-100 text-primary-700' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors">
                    📝 {{ __('Summarizer') }}
                </button>
                <button @click="activeTab = 'title'" :class="activeTab === 'title' ? 'bg-primary-100 text-primary-700' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors">
                    🏷️ {{ __('Title Generator') }}
                </button>
                <button @click="activeTab = 'translate'" :class="activeTab === 'translate' ? 'bg-primary-100 text-primary-700' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors">
                    🌐 {{ __('Translator') }}
                </button>
                <button @click="activeTab = 'sentiment'" :class="activeTab === 'sentiment' ? 'bg-primary-100 text-primary-700' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors">
                    😊 {{ __('Sentiment') }}
                </button>
                <button @click="activeTab = 'custom'" :class="activeTab === 'custom' ? 'bg-primary-100 text-primary-700' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors">
                    ⚡ {{ __('Custom Prompt') }}
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Chat Tab -->
            <div x-show="activeTab === 'chat'" x-cloak>
                <div class="space-y-4">
                    <div id="chat-messages" class="h-80 overflow-y-auto border border-gray-200 rounded-lg p-4 bg-gray-50 space-y-3">
                        <div class="text-center text-gray-500 text-sm">{{ __('Start a conversation with AI...') }}</div>
                    </div>
                    <div class="flex gap-3">
                        <input type="text" id="chat-input" placeholder="{{ __('Type your message...') }}" class="flex-1 rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        <button onclick="sendChat()" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                            {{ __('Send') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summarize Tab -->
            <div x-show="activeTab === 'summarize'" x-cloak>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Text to Summarize') }}</label>
                        <textarea id="summarize-input" rows="6" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="{{ __('Paste your text here...') }}"></textarea>
                    </div>
                    <div class="flex gap-3">
                        <select id="summarize-lang" class="rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            <option value="en">English</option>
                            <option value="ar">العربية</option>
                        </select>
                        <button onclick="runSummarize()" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                            {{ __('Summarize') }}
                        </button>
                    </div>
                    <div id="summarize-output" class="hidden p-4 bg-gray-50 rounded-lg border border-gray-200"></div>
                </div>
            </div>

            <!-- Title Generator Tab -->
            <div x-show="activeTab === 'title'" x-cloak>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Content') }}</label>
                        <textarea id="title-input" rows="6" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="{{ __('Enter your content to generate title and description...') }}"></textarea>
                    </div>
                    <button onclick="runTitleGen()" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        {{ __('Generate') }}
                    </button>
                    <div id="title-output" class="hidden p-4 bg-gray-50 rounded-lg border border-gray-200"></div>
                </div>
            </div>

            <!-- Translator Tab -->
            <div x-show="activeTab === 'translate'" x-cloak>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Text to Translate') }}</label>
                        <textarea id="translate-input" rows="4" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="{{ __('Enter text...') }}"></textarea>
                    </div>
                    <div class="flex gap-3">
                        <select id="translate-target" class="rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            <option value="ar">{{ __('To Arabic') }}</option>
                            <option value="en">{{ __('To English') }}</option>
                        </select>
                        <button onclick="runTranslate()" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                            {{ __('Translate') }}
                        </button>
                    </div>
                    <div id="translate-output" class="hidden p-4 bg-gray-50 rounded-lg border border-gray-200"></div>
                </div>
            </div>

            <!-- Sentiment Tab -->
            <div x-show="activeTab === 'sentiment'" x-cloak>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Text to Analyze') }}</label>
                        <textarea id="sentiment-input" rows="4" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="{{ __('Enter text to analyze sentiment...') }}"></textarea>
                    </div>
                    <button onclick="runSentiment()" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        {{ __('Analyze') }}
                    </button>
                    <div id="sentiment-output" class="hidden p-4 bg-gray-50 rounded-lg border border-gray-200"></div>
                </div>
            </div>

            <!-- Custom Prompt Tab -->
            <div x-show="activeTab === 'custom'" x-cloak>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('System Prompt (Optional)') }}</label>
                        <textarea id="custom-system" rows="2" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="{{ __('Define AI behavior...') }}"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Your Prompt') }}</label>
                        <textarea id="custom-input" rows="4" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="{{ __('Enter your prompt...') }}"></textarea>
                    </div>
                    <button onclick="runCustom()" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        {{ __('Run') }}
                    </button>
                    <div id="custom-output" class="hidden p-4 bg-gray-50 rounded-lg border border-gray-200"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="ai-loading" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 flex items-center gap-4">
            <svg class="animate-spin h-8 w-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700 font-medium">{{ __('AI is thinking...') }}</span>
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
            
            messages.innerHTML += `<div class="flex justify-end"><div class="bg-primary-600 text-white rounded-lg px-4 py-2 max-w-xs">${message}</div></div>`;
            input.value = '';
            
            const result = await aiRequest('chat', message);
            if (result.success) {
                messages.innerHTML += `<div class="flex justify-start"><div class="bg-white border border-gray-200 rounded-lg px-4 py-2 max-w-md">${result.output}</div></div>`;
            } else {
                messages.innerHTML += `<div class="flex justify-start"><div class="bg-red-100 text-red-700 rounded-lg px-4 py-2">${result.error}</div></div>`;
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
