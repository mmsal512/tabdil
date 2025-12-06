<x-app-layout>
    <x-slot name="header">
        {{ __('Content Writer') }}
    </x-slot>

    <style>
        /* Content Writer Animations */
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .ai-gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #6B8DD6 100%);
            background-size: 200% 200%;
            animation: gradient-shift 4s ease infinite;
        }
        .blog-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .seo-gradient {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .rewrite-gradient {
            background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%);
        }
        .tool-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .tool-card:hover {
            transform: translateY(-4px);
        }
        .tool-icon {
            animation: float 3s ease-in-out infinite;
        }
        .output-card {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.03) 100%);
        }
        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
    </style>

    <!-- Header Banner -->
    <div class="mb-8 relative overflow-hidden rounded-2xl ai-gradient-bg p-8">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="dots" width="10" height="10" patternUnits="userSpaceOnUse">
                        <circle cx="5" cy="5" r="1.5" fill="white"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#dots)" />
            </svg>
        </div>
        <div class="relative flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center">
                <span class="text-3xl tool-icon">✍️</span>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-white mb-1">{{ __('Content Writer') }}</h1>
                <p class="text-white/80">{{ __('AI-powered content creation tools') }}</p>
            </div>
        </div>
    </div>

    <!-- Tools Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Blog Generator Card -->
        <div class="tool-card bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="blog-gradient p-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                        <span class="text-2xl tool-icon">📝</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ __('Blog Generator') }}</h3>
                        <p class="text-xs text-white/70">{{ __('Create full blog posts') }}</p>
                    </div>
                </div>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Topic') }}</label>
                    <input type="text" id="blog-topic" 
                        class="w-full rounded-xl border-gray-200 bg-gray-50 focus:border-purple-500 focus:ring-purple-500 focus:bg-white transition-all" 
                        placeholder="{{ __('Enter blog topic...') }}">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Language') }}</label>
                        <select id="blog-lang" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:border-purple-500 focus:ring-purple-500 text-sm">
                            <option value="en">English</option>
                            <option value="ar">العربية</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Word Count') }}</label>
                        <select id="blog-words" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:border-purple-500 focus:ring-purple-500 text-sm">
                            <option value="300">~300</option>
                            <option value="500" selected>~500</option>
                            <option value="800">~800</option>
                            <option value="1200">~1200</option>
                        </select>
                    </div>
                </div>
                <button onclick="generateBlog()" class="w-full py-3 blog-gradient text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all hover:scale-[1.02] flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                    {{ __('Generate Blog') }}
                </button>
            </div>
        </div>

        <!-- SEO Keywords Card -->
        <div class="tool-card bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="seo-gradient p-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                        <span class="text-2xl tool-icon" style="animation-delay: 0.5s;">🔍</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ __('SEO Keywords') }}</h3>
                        <p class="text-xs text-white/70">{{ __('Extract key phrases') }}</p>
                    </div>
                </div>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Content') }}</label>
                    <textarea id="seo-content" rows="5" 
                        class="w-full rounded-xl border-gray-200 bg-gray-50 focus:border-emerald-500 focus:ring-emerald-500 focus:bg-white transition-all resize-none" 
                        placeholder="{{ __('Paste content to extract keywords...') }}"></textarea>
                </div>
                <button onclick="generateSeo()" class="w-full py-3 seo-gradient text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all hover:scale-[1.02] flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                    </svg>
                    {{ __('Generate Keywords') }}
                </button>
            </div>
        </div>

        <!-- Content Rewriter Card -->
        <div class="tool-card bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="rewrite-gradient p-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                        <span class="text-2xl tool-icon" style="animation-delay: 1s;">🔄</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ __('Content Rewriter') }}</h3>
                        <p class="text-xs text-white/70">{{ __('Transform your text') }}</p>
                    </div>
                </div>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Original Text') }}</label>
                    <textarea id="rewrite-input" rows="3" 
                        class="w-full rounded-xl border-gray-200 bg-gray-50 focus:border-purple-500 focus:ring-purple-500 focus:bg-white transition-all resize-none" 
                        placeholder="{{ __('Enter text to rewrite...') }}"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Style') }}</label>
                    <select id="rewrite-style" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:border-purple-500 focus:ring-purple-500 text-sm">
                        <option value="professional">{{ __('Professional') }}</option>
                        <option value="casual">{{ __('Casual') }}</option>
                        <option value="formal">{{ __('Formal') }}</option>
                        <option value="creative">{{ __('Creative') }}</option>
                    </select>
                </div>
                <button onclick="rewriteContent()" class="w-full py-3 rewrite-gradient text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all hover:scale-[1.02] flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    {{ __('Rewrite') }}
                </button>
            </div>
        </div>
        
    </div>

    <!-- Output Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-4 p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl ai-gradient-bg flex items-center justify-center">
                    <span class="text-lg">📄</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ __('Output') }}</h3>
                    <p class="text-xs text-gray-500">{{ __('Generated content will appear here...') }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="copyOutput()" class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                    </svg>
                    {{ __('Copy') }}
                </button>
                <button onclick="downloadMarkdown()" class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    MD
                </button>
                <button onclick="downloadHtml()" class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    HTML
                </button>
            </div>
        </div>
        <div id="content-output" class="min-h-[400px] p-6 output-card prose max-w-none overflow-auto">
            <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <p class="text-sm">{{ __('Choose a tool above to generate content') }}</p>
            </div>
        </div>
        <div id="output-stats" class="hidden px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-wrap gap-6 text-sm">
            <div class="flex items-center gap-2 text-gray-600">
                <svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span><span id="exec-time" class="font-semibold text-gray-900">0</span>ms</span>
            </div>
            <div class="flex items-center gap-2 text-gray-600">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
                </svg>
                <span><span id="token-count" class="font-semibold text-gray-900">0</span> {{ __('tokens') }}</span>
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
            <span class="text-gray-700 font-medium text-lg">{{ __('Generating content...') }}</span>
            <p class="text-sm text-gray-500">{{ __('This may take a few moments...') }}</p>
        </div>
    </div>

    @push('scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let currentOutput = '';
        
        function showLoading() {
            document.getElementById('ai-loading').classList.remove('hidden');
        }
        
        function hideLoading() {
            document.getElementById('ai-loading').classList.add('hidden');
        }
        
        function updateOutput(content, tokens = 0, time = 0) {
            currentOutput = content;
            const outputEl = document.getElementById('content-output');
            outputEl.innerHTML = `<div class="prose max-w-none">${content.replace(/\n/g, '<br>')}</div>`;
            outputEl.classList.add('animate-fadeIn');
            document.getElementById('output-stats').classList.remove('hidden');
            document.getElementById('exec-time').textContent = time;
            document.getElementById('token-count').textContent = tokens;
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
        
        async function generateBlog() {
            const topic = document.getElementById('blog-topic').value;
            const lang = document.getElementById('blog-lang').value;
            const words = document.getElementById('blog-words').value;
            
            if (!topic) return alert('{{ __("Please enter a topic") }}');
            
            const result = await aiRequest('blog', topic, { 
                language: lang, 
                options: { word_count: parseInt(words) } 
            });
            
            if (result.success) {
                updateOutput(result.output, result.tokens, result.execution_time);
            } else {
                updateOutput(`<div class="text-red-600 p-4 bg-red-50 rounded-xl">${result.error}</div>`);
            }
        }
        
        async function generateSeo() {
            const content = document.getElementById('seo-content').value;
            if (!content) return alert('{{ __("Please enter content") }}');
            
            const result = await aiRequest('seo', content);
            if (result.success) {
                updateOutput(result.output, result.tokens, result.execution_time);
            } else {
                updateOutput(`<div class="text-red-600 p-4 bg-red-50 rounded-xl">${result.error}</div>`);
            }
        }
        
        async function rewriteContent() {
            const input = document.getElementById('rewrite-input').value;
            const style = document.getElementById('rewrite-style').value;
            if (!input) return alert('{{ __("Please enter text") }}');
            
            const result = await aiRequest('rewrite', input, { options: { style } });
            if (result.success) {
                updateOutput(result.output, result.tokens, result.execution_time);
            } else {
                updateOutput(`<div class="text-red-600 p-4 bg-red-50 rounded-xl">${result.error}</div>`);
            }
        }
        
        function copyOutput() {
            navigator.clipboard.writeText(currentOutput);
            // Show toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-gray-900 text-white px-4 py-2 rounded-xl shadow-lg z-50 animate-fadeIn';
            toast.textContent = '{{ __("Copied!") }}';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        }
        
        function downloadMarkdown() {
            const blob = new Blob([currentOutput], { type: 'text/markdown' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'content.md';
            a.click();
        }
        
        function downloadHtml() {
            const html = `<!DOCTYPE html><html><head><meta charset="utf-8"><title>Content</title></head><body>${currentOutput.replace(/\n/g, '<br>')}</body></html>`;
            const blob = new Blob([html], { type: 'text/html' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'content.html';
            a.click();
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
    @endpush
</x-app-layout>
