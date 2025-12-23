<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                ⚙️ {{ __('visitors.notification_settings') }}
            </h2>
            <a href="{{ route('admin.visitors.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← {{ __('visitors.back_to_analytics') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.visitors.settings.update') }}" method="POST">
                @csrf
                
                <!-- Notification Settings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium mb-4 flex items-center gap-2">
                        🔔 {{ __('visitors.notification_settings') }}
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- Enable Notifications -->
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="notifications_enabled" id="notifications_enabled" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                   {{ $settings->notifications_enabled ? 'checked' : '' }}>
                            <label for="notifications_enabled" class="text-gray-700">
                                {{ __('visitors.enable_notifications') }}
                            </label>
                        </div>

                        <!-- Notification Interval -->
                        <div>
                            <label for="notification_interval_hours" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('visitors.notification_interval') }}
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="notification_interval_hours" id="notification_interval_hours"
                                       value="{{ $settings->notification_interval_hours }}"
                                       min="1" max="720"
                                       class="w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="text-gray-500">{{ __('visitors.hours') }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ __('visitors.interval_hint') }}</p>
                        </div>

                        <!-- Report Language -->
                        <div>
                            <label for="report_language" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('visitors.report_language') }}
                            </label>
                            <select name="report_language" id="report_language"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="auto" {{ $settings->report_language === 'auto' ? 'selected' : '' }}>
                                    {{ __('visitors.auto_language') }}
                                </option>
                                <option value="ar" {{ $settings->report_language === 'ar' ? 'selected' : '' }}>
                                    العربية
                                </option>
                                <option value="en" {{ $settings->report_language === 'en' ? 'selected' : '' }}>
                                    English
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Telegram Settings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium mb-4 flex items-center gap-2">
                        📱 {{ __('visitors.telegram_settings') }}
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="telegram_bot_token" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('visitors.bot_token') }}
                            </label>
                            <input type="text" name="telegram_bot_token" id="telegram_bot_token"
                                   value="{{ $settings->telegram_bot_token }}"
                                   placeholder="123456789:ABCdefGHIjklMNOpqrsTUVwxyz"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm">
                        </div>

                        <div>
                            <label for="telegram_chat_id" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('visitors.chat_id') }}
                            </label>
                            <input type="text" name="telegram_chat_id" id="telegram_chat_id"
                                   value="{{ $settings->telegram_chat_id }}"
                                   placeholder="1234567890"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm">
                        </div>
                    </div>
                </div>

                <!-- n8n Settings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium mb-4 flex items-center gap-2">
                        🔗 {{ __('visitors.n8n_settings') }}
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="use_n8n" id="use_n8n" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                   {{ $settings->use_n8n ? 'checked' : '' }}>
                            <label for="use_n8n" class="text-gray-700">
                                {{ __('visitors.use_n8n') }}
                            </label>
                        </div>

                        <div>
                            <label for="n8n_webhook_url" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('visitors.webhook_url') }}
                            </label>
                            <input type="url" name="n8n_webhook_url" id="n8n_webhook_url"
                                   value="{{ $settings->n8n_webhook_url }}"
                                   placeholder="https://n8n.example.com/webhook/..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm">
                            <p class="text-xs text-gray-500 mt-1">{{ __('visitors.n8n_hint') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Smart Alerts -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium mb-4 flex items-center gap-2">
                        🚨 {{ __('visitors.smart_alerts') }}
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="smart_alerts_enabled" id="smart_alerts_enabled" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                   {{ $settings->smart_alerts_enabled ? 'checked' : '' }}>
                            <label for="smart_alerts_enabled" class="text-gray-700">
                                {{ __('visitors.enable_smart_alerts') }}
                            </label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="spike_threshold_percent" class="block text-sm font-medium text-gray-700 mb-1">
                                    🔥 {{ __('visitors.spike_threshold') }}
                                </label>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="spike_threshold_percent" id="spike_threshold_percent"
                                           value="{{ $settings->spike_threshold_percent }}"
                                           min="50" max="1000"
                                           class="w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <span class="text-gray-500">%</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ __('visitors.spike_hint') }}</p>
                            </div>

                            <div>
                                <label for="drop_threshold_percent" class="block text-sm font-medium text-gray-700 mb-1">
                                    ⚠️ {{ __('visitors.drop_threshold') }}
                                </label>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="drop_threshold_percent" id="drop_threshold_percent"
                                           value="{{ $settings->drop_threshold_percent }}"
                                           min="10" max="100"
                                           class="w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <span class="text-gray-500">%</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ __('visitors.drop_hint') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium mb-4 flex items-center gap-2">
                        📊 {{ __('visitors.report_statistics') }}
                    </h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $settings->total_reports_sent }}</div>
                            <div class="text-sm text-gray-500">{{ __('visitors.reports_sent') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($settings->total_visitors_reported) }}</div>
                            <div class="text-sm text-gray-500">{{ __('visitors.visitors_reported') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">
                                {{ $settings->last_notification_sent_at ? $settings->last_notification_sent_at->diffForHumans() : __('visitors.never') }}
                            </div>
                            <div class="text-sm text-gray-500">{{ __('visitors.last_report') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        💾 {{ __('visitors.save_settings') }}
                    </button>
                    
                    <button type="button" onclick="document.getElementById('test-form').submit()" 
                            class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700">
                        📤 {{ __('visitors.send_test') }}
                    </button>
                </div>
            </form>

            <!-- Test Notification Form (separate) -->
            <form id="test-form" action="{{ route('admin.visitors.test-notification') }}" method="POST" class="hidden">
                @csrf
            </form>

        </div>
    </div>
</x-app-layout>
