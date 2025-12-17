<!-- AI Chat Widget -->
<div x-data="aiChatWidget()" x-cloak class="fixed bottom-6 z-50" :class="isRtl ? 'left-6' : 'right-6'">
    <!-- Chat Button -->
    <button @click="toggleChat()" class="w-14 h-14 bg-gradient-to-r from-primary-600 to-purple-600 rounded-full shadow-lg flex items-center justify-center text-white hover:scale-110 transition-transform">
        <svg x-show="!isOpen" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
        <svg x-show="isOpen" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Chat Window -->
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4" class="absolute bottom-16 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl overflow-hidden" :class="isRtl ? 'left-0' : 'right-0'">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary-600 to-purple-600 px-4 py-3 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold">{{ __('AI Assistant') }}</h3>
                    <p class="text-xs text-white/80">{{ __('Ask me anything') }}</p>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <div x-ref="messagesContainer" class="h-80 overflow-y-auto p-4 space-y-3 bg-gray-50">
            <!-- Welcome Message -->
            <template x-if="messages.length === 0">
                <div class="text-center text-gray-500 text-sm py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                    {{ __('Hello! How can I help you today?') }}
                </div>
            </template>

            <!-- Message List -->
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.role === 'user' ? 'bg-primary-600 text-white' : 'bg-white border border-gray-200 text-gray-800'" class="max-w-[80%] rounded-2xl px-4 py-2 text-sm" x-text="msg.content"></div>
                </div>
            </template>

            <!-- Typing Indicator -->
            <div x-show="isTyping" class="flex justify-start">
                <div class="bg-white border border-gray-200 rounded-2xl px-4 py-3">
                    <div class="flex gap-1">
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input -->
        <div class="p-3 bg-white border-t border-gray-200">
            <div class="flex gap-2">
                <input x-model="inputMessage" @keydown.enter="sendMessage()" type="text" :placeholder="'{{ __('Type a message...') }}'" class="flex-1 text-sm border-gray-300 rounded-full px-4 py-2 focus:border-primary-500 focus:ring-primary-500">
                <button @click="sendMessage()" :disabled="isTyping || !inputMessage.trim()" class="w-10 h-10 bg-primary-600 text-white rounded-full flex items-center justify-center hover:bg-primary-700 transition-colors disabled:opacity-50">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function aiChatWidget() {
    return {
        isOpen: false,
        isTyping: false,
        inputMessage: '',
        messages: [],
        isRtl: document.documentElement.dir === 'rtl',

        toggleChat() {
            this.isOpen = !this.isOpen;
        },

        async sendMessage() {
            const message = this.inputMessage.trim();
            if (!message || this.isTyping) return;

            this.messages.push({ role: 'user', content: message });
            this.inputMessage = '';
            this.isTyping = true;

            this.$nextTick(() => {
                this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
            });

            try {
                // Send message to n8n webhook with timeout
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 60000); // 60 second timeout
                
                const response = await fetch('https://n8ntabdil.n8ntabdil.online/webhook/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ 
                        message: message
                    }),
                    signal: controller.signal
                });
                
                clearTimeout(timeoutId);
                
                // Check if response is OK
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                // Parse webhook response from n8n AI Agent
                let result = await response.json();
                
                // n8n may return an array (First Incoming Item from AI Agent)
                if (Array.isArray(result) && result.length > 0) {
                    result = result[0];
                }
                
                // Extract the assistant message from n8n AI Agent response
                let assistantMessage = '';
                
                // n8n AI Agent typically returns { output: "..." }
                if (typeof result === 'string') {
                    assistantMessage = result;
                } else if (result.output) {
                    assistantMessage = result.output;
                } else if (result.text) {
                    assistantMessage = result.text;
                } else if (result.response) {
                    assistantMessage = result.response;
                } else if (result.message) {
                    assistantMessage = result.message;
                } else if (result.content) {
                    assistantMessage = result.content;
                } else if (result.reply) {
                    assistantMessage = result.reply;
                } else if (result.data && typeof result.data === 'string') {
                    assistantMessage = result.data;
                } else if (result.data && result.data.output) {
                    assistantMessage = result.data.output;
                } else {
                    // Fallback: stringify the entire response for debugging
                    console.log('n8n response structure:', result);
                    assistantMessage = typeof result === 'object' ? 
                        (Object.values(result).find(v => typeof v === 'string' && v.length > 0) || JSON.stringify(result)) 
                        : String(result);
                }
                
                this.messages.push({ role: 'assistant', content: assistantMessage });
            } catch (error) {
                console.error('Webhook error:', error);
                let errorMessage = '{{ __("Connection error. Please try again.") }}';
                
                if (error.name === 'AbortError') {
                    errorMessage = '{{ __("Request timed out. Please try again.") }}';
                }
                
                this.messages.push({ role: 'assistant', content: errorMessage });
            }

            this.isTyping = false;
            this.$nextTick(() => {
                this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
            });
        }
    }
}
</script>
