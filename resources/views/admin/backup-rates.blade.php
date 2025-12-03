<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Backup Exchange Rates') }}
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
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('These rates will be used') }}
                    </p>
                    <p class="mb-6 text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Base currency is YER.') }}
                    </p>

                    <form method="POST" action="{{ route('admin.backup-rates.update') }}">
                        @csrf

                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Currency') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Buy Rate') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Sell Rate') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach(['SAR', 'USD', 'OMR', 'AED', 'KWD'] as $currency)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 text-center">
                                                {{ $currency }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="number" 
                                                       step="0.01" 
                                                       name="buy_rates[{{ $currency }}]" 
                                                       id="buy_rate_{{ $currency }}"
                                                       value="{{ $buyRates[$currency] ?? 0 }}"
                                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300 sm:text-sm text-center">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="number" 
                                                       step="0.01" 
                                                       name="sell_rates[{{ $currency }}]" 
                                                       id="sell_rate_{{ $currency }}"
                                                       value="{{ $sellRates[$currency] ?? 0 }}"
                                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300 sm:text-sm text-center">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Cards -->
                        <div class="md:hidden space-y-6">
                            @foreach(['SAR', 'USD', 'OMR', 'AED', 'KWD'] as $currency)
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-gray-600">
                                    <div class="flex items-center justify-between mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $currency }}</h3>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label for="mobile_buy_rate_{{ $currency }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                {{ __('Buy Rate') }}
                                            </label>
                                            <input type="number" 
                                                   step="0.01" 
                                                   name="buy_rates[{{ $currency }}]" 
                                                   id="mobile_buy_rate_{{ $currency }}"
                                                   value="{{ $buyRates[$currency] ?? 0 }}"
                                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300 sm:text-sm">
                                        </div>
                                        
                                        <div>
                                            <label for="mobile_sell_rate_{{ $currency }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                {{ __('Sell Rate') }}
                                            </label>
                                            <input type="number" 
                                                   step="0.01" 
                                                   name="sell_rates[{{ $currency }}]" 
                                                   id="mobile_sell_rate_{{ $currency }}"
                                                   value="{{ $sellRates[$currency] ?? 0 }}"
                                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300 sm:text-sm">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex items-center gap-4">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Save Backup Rates') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
