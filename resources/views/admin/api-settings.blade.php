<x-app-layout>
    <x-slot name="header">
        {{ __('API Configuration') }}
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
            <div class="px-4 py-6 sm:p-8">
                <div class="grid grid-cols-1 gap-x-8 gap-y-8 md:grid-cols-3">
                    <div class="px-4 sm:px-0">
                        <h2 class="text-base font-semibold leading-7 text-gray-900">{{ __('General Settings') }}</h2>
                        <p class="mt-1 text-sm leading-6 text-gray-600">{{ __('Configure the external API provider for exchange rates.') }}</p>
                    </div>

                    <form method="POST" action="{{ route('admin.api-settings.update') }}" class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">
                        @csrf
                        <div class="px-4 py-6 sm:p-8">
                            <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <!-- API Provider -->
                                <div class="sm:col-span-4">
                                    <label for="api_provider" class="block text-sm font-medium leading-6 text-gray-900">{{ __('API Provider') }}</label>
                                    <div class="mt-2">
                                        <select id="api_provider" name="api_provider" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                            <option value="exchangerate-api" {{ ($settings['api_provider'] ?? '') == 'exchangerate-api' ? 'selected' : '' }}>ExchangeRate-API</option>
                                            <option value="freecurrencyapi" {{ ($settings['api_provider'] ?? '') == 'freecurrencyapi' ? 'selected' : '' }}>FreeCurrencyAPI</option>
                                            <option value="fixer" {{ ($settings['api_provider'] ?? '') == 'fixer' ? 'selected' : '' }}>Fixer.io</option>
                                            <option value="openexchangerates" {{ ($settings['api_provider'] ?? '') == 'openexchangerates' ? 'selected' : '' }}>Open Exchange Rates</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- API Key -->
                                <div class="sm:col-span-4">
                                    <label for="api_key" class="block text-sm font-medium leading-6 text-gray-900">{{ __('API Key') }}</label>
                                    <div class="mt-2">
                                        <input type="text" name="api_key" id="api_key" value="{{ $settings['api_key'] ?? '' }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                    </div>
                                </div>

                                <!-- Cache Duration -->
                                <div class="sm:col-span-3">
                                    <label for="cache_duration" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Cache Duration (Minutes)') }}</label>
                                    <div class="mt-2">
                                        <input type="number" name="cache_duration" id="cache_duration" value="{{ $settings['cache_duration'] ?? 60 }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                    </div>
                                </div>

                                <!-- API Enabled -->
                                <div class="sm:col-span-6">
                                    <div class="relative flex gap-x-3">
                                        <div class="flex h-6 items-center">
                                            <input type="hidden" name="api_enabled" value="0">
                                            <input id="api_enabled" name="api_enabled" value="1" type="checkbox" {{ ($settings['api_enabled'] ?? 'false') === 'true' ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                                        </div>
                                        <div class="text-sm leading-6">
                                            <label for="api_enabled" class="font-medium text-gray-900">{{ __('Enable External API') }}</label>
                                            <p class="text-gray-500">{{ __('If disabled, the system will use backup rates only.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 py-4 sm:px-8">
                            <button type="button" id="test-connection" class="text-sm font-semibold leading-6 text-gray-900">{{ __('Test Connection') }}</button>
                            <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">{{ __('Save Settings') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Test Result Area -->
        <div id="test-result" class="hidden rounded-md bg-white p-4 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex">
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900" id="test-result-content"></p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('test-connection').addEventListener('click', function() {
            const resultDiv = document.getElementById('test-result');
            const resultContent = document.getElementById('test-result-content');
            
            resultDiv.classList.remove('hidden');
            resultDiv.className = 'rounded-md bg-gray-50 p-4 shadow-sm ring-1 ring-gray-900/5 mt-4';
            resultContent.innerHTML = '<span class="flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Testing connection...</span>';

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
                    resultDiv.className = 'rounded-md bg-green-50 p-4 shadow-sm ring-1 ring-green-600/20 mt-4';
                    resultContent.className = 'text-sm font-medium text-green-700';
                    resultContent.innerHTML = `<strong>Success:</strong> ${data.message}<br>Sample Rate (USD->SAR): ${data.sample_rate}`;
                } else {
                    resultDiv.className = 'rounded-md bg-red-50 p-4 shadow-sm ring-1 ring-red-600/20 mt-4';
                    resultContent.className = 'text-sm font-medium text-red-700';
                    resultContent.innerHTML = `<strong>Error:</strong> ${data.message}`;
                }
            })
            .catch(error => {
                resultDiv.className = 'rounded-md bg-red-50 p-4 shadow-sm ring-1 ring-red-600/20 mt-4';
                resultContent.className = 'text-sm font-medium text-red-700';
                resultContent.innerHTML = `<strong>Error:</strong> ${error.message}`;
            });
        });
    </script>
    @endpush
</x-app-layout>
