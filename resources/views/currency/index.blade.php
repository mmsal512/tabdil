<x-app-layout>
    <x-slot name="header">
        {{ __('Currency Converter') }}
    </x-slot>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="mx-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Converter Card -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">
                <div class="px-4 py-6 sm:p-8">
                    <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <div class="sm:col-span-6">
                            <h2 class="text-base font-semibold leading-7 text-gray-900">{{ __('Real-time Converter') }}</h2>
                            <p class="mt-1 text-sm leading-6 text-gray-600">{{ __('Convert currencies instantly with live rates.') }}</p>
                            
                            @if(isset($lastUpdated))
                                @php
                                    $dateTime = \Carbon\Carbon::parse($lastUpdated);
                                    $formattedDateTime = app()->getLocale() === 'ar' 
                                        ? $dateTime->format('Y-m-d') . ' - ' . $dateTime->format('h:i A')
                                        : $dateTime->format('F j, Y') . ' - ' . $dateTime->format('h:i A');
                                @endphp
                                <div class="mt-2 flex items-center text-xs font-medium text-primary-600">
                                    <svg class="mr-1.5 h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                    {{ __('Last updated') }}: {{ $formattedDateTime }}
                                </div>
                            @endif
                        </div>

                        <form id="converter-form" class="sm:col-span-6 space-y-6" onsubmit="return false;">
                            @csrf
                            
                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Amount') }}</label>
                                <div class="relative mt-2 rounded-md shadow-sm">
                                    <input type="text" name="amount" id="amount" value="1" 
                                        class="block w-full rounded-md border-0 py-3 pl-4 pr-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-lg sm:leading-6 font-semibold" 
                                        placeholder="0.00"
                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ','); convert()">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <!-- From Currency -->
                                <div>
                                    <label for="from" class="block text-sm font-medium leading-6 text-gray-900">{{ __('From') }}</label>
                                    <select id="from" name="from" class="mt-2 block w-full rounded-md border-0 py-3 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                                        @foreach($currencies as $currency)
                                            <option value="{{ $currency }}" {{ $currency == 'USD' ? 'selected' : '' }}>{{ __($currency) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- To Currency -->
                                <div>
                                    <label for="to" class="block text-sm font-medium leading-6 text-gray-900">{{ __('To') }}</label>
                                    <select id="to" name="to" class="mt-2 block w-full rounded-md border-0 py-3 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                                        @foreach($currencies as $currency)
                                            <option value="{{ $currency }}" {{ $currency == 'SAR' ? 'selected' : '' }}>{{ __($currency) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Result Card -->
                            <div class="relative overflow-hidden rounded-xl bg-primary-600 p-8 text-center shadow-lg">
                                <div class="absolute inset-0 bg-gradient-to-br from-primary-500 to-primary-700"></div>
                                <div class="absolute -top-24 -right-24 h-64 w-64 rounded-full bg-primary-400 opacity-20 blur-3xl"></div>
                                <div class="absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-primary-800 opacity-20 blur-3xl"></div>
                                
                                <div class="relative">
                                    <h3 class="text-sm font-medium text-primary-100 uppercase tracking-wider">{{ __('Converted Amount') }}</h3>
                                    
                                    <div class="mt-4 flex items-center justify-center">
                                        <!-- Loading Spinner -->
                                        <div id="converter-loader" class="hidden absolute">
                                            <svg class="animate-spin h-10 w-10 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                        
                                        <p class="text-4xl sm:text-5xl font-bold text-white tracking-tight break-all" id="result">---</p>
                                    </div>
                                    
                                    <p class="mt-2 text-lg text-primary-100 font-medium" id="rate-info"></p>
                                </div>

                                @auth
                                    <button type="button" onclick="saveFavorite()" class="absolute top-4 right-4 p-2 text-primary-100 hover:text-white hover:bg-white/10 rounded-full transition-colors" title="{{ __('Save Conversion') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>
                                @endauth
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Comparison Table -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                <div class="px-4 py-6 sm:p-8">
                    <h3 class="text-base font-semibold leading-7 text-gray-900 mb-6">{{ __('Multi-Currency Comparison') }}</h3>
                    
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-hidden ring-1 ring-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-center text-sm font-semibold text-gray-900 sm:pl-6">{{ __('Currency') }}</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">{{ __('Rate') }} (1)</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">{{ __('Converted') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white" id="comparison-table-body">
                                <tr>
                                    <td colspan="3" class="py-4 text-center text-sm text-gray-500">{{ __('Loading...') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden space-y-4" id="comparison-cards-container">
                        <div class="text-center text-sm text-gray-500">{{ __('Loading...') }}</div>
                    </div>
                </div>
            </div>
        </div>

            <!-- Ad / Info Placeholder -->
            <div class="bg-gradient-to-br from-indigo-900 to-purple-900 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-6 text-white overflow-hidden relative">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <div class="relative">
                    <h3 class="font-bold text-lg mb-2">{{ __('Need Help?') }}</h3>
                    <p class="text-indigo-100 text-sm mb-4">{{ __('Contact our support team for assistance with bulk conversions or API access.') }}</p>
                    <button class="text-xs font-semibold bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition-colors border border-white/20">
                        {{ __('Contact Support') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms & Modals -->
    @auth
    <form id="favorite-form" action="{{ route('favorites.store') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="base_currency" id="fav-base">
        <input type="hidden" name="target_currency" id="fav-target">
        <input type="hidden" name="amount" id="fav-amount">
        <input type="hidden" name="converted_amount" id="fav-converted-amount">
        <input type="hidden" name="label" id="fav-label" value="My Conversion">
    </form>

    <div id="save-modal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6 modal-content scale-95 transition-transform duration-200">
                    <div>
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary-100">
                            <svg class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">{{ __('Save Conversion') }}</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">{{ __('Save this conversion to your favorites for quick access later.') }}</p>
                                
                                <div class="mt-4 bg-gray-50 rounded-lg p-4 text-left">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">{{ __('From') }}:</span>
                                        <span class="font-medium text-gray-900" id="modal-amount"></span>
                                    </div>
                                    <div class="flex justify-between text-sm mt-2">
                                        <span class="text-gray-500">{{ __('To') }}:</span>
                                        <span class="font-medium text-gray-900" id="modal-result"></span>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label for="modal-label-input" class="block text-sm font-medium leading-6 text-gray-900 text-left">{{ __('Label') }} <span class="text-gray-400 font-normal">({{ __('Optional') }})</span></label>
                                    <div class="mt-2">
                                        <input type="text" id="modal-label-input" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6" placeholder="{{ __('e.g. Monthly Salary') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button type="button" onclick="submitFavorite()" class="inline-flex w-full justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 sm:col-start-2">{{ __('Save') }}</button>
                        <button type="button" onclick="closeModal()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">{{ __('Cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endauth

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Keep existing JS logic but update DOM selectors if needed
        function initCurrencyConverter() {
            const form = document.getElementById('converter-form');
            const amountInput = document.getElementById('amount');
            const fromSelect = document.getElementById('from');
            const toSelect = document.getElementById('to');
            const resultDisplay = document.getElementById('result');
            const rateInfoDisplay = document.getElementById('rate-info');
            const comparisonTableBody = document.getElementById('comparison-table-body');
            
            let chartInstance = null;
            let currentChartDays = 30;
            let currentRequestId = 0;

            const currencyNames = {
                'en': { 'USD': 'USD', 'SAR': 'SAR', 'YER': 'YER', 'OMR': 'OMR', 'AED': 'AED', 'KWD': 'KWD' },
                'ar': { 'USD': 'دولار امريكي', 'SAR': 'ريال سعودي', 'YER': 'ريال يمني', 'OMR': 'ريال عماني', 'AED': 'درهم اماراتي', 'KWD': 'دينار كويتي' }
            };

            const currentLocale = '{{ app()->getLocale() }}';

            function getCurrencyName(code) {
                return currencyNames[currentLocale][code] || code;
            }

            function formatCurrencyDisplay(amount, currencyCode, decimals = 2) {
                const currencyName = getCurrencyName(currencyCode);
                const amountNum = parseFloat(amount);
                const formattedAmount = amountNum.toLocaleString('en-US', {
                    maximumFractionDigits: decimals,
                    minimumFractionDigits: decimals
                });
                return `${formattedAmount} ${currencyName}`;
            }

            function convert() {
                const rawAmount = amountInput.value.replace(/,/g, '');
                const amount = rawAmount;
                const from = fromSelect.value;
                const to = toSelect.value;

                if (amount === '' || amount < 0) return;

                const requestId = ++currentRequestId;
                const converterLoader = document.getElementById('converter-loader');
                
                if(converterLoader) converterLoader.classList.remove('hidden');
                resultDisplay.style.opacity = '0.3';
                rateInfoDisplay.textContent = '{{ __('Converting...') }}';

                fetch('{{ route('currency.convert') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ amount, from, to })
                })
                .then(response => response.json())
                .then(data => {
                    if (requestId !== currentRequestId) return;

                    if(converterLoader) converterLoader.classList.add('hidden');
                    resultDisplay.style.opacity = '1';

                    if (data.success) {
                        resultDisplay.textContent = formatCurrencyDisplay(data.result, to, 0);
                        
                        const fromName = getCurrencyName(from);
                        const toName = getCurrencyName(to);
                        rateInfoDisplay.textContent = `1 ${fromName} = ${data.rate} ${toName}`;
                        
                        updateComparisonTable(from, amount);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (requestId === currentRequestId) {
                        if(converterLoader) converterLoader.classList.add('hidden');
                        resultDisplay.style.opacity = '1';
                        rateInfoDisplay.textContent = '{{ __('Error converting') }}';
                    }
                });
            }

            function updateComparisonTable(base, amount) {
                const formattedInputAmount = parseFloat(amount).toLocaleString('en-US', { maximumFractionDigits: 2 });
                
                comparisonTableBody.innerHTML = `
                    <tr>
                        <td colspan="3" class="py-4 text-center text-sm text-gray-500">
                            <div class="flex items-center justify-center space-x-2">
                                <div class="animate-spin rounded-full h-4 w-4 border-2 border-primary-600 border-t-transparent"></div>
                                <span>{{ __('Loading...') }}</span>
                            </div>
                        </td>
                    </tr>
                `;

                fetch(`{{ route('currency.comparison') }}?base=${base}&amount=${amount}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        comparisonTableBody.innerHTML = '';
                        const cardsContainer = document.getElementById('comparison-cards-container');
                        if (cardsContainer) cardsContainer.innerHTML = '';
                        
                        data.comparisons.forEach((item) => {
                                const currencyName = getCurrencyName(item.currency);
                                const formattedAmount = parseFloat(item.amount).toLocaleString('en-US', {
                                    maximumFractionDigits: 0,
                                    minimumFractionDigits: 0
                                });
                                
                                // Desktop Row
                                const row = document.createElement('tr');
                                row.className = 'hover:bg-gray-50 transition-colors duration-150';
                                row.innerHTML = `
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 text-center">${currencyName}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-center">${item.rate}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-bold text-primary-600 text-center">${formattedAmount}</td>
                                `;
                                comparisonTableBody.appendChild(row);

                                // Mobile Card
                                if (cardsContainer) {
                                    const card = document.createElement('div');
                                    card.className = 'bg-white p-4 rounded-lg shadow-sm border border-gray-200';
                                    card.innerHTML = `
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="font-medium text-gray-900">${currencyName}</span>
                                            <span class="text-primary-600 font-bold text-lg">${formattedAmount}</span>
                                        </div>
                                        <div class="text-xs text-gray-500 flex justify-between border-t border-gray-100 pt-2">
                                            <span>{{ __('Rate') }}:</span>
                                            <span>${item.rate}</span>
                                        </div>
                                    `;
                                    cardsContainer.appendChild(card);
                                }
                        });
                    }
                });
            }

            // Save Favorite Logic
            window.saveFavorite = function() {
                const amount = amountInput.value.replace(/,/g, '');
                const from = fromSelect.value;
                const to = toSelect.value;
                const resultText = resultDisplay.textContent;
                
                document.getElementById('fav-amount').value = amount;
                document.getElementById('fav-base').value = from;
                document.getElementById('fav-target').value = to;
                document.getElementById('fav-converted-amount').value = parseFloat(resultText.replace(/[^0-9.]/g, ''));
                
                document.getElementById('modal-amount').textContent = amountInput.value + ' ' + getCurrencyName(from);
                document.getElementById('modal-result').textContent = resultText;
                
                const modal = document.getElementById('save-modal');
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.querySelector('.modal-content').classList.remove('scale-95');
                    modal.querySelector('.modal-content').classList.add('scale-100');
                }, 10);
            }
            
            window.closeModal = function() {
                const modal = document.getElementById('save-modal');
                modal.querySelector('.modal-content').classList.remove('scale-100');
                modal.querySelector('.modal-content').classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 150);
            }
            
            window.submitFavorite = function() {
                const label = document.getElementById('modal-label-input').value.trim();
                document.getElementById('fav-label').value = label || '{{ __('My Conversion') }}';
                document.getElementById('favorite-form').submit();
            }

            // Event Listeners
            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }
            const debouncedConvert = debounce(convert, 300);

            amountInput.addEventListener('input', (e) => { e.preventDefault(); debouncedConvert(); });
            fromSelect.addEventListener('change', (e) => { e.preventDefault(); convert(); });
            toSelect.addEventListener('change', (e) => { e.preventDefault(); convert(); });

            // Initial call
            convert();
        }

        document.addEventListener('DOMContentLoaded', initCurrencyConverter);
        document.addEventListener('turbo:load', initCurrencyConverter);
    </script>
    @endpush
</x-app-layout>
