<x-app-layout>
    <x-slot name="header">
        {{ __('Backup Exchange Rates') }}
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
                        <h2 class="text-base font-semibold leading-7 text-gray-900">{{ __('Manual Rates') }}</h2>
                        <p class="mt-1 text-sm leading-6 text-gray-600">{{ __('Set manual exchange rates to be used when the API is unavailable.') }}</p>
                    </div>

                    <form method="POST" action="{{ route('admin.backup-rates.update') }}" class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">
                        @csrf
                        <div class="px-4 py-6 sm:p-8">
                            <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                @foreach(['SAR', 'USD', 'OMR', 'AED', 'KWD'] as $currency)
                                <div class="sm:col-span-6">
                                    <h3 class="text-sm font-medium leading-6 text-gray-900 border-b border-gray-200 pb-2 mb-4">{{ $currency }}</h3>
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div>
                                            <label for="buy_rates_{{ $currency }}" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Buy Rate') }}</label>
                                            <div class="mt-2">
                                                <input type="number" step="0.0001" name="buy_rates[{{ $currency }}]" id="buy_rates_{{ $currency }}" value="{{ $buyRates[$currency] ?? 0 }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="sell_rates_{{ $currency }}" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Sell Rate') }}</label>
                                            <div class="mt-2">
                                                <input type="number" step="0.0001" name="sell_rates[{{ $currency }}]" id="sell_rates_{{ $currency }}" value="{{ $sellRates[$currency] ?? 0 }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 py-4 sm:px-8">
                            <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">{{ __('Update Rates') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
