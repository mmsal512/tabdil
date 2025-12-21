<div id="support-widget" class="fixed bottom-6 z-50 font-sans {{ App::getLocale() == 'ar' ? 'right-6' : 'left-6' }}" dir="{{ App::getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <!-- Floating Button -->
    <button id="support-toggle-btn" onclick="toggleSupportModal()" 
        class="w-14 h-14 rounded-full shadow-lg transform transition-all duration-300 hover:scale-110 hover:rotate-12 focus:outline-none flex items-center justify-center text-white relative group"
        style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);">
        
        <!-- Chat Icon -->
        <svg id="chat-icon" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 transition-opacity duration-300 opacity-100 absolute" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>

        <!-- Close Icon (Hidden by default) -->
        <svg id="close-icon" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 transition-opacity duration-300 opacity-0 absolute" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>

        <!-- Pulse Effect -->
        <span class="absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75 animate-ping group-hover:hidden"></span>
    </button>

    <!-- Modal Window -->
    <div id="support-modal" class="hidden absolute bottom-20 w-[350px] bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden transition-all duration-300 opacity-0 transform translate-y-10 border border-gray-100 dark:border-gray-700 {{ App::getLocale() == 'ar' ? 'right-0 origin-bottom-right' : 'left-0 origin-bottom-left' }}">
        
        <!-- Header -->
        <div class="p-4 text-white relative overflow-hidden" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);">
            <h3 class="font-bold text-lg relative z-10">{{ __('Contact Us') }}</h3>
            <p class="text-xs text-purple-100 relative z-10 opacity-90">{{ __('We will reply shortly') }}</p>
            
            <!-- Decorative Circles -->
            <div class="absolute top-[-20px] right-[-20px] w-20 h-20 bg-white opacity-10 rounded-full"></div>
            <div class="absolute bottom-[-10px] left-[-10px] w-10 h-10 bg-white opacity-10 rounded-full"></div>
        </div>

        <!-- Body -->
        <div class="p-5">
            <form id="support-form" onsubmit="submitSupportForm(event)">
                @csrf
                
                <!-- Name -->
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Name') }} <span class="text-gray-300 text-[10px]">({{ __('Optional') }})</span></label>
                    <input type="text" name="name" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors" placeholder="{{ __('Name') }}">
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Email') }} <span class="text-gray-300 text-[10px]">({{ __('Optional to reply to you') }})</span></label>
                    <input type="email" name="email" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors" placeholder="example@email.com">
                </div>

                <!-- Type -->
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Message Type') }}</label>
                    <select name="type" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors">
                        <option value="استفسار">{{ __('Inquiry') }}</option>
                        <option value="اقتراح">{{ __('Suggestion') }}</option>
                        <option value="مشكلة">{{ __('Complaint') }}</option>
                        <option value="اخرى">{{ __('Other') }}</option>
                    </select>
                </div>

                <!-- Message -->
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Your Message') }} <span class="text-red-500">*</span></label>
                    <textarea name="message" rows="3" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors resize-none" placeholder="{{ __('Write your message here...') }}"></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submit-btn" class="w-full py-2.5 rounded-lg text-white text-sm font-semibold shadow-md transform transition-transform active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);">
                    <span id="btn-text">{{ __('Send Message') }}</span>
                    <span id="btn-loader" class="hidden">
                        <svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>

            <!-- Success Message -->
            <div id="success-message" class="hidden text-center py-8">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h4 class="text-gray-800 dark:text-white font-bold mb-2">{{ __('Message Sent Successfully') }}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('We will reply shortly') }}</p>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSupportModal() {
        const modal = document.getElementById('support-modal');
        const iconChat = document.getElementById('chat-icon');
        const iconClose = document.getElementById('close-icon');

        if (modal.classList.contains('hidden')) {
            // Open
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0', 'translate-y-10');
            }, 10);
            iconChat.classList.add('opacity-0');
            iconClose.classList.remove('opacity-0');
        } else {
            // Close
            modal.classList.add('opacity-0', 'translate-y-10');
            setTimeout(() => {
                modal.classList.add('hidden');
                // Reset form state after closing
                setTimeout(() => {
                   document.getElementById('support-form').classList.remove('hidden');
                   document.getElementById('success-message').classList.add('hidden');
                   document.getElementById('support-form').reset();
                }, 300);
            }, 300);
            iconChat.classList.remove('opacity-0');
            iconClose.classList.add('opacity-0');
        }
    }

    async function submitSupportForm(e) {
        e.preventDefault();
        
        const form = document.getElementById('support-form');
        const btn = document.getElementById('submit-btn');
        const btnText = document.getElementById('btn-text');
        const btnLoader = document.getElementById('btn-loader');
        
        // Loading state
        btn.disabled = true;
        btnText.classList.add('hidden');
        btnLoader.classList.remove('hidden');

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        try {
            // Post to Laravel Controller
            const response = await fetch('/support/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                // Success
                form.classList.add('hidden');
                document.getElementById('success-message').classList.remove('hidden');
                
                // Auto close after 3 seconds
                setTimeout(() => {
                    toggleSupportModal();
                }, 3000);
            } else {
                throw new Error('Failed');
            }
        } catch (error) {
            alert('{{ __('Error sending message') }}');
            console.error(error);
        } finally {
            // Reset state
            btn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoader.classList.add('hidden');
        }
    }
</script>
