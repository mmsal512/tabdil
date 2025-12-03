<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('API Configuration') }}
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← {{ __('Back to Dashboard') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('General Settings') }}</h3>
                    
                    <form method="POST" action="{{ route('admin.api-settings.update') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- API Provider -->
                            <div>
                                <label for="api_provider" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('API Provider') }}</label>
                                <select id="api_provider" name="api_provider" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300 sm:text-sm">
                                    <option value="exchangerate-api" {{ ($settings['api_provider'] ?? '') == 'exchangerate-api' ? 'selected' : '' }}>ExchangeRate-API</option>
                                    <option value="freecurrencyapi" {{ ($settings['api_provider'] ?? '') == 'freecurrencyapi' ? 'selected' : '' }}>FreeCurrencyAPI</option>
                                    <option value="fixer" {{ ($settings['api_provider'] ?? '') == 'fixer' ? 'selected' : '' }}>Fixer.io</option>
                                </select>
                            </div>

                            <!-- API Key -->
                            <div>
                                <label for="api_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('API Key') }}</label>
                                <input type="text" name="api_key" id="api_key" value="{{ $settings['api_key'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300 sm:text-sm">
                            </div>

                            <!-- Cache Duration -->
                            <div>
                                <label for="cache_duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Cache Duration (Minutes)') }}</label>
                                <input type="number" name="cache_duration" id="cache_duration" value="{{ $settings['cache_duration'] ?? 60 }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300 sm:text-sm">
                            </div>

                            <!-- API Enabled -->
                            <div class="flex items-center mt-6">
                                <input type="hidden" name="api_enabled" value="0">
                                <input type="checkbox" name="api_enabled" id="api_enabled" value="1" {{ ($settings['api_enabled'] ?? 'false') === 'true' ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="api_enabled" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('Enable External API') }}
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-4">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Save Settings') }}
                            </button>
                            
                            <button type="button" id="test-connection" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Test Connection') }}
                            </button>
                        </div>
                    </form>

                    <div id="test-result" class="mt-4 hidden">
                        <div class="p-4 rounded-md"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('test-connection').addEventListener('click', function() {
            const resultDiv = document.getElementById('test-result');
            const resultContent = resultDiv.querySelector('div');
            
            resultDiv.classList.remove('hidden');
            resultContent.className = 'p-4 rounded-md bg-gray-100 text-gray-700';
            resultContent.innerHTML = 'Testing connection...';

            fetch('{{ route('admin.test-api-connection') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultContent.className = 'p-4 rounded-md bg-green-100 text-green-700 border border-green-400';
                    resultContent.innerHTML = `<strong>Success:</strong> ${data.message}<br>Sample Rate (USD->SAR): ${data.sample_rate}`;
                } else {
                    resultContent.className = 'p-4 rounded-md bg-red-100 text-red-700 border border-red-400';
                    resultContent.innerHTML = `<strong>Error:</strong> ${data.message}`;
                }
            })
            .catch(error => {
                resultContent.className = 'p-4 rounded-md bg-red-100 text-red-700 border border-red-400';
                resultContent.innerHTML = `<strong>Error:</strong> ${error.message}`;
            });
        });
    </script>
</x-app-layout>
