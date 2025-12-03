<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl gradient-text">
            {{ __('Currency Converter') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-indigo-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 page-transition">
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg mb-4 shadow-sm animate-fadeIn" role="alert">
                    <span class="block sm:inline font-medium">{{ session('error') }}</span>
                </div>
            @endif
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg mb-4 shadow-sm animate-fadeIn" role="alert">
                    <span class="block sm:inline font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Converter Card -->
            <div class="glass-card rounded-2xl p-8 mb-8 hover-lift gradient-border">
                <div class="max-w-xl mx-auto">
                    <div class="text-center mb-6">
                        <h3 class="text-3xl font-extrabold gradient-text mb-2">{{ __('Real-time Converter') }}</h3>
                        <p class="text-gray-900 font-extrabold">{{ __('Convert currencies instantly with live rates') }}</p>
                        
                        @if(isset($lastUpdated))
                            @php
                                $dateTime = \Carbon\Carbon::parse($lastUpdated);
                                // Format based on locale
                                if (app()->getLocale() === 'ar') {
                                    $formattedDateTime = $dateTime->format('Y-m-d') . ' - ' . $dateTime->format('h:i A');
                                } else {
                                    $formattedDateTime = $dateTime->format('F j, Y') . ' - ' . $dateTime->format('h:i A');
                                }
                            @endphp
                            <div class="mt-2 flex items-center justify-center text-sm font-medium text-gray-900 font-extrabold">
                                {{ __('Last updated from API') }}: {{ $formattedDateTime }}
                            </div>
                        @else
                             <div class="mt-2 flex items-center justify-center text-sm font-medium text-gray-600">
                                {{ __('Last updated from API') }}: {{ __('Never') }}
                            </div>
                        @endif
                    </div>
                    
                    <form id="converter-form" class="space-y-8" onsubmit="return false;">
                        @csrf
                        <div>
                            <label for="amount" class="label-robust">{{ __('Amount') }}</label>
                            <input type="text" inputmode="decimal" name="amount" id="amount" value="1" 
                                   class="input-robust" required
                                   oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ','); convert()">
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label for="from" class="label-robust">{{ __('From') }}</label>
                                <select name="from" id="from" class="input-robust">
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency }}" {{ $currency == 'USD' ? 'selected' : '' }}>{{ __($currency) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="to" class="label-robust">{{ __('To') }}</label>
                                <select name="to" id="to" class="input-robust">
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency }}" {{ $currency == 'SAR' ? 'selected' : '' }}>{{ __($currency) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="bg-gradient-primary rounded-2xl p-8 text-center relative overflow-hidden shadow-2xl transform transition-all hover:scale-[1.02]">
                            <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-10 rounded-full -mr-20 -mt-20 blur-2xl"></div>
                            <div class="absolute bottom-0 left-0 w-32 h-32 bg-indigo-900 opacity-20 rounded-full -ml-16 -mb-16 blur-xl"></div>
                            
                            <p class="text-indigo-100 font-medium text-lg mb-2 uppercase tracking-wider">{{ __('CONVERTED AMOUNT') }}</p>
                            <div class="relative">
                                <!-- Loading Spinner -->
                                <div id="converter-loader" class="hidden absolute inset-0 flex items-center justify-center">
                                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-white border-t-transparent"></div>
                                </div>
                                <p class="text-4xl md:text-6xl font-black text-white count-up tracking-tight drop-shadow-md break-all leading-tight" id="result">---</p>
                            </div>
                            <p class="text-indigo-200 mt-3 font-medium text-lg" id="rate-info"></p>
                            
                            @auth
                                <button type="button" onclick="saveFavorite()" class="absolute top-4 right-4 text-white/80 hover:text-white transition-colors p-2 rounded-full hover:bg-white/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                </button>
                            @endauth
                        </div>
                    </form>
                    
                    @auth
                    <form id="favorite-form" action="{{ route('favorites.store') }}" method="POST" class="hidden">
                        @csrf
                        <input type="hidden" name="base_currency" id="fav-base">
                        <input type="hidden" name="target_currency" id="fav-target">
                        <input type="hidden" name="amount" id="fav-amount">
                        <input type="hidden" name="converted_amount" id="fav-converted-amount">
                        <input type="hidden" name="label" id="fav-label" value="My Conversionس">
                    </form>
                    @endauth
                    
                    <!-- Save Conversion Modal -->
                    @auth
                    <div id="save-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
                        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full transform transition-all scale-95 modal-content">
                            <!-- Modal Header -->
                            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-3xl p-6">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-2xl font-bold text-white flex items-center">
                                        <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ __('Save Conversion') }}
                                    </h3>
                                    <button onclick="closeModal()" class="text-white/80 hover:text-white transition-colors p-2 rounded-full hover:bg-white/10">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Modal Body -->
                            <div class="p-6 space-y-6">
                                <!-- Conversion Details Card -->
                                <div class="bg-gradient-to-br from-gray-50 to-indigo-50 rounded-2xl p-5 border-2 border-indigo-100">
                                    <p class="text-sm font-semibold text-gray-600 mb-4 uppercase tracking-wide">{{ __('Conversion Details') }}</p>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">{{ __('Amount') }}:</span>
                                            <span class="text-gray-900 font-bold text-lg" id="modal-amount">-</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">{{ __('From') }}:</span>
                                            <span class="text-gray-900 font-bold" id="modal-from">-</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">{{ __('To') }}:</span>
                                            <span class="text-gray-900 font-bold" id="modal-to">-</span>
                                        </div>
                                        <div class="h-px bg-indigo-200 my-2"></div>
                                        <div class="flex justify-between items-center bg-white rounded-xl p-3">
                                            <span class="text-indigo-600 font-semibold">{{ __('Converted Amount') }}:</span>
                                            <span class="text-indigo-600 font-bold text-xl" id="modal-result">-</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Label Input -->
                                <div>
                                    <label for="modal-label-input" class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ __('Conversion Label') }}
                                        <span class="text-gray-400 font-normal">({{ __('optional') }})</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="modal-label-input" 
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all outline-none text-gray-900 font-medium"
                                        placeholder="{{ __('e.g., Monthly Salary') }}"
                                    >
                                    <p class="mt-2 text-xs text-gray-500">{{ __('Give this conversion a label to easily find it later.') }}</p>
                                </div>
                            </div>
                            
                            <!-- Modal Footer -->
                            <div class="px-6 pb-6 flex gap-3">
                                <button 
                                    onclick="closeModal()" 
                                    class="flex-1 px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-all transform hover:scale-105">
                                    {{ __('Close') }}
                                </button>
                                <button 
                                    onclick="submitFavorite()" 
                                    class="flex-1 px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold hover:from-indigo-700 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>

            <!-- Multi-Currency Comparison -->
            <div class="glass-card rounded-2xl p-8 mb-8 hover-lift gradient-border">
                <div class="text-center mb-6">
                    <h3 class="text-2xl font-bold gradient-text mb-2">{{ __('Multi-Currency Comparison') }}</h3>
                    <!-- <p class="text-gray-600">{{ __('See how :currency  compares to other currencies ', ['currency ' => '']) }}<span id="comparison-base-display" class="font-bold">USD</span></p> -->
                </div>
                
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-hidden rounded-xl shadow-lg border-2 border-gray-100">
                    <table class="min-w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-8 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider">{{ __('CURRENCY') }}</th>
                                <th class="px-8 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider">{{ __('Rate') }} (1)</th>
                                <th class="px-8 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider">{{ __('Converted') }} (<span class="amount-display">1</span> )</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y-2 divide-gray-100" id="comparison-table-body">
                            <!-- Populated by JS -->
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td colspan="3" class="px-8 py-5 text-center text-gray-500">{{ __('Loading comparison rates...') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden space-y-4" id="comparison-cards-container">
                    <div class="text-center text-gray-500 py-4">{{ __('Loading comparison rates...') }}</div>
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function initCurrencyConverter() {
            const form = document.getElementById('converter-form');
            const amountInput = document.getElementById('amount');
            const fromSelect = document.getElementById('from');
            const toSelect = document.getElementById('to');
            const resultDisplay = document.getElementById('result');
            const rateInfoDisplay = document.getElementById('rate-info');
            const comparisonTableBody = document.getElementById('comparison-table-body');
            const comparisonBaseDisplay = document.getElementById('comparison-base-display');
            const baseCurrencyCodes = document.querySelectorAll('.base-currency-code');
            const amountDisplays = document.querySelectorAll('.amount-display');
            
            let chartInstance = null;
            let currentChartDays = 30;
            let currentRequestId = 0; // To track the latest request

            // Currency name translations
            const currencyNames = {
                'en': {
                    'USD': 'USD',
                    'SAR': 'SAR',
                    'YER': 'YER',
                    'OMR': 'OMR',
                    'AED': 'AED',
                    'KWD': 'KWD'
                },
                'ar': {
                    'USD': 'دولار امريكي',
                    'SAR': 'ريال سعودي',
                    'YER': 'ريال يمني',
                    'OMR': 'ريال عماني',
                    'AED': 'درهم اماراتي',
                    'KWD': 'دينار كويتي'
                }
            };

            // Get current locale
            const currentLocale = '{{ app()->getLocale() }}';

            // Function to get currency name
            function getCurrencyName(code) {
                return currencyNames[currentLocale][code] || code;
            }

            // Function to pluralize currency names in Arabic
            function formatCurrencyDisplay(amount, currencyCode, decimals = 2) {
                const currencyName = getCurrencyName(currencyCode);
                const amountNum = parseFloat(amount);
                
                // Format with commas (e.g., 1,234.56 or 1,234)
                const formattedAmount = amountNum.toLocaleString('en-US', {
                    maximumFractionDigits: decimals,
                    minimumFractionDigits: decimals
                });

                return `${formattedAmount} ${currencyName}`;
            }

            // Prevent form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                return false;
            });

            function convert() {
                // Remove commas for calculation
                const rawAmount = amountInput.value.replace(/,/g, '');
                const amount = rawAmount;
                const from = fromSelect.value;
                const to = toSelect.value;

                if (amount === '' || amount < 0) return;

                // Increment request ID
                const requestId = ++currentRequestId;

                // Show loading state
                const converterLoader = document.getElementById('converter-loader');
                converterLoader.classList.remove('hidden');
                resultDisplay.style.opacity = '0.3';
                rateInfoDisplay.textContent = '{{ __('Converting') }}';

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
                    // Check if this is the latest request
                    if (requestId !== currentRequestId) {
                        return; // Ignore old requests
                    }

                    // Hide loading state
                    converterLoader.classList.add('hidden');
                    resultDisplay.style.opacity = '1';

                    if (data.success) {
                        // Always use 0 decimals for converted amount as requested
                        const decimals = 0;
                        
                        // Display converted amount with full currency name
                        resultDisplay.textContent = formatCurrencyDisplay(data.result, to, decimals);
                        
                        // Display rate with full currency names
                        const fromName = getCurrencyName(from);
                        const toName = getCurrencyName(to);
                        rateInfoDisplay.textContent = `1 ${fromName} = ${data.rate} ${toName}`;
                        
                        updateChart(from, to, currentChartDays);
                        updateComparisonTable(from, amount);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (requestId === currentRequestId) {
                        converterLoader.classList.add('hidden');
                        resultDisplay.style.opacity = '1';
                        rateInfoDisplay.textContent = '{{ __('Error converting') }}';
                    }
                });
            }

            function updateComparisonTable(base, amount) {
                // Update labels with currency names
                if (comparisonBaseDisplay) comparisonBaseDisplay.textContent = getCurrencyName(base);
                baseCurrencyCodes.forEach(el => el.textContent = getCurrencyName(base));
                
                const formattedInputAmount = parseFloat(amount).toLocaleString('en-US', {
                    maximumFractionDigits: 2
                });
                amountDisplays.forEach(el => el.textContent = formattedInputAmount);

                // Show loading message
                comparisonTableBody.innerHTML = `
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td colspan="3" class="px-8 py-5 text-center text-gray-500">
                            <div class="flex items-center justify-center space-x-2">
                                <div class="animate-spin rounded-full h-5 w-5 border-2 border-indigo-600 border-t-transparent"></div>
                                <span>{{ __('Loading comparison rates...') }}</span>
                            </div>
                        </td>
                    </tr>
                `;

                fetch(`{{ route('currency.comparison') }}?base=${base}&amount=${amount}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Comparison data:', data);
                    if (data.success) {
                        // Clear table and cards
                        comparisonTableBody.innerHTML = '';
                        const cardsContainer = document.getElementById('comparison-cards-container');
                        if (cardsContainer) cardsContainer.innerHTML = '';
                        
                        // Load all rows at once (no progressive loading)
                        data.comparisons.forEach((item) => {
                                const currencyName = getCurrencyName(item.currency);
                                // Format amount: always use 0 decimals (integers) as requested
                                const formattedAmount = parseFloat(item.amount).toLocaleString('en-US', {
                                    maximumFractionDigits: 0,
                                    minimumFractionDigits: 0
                                });
                                
                                // Add Row to Desktop Table
                                const row = document.createElement('tr');
                                row.className = 'hover:bg-gray-50 transition-colors duration-150';
                                row.innerHTML = `
                                    <td class="px-8 py-5 whitespace-nowrap font-bold text-gray-900 text-base text-center">${currencyName}</td>
                                    <td class="px-8 py-5 whitespace-nowrap text-gray-600 text-base text-center">${item.rate}</td>
                                    <td class="px-8 py-5 whitespace-nowrap text-indigo-600 font-bold text-lg text-center">${formattedAmount}</td>
                                `;
                                comparisonTableBody.appendChild(row);

                                // Add Card to Mobile View
                                if (cardsContainer) {
                                    const card = document.createElement('div');
                                    card.className = 'bg-white p-4 rounded-xl shadow-sm border border-gray-100';
                                    card.innerHTML = `
                                        <div class="flex flex-wrap justify-between items-end mb-2 gap-x-2">
                                            <span class="font-bold text-gray-900 whitespace-nowrap">${currencyName}</span>
                                            <span class="text-indigo-600 font-bold text-lg break-all text-right flex-1 min-w-[50%]">${formattedAmount}</span>
                                        </div>
                                        <div class="text-sm text-gray-500 flex justify-between border-t border-gray-100 pt-2 mt-1">
                                            <span>{{ __('Rate') }} (1):</span>
                                            <span>${item.rate}</span>
                                        </div>
                                    `;
                                    cardsContainer.appendChild(card);
                                }
                        });
                    } else {
                        console.error('Comparison failed:', data);
                        comparisonTableBody.innerHTML = '<tr><td colspan="3" class="px-6 py-4 text-center text-red-500">Failed to load comparison rates</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching comparison:', error);
                    comparisonTableBody.innerHTML = '<tr><td colspan="3" class="px-6 py-4 text-center text-red-500">Error loading rates</td></tr>';
                });
            }

            window.updateChartRange = function(days) {
                currentChartDays = days;
                
                // Update active button state
                document.querySelectorAll('.chart-range-btn').forEach(btn => {
                    if (parseInt(btn.dataset.days) === days) {
                        btn.classList.remove('bg-indigo-100', 'text-indigo-700');
                        btn.classList.add('bg-indigo-600', 'text-white');
                    } else {
                        btn.classList.add('bg-indigo-100', 'text-indigo-700');
                        btn.classList.remove('bg-indigo-600', 'text-white');
                    }
                });

                const from = fromSelect.value;
                const to = toSelect.value;
                updateChart(from, to, days);
            };

            function updateChart(from, to, days = 30) {
                fetch(`{{ route('currency.history') }}?from=${from}&to=${to}&days=${days}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.rates.length > 0) {
                        const labels = data.rates.map(rate => rate.date);
                        const values = data.rates.map(rate => rate.rate_value);
                        
                        const ctx = document.getElementById('historyChart').getContext('2d');
                        
                        if (chartInstance) {
                            chartInstance.destroy();
                        }

                        chartInstance = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: `${from} to ${to} Exchange Rate`,
                                    data: values,
                                    borderColor: 'rgb(79, 70, 229)',
                                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                    tension: 0.1,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: false
                                    }
                                }
                            }
                        });
                    }
                });
            }

            function saveFavorite() {
                const amount = amountInput.value.replace(/,/g, '');  // Remove commas for saving
                const from = fromSelect.value;
                const to = toSelect.value;
                
                // Extract numeric value from result display (remove commas and currency name)
                const resultText = resultDisplay.textContent;
                const convertedAmount = parseFloat(resultText.replace(/[^0-9.]/g, ''));

                // Store data in hidden form fields
                document.getElementById('fav-amount').value = amount;
                document.getElementById('fav-base').value = from;
                document.getElementById('fav-target').value = to;
                document.getElementById('fav-converted-amount').value = convertedAmount;
                
                // Populate modal with conversion details
                document.getElementById('modal-amount').textContent = amountInput.value + ' ' + getCurrencyName(from);
                document.getElementById('modal-from').textContent = getCurrencyName(from);
                document.getElementById('modal-to').textContent = getCurrencyName(to);
                document.getElementById('modal-result').textContent = resultDisplay.textContent;
                
                // Clear previous label input
                document.getElementById('modal-label-input').value = '';
                
                // Show modal
                const modal = document.getElementById('save-modal');
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.querySelector('.modal-content').classList.remove('scale-95');
                    modal.querySelector('.modal-content').classList.add('scale-100');
                }, 10);
            }
            
            function closeModal() {
                const modal = document.getElementById('save-modal');
                modal.querySelector('.modal-content').classList.remove('scale-100');
                modal.querySelector('.modal-content').classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 150);
            }
            
            function submitFavorite() {
                const label = document.getElementById('modal-label-input').value.trim();
                document.getElementById('fav-label').value = label || '{{ __('My Conversion') }}';
                document.getElementById('favorite-form').submit();
            }
            
            // Expose to global scope
            window.saveFavorite = saveFavorite;
            window.closeModal = closeModal;
            window.submitFavorite = submitFavorite;

            // Debounce function to limit API calls
            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            const debouncedConvert = debounce(convert, 300);

            // Prevent default on input events
            amountInput.addEventListener('input', function(e) {
                e.preventDefault();
                debouncedConvert();
            });
            
            fromSelect.addEventListener('change', function(e) {
                e.preventDefault();
                convert();
            });
            
            toSelect.addEventListener('change', function(e) {
                e.preventDefault();
                convert();
            });

            // Initial conversion
            convert();
        }

        // Make the initialization function globally available for Turbo
        window.initializeCurrencyConverter = initCurrencyConverter;

        // Run on initial load and on Turbo visits
        document.addEventListener('DOMContentLoaded', initCurrencyConverter);
        document.addEventListener('turbo:load', initCurrencyConverter);
        document.addEventListener('turbo:render', initCurrencyConverter);
    </script>
    @endpush
</x-app-layout>
