<x-app-layout>
    <x-slot name="header">
        {{ __('Content Writer') }}
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Input Section -->
        <div class="space-y-6">
            <!-- Blog Generator -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📝 {{ __('Blog Generator') }}</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Topic') }}</label>
                        <input type="text" id="blog-topic" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="{{ __('Enter blog topic...') }}">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Language') }}</label>
                            <select id="blog-lang" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                                <option value="en">English</option>
                                <option value="ar">العربية</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Word Count') }}</label>
                            <select id="blog-words" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                                <option value="300">~300</option>
                                <option value="500" selected>~500</option>
                                <option value="800">~800</option>
                                <option value="1200">~1200</option>
                            </select>
                        </div>
                    </div>
                    <button onclick="generateBlog()" class="w-full px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium">
                        ✨ {{ __('Generate Blog') }}
                    </button>
                </div>
            </div>

            <!-- SEO Generator -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🔍 {{ __('SEO Keywords') }}</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Content') }}</label>
                        <textarea id="seo-content" rows="4" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="{{ __('Paste content to extract keywords...') }}"></textarea>
                    </div>
                    <button onclick="generateSeo()" class="w-full px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                        🏷️ {{ __('Generate Keywords') }}
                    </button>
                </div>
            </div>

            <!-- Rewriter -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🔄 {{ __('Content Rewriter') }}</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Original Text') }}</label>
                        <textarea id="rewrite-input" rows="4" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="{{ __('Enter text to rewrite...') }}"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Style') }}</label>
                        <select id="rewrite-style" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            <option value="professional">{{ __('Professional') }}</option>
                            <option value="casual">{{ __('Casual') }}</option>
                            <option value="formal">{{ __('Formal') }}</option>
                            <option value="creative">{{ __('Creative') }}</option>
                        </select>
                    </div>
                    <button onclick="rewriteContent()" class="w-full px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium">
                        ✏️ {{ __('Rewrite') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Output Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">📄 {{ __('Output') }}</h3>
                <div class="flex gap-2">
                    <button onclick="copyOutput()" class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        📋 {{ __('Copy') }}
                    </button>
                    <button onclick="downloadMarkdown()" class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        ⬇️ Markdown
                    </button>
                    <button onclick="downloadHtml()" class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        ⬇️ HTML
                    </button>
                </div>
            </div>
            <div id="content-output" class="min-h-[500px] p-4 bg-gray-50 rounded-lg border border-gray-200 prose max-w-none overflow-auto">
                <p class="text-gray-500 text-center">{{ __('Generated content will appear here...') }}</p>
            </div>
            <div id="output-stats" class="hidden mt-4 flex gap-4 text-sm text-gray-500">
                <span>⏱️ <span id="exec-time">0</span>ms</span>
                <span>🔢 <span id="token-count">0</span> {{ __('tokens') }}</span>
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
            <span class="text-gray-700 font-medium">{{ __('Generating content...') }}</span>
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
            document.getElementById('content-output').innerHTML = content.replace(/\n/g, '<br>');
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
                updateOutput(`<span class="text-red-600">${result.error}</span>`);
            }
        }
        
        async function generateSeo() {
            const content = document.getElementById('seo-content').value;
            if (!content) return alert('{{ __("Please enter content") }}');
            
            const result = await aiRequest('seo', content);
            if (result.success) {
                updateOutput(result.output, result.tokens, result.execution_time);
            } else {
                updateOutput(`<span class="text-red-600">${result.error}</span>`);
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
                updateOutput(`<span class="text-red-600">${result.error}</span>`);
            }
        }
        
        function copyOutput() {
            navigator.clipboard.writeText(currentOutput);
            alert('{{ __("Copied!") }}');
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
    @endpush
</x-app-layout>
