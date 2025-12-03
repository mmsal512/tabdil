<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\ExchangeRate;
use App\Models\BackupRate;

class CurrencyConversionService
{
    protected $currencies = ['SAR', 'YER', 'OMR', 'USD', 'AED', 'KWD'];
    
    /**
     * Record API failure and enable offline mode after 2 consecutive failures
     */
    protected function recordApiFailure($currency)
    {
        $failureKey = "api_failures_{$currency}";
        $offlineModeKey = "offline_mode_{$currency}";
        
        $failures = Cache::get($failureKey, 0);
        $failures++;
        
        Cache::put($failureKey, $failures, 600); // Track for 10 minutes
        
        // Enable offline mode for 5 minutes after 2 failures
        if ($failures >= 2) {
            Cache::put($offlineModeKey, true, 300); // 5 minutes
            \Log::warning("Offline mode activated for {$currency} after {$failures} failures");
        }
    }
    
    /**
     * Get API configuration from database
     */
    protected function getApiConfig()
    {
        return Cache::remember('api_config', 300, function () {
            return DB::table('api_settings')->pluck('value', 'key')->toArray();
        });
    }
    
    /**
     * Get API base URL based on provider
     */
    protected function getApiUrl($provider)
    {
        $urls = [
            'freecurrencyapi' => 'https://api.freecurrencyapi.com/v1/latest',
            'exchangerate-api' => 'https://v6.exchangerate-api.com/v6/latest/',
            'fixer' => 'http://data.fixer.io/api/latest',
        ];
        
        return $urls[$provider] ?? $urls['exchangerate-api'];
    }

    /**
     * Convert currency amount.
     *
     * @param string $from
     * @param string $to
     * @param float $amount
     * @return float
     */
    public function convert($from, $to, $amount)
    {
        // Case 1: Same currency
        if ($from === $to) {
            return $amount;
        }

        // Case A: Foreign -> Foreign (Neither is YER)
        if ($from !== 'YER' && $to !== 'YER') {
            // Try API first (which now includes DB fallback)
            return $this->convertViaApi($from, $to, $amount);
        }

        // Case B: YER -> Foreign (Selling Foreign Currency)
        // Logic: 1 YER = (1 / Sell Rate) Unit
        // Example: If Sell Rate for SAR is 428 (1 SAR = 428 YER)
        // Then 1 YER = 1/428 SAR
        // Amount (YER) * (1/SellRate) = Amount / SellRate
        if ($from === 'YER') {
            $rates = $this->getBuySellRates();
            $sellRate = $rates[$to]['sell'] ?? 0;
            
            if ($sellRate > 0) {
                // $sellRate is stored as "YER per Unit" (e.g. 428)
                // We want to convert YER to Unit.
                // Formula: Amount / Rate
                return round($amount / $sellRate, 4);
            }
            return 0;
        }

        // Case C: Foreign -> YER (Buying Foreign Currency)
        // Logic: 1 Unit = Buy Rate YER
        // Example: If Buy Rate for SAR is 425 (1 SAR = 425 YER)
        // Amount (SAR) * BuyRate = Amount * 425
        if ($to === 'YER') {
            $rates = $this->getBuySellRates();
            $buyRate = $rates[$from]['buy'] ?? 0;
            
            if ($buyRate > 0) {
                // $buyRate is stored as "YER per Unit" (e.g. 425)
                // We want to convert Unit to YER.
                // Formula: Amount * Rate
                return round($amount * $buyRate, 4);
            }
            return 0;
        }

        return 0;
    }

    /**
     * Convert currency using external API.
     */
    protected function convertViaApi($from, $to, $amount)
    {
        $apiKey = 'b79fc16303bd87c0a25c9b22';
        $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/{$from}";
        $cacheKey = "api_rates_{$from}";
        $offlineModeKey = "offline_mode_{$from}";
        $cacheDuration = 86400; // 24 hours

        try {
            // 1. Check if in Offline Mode (skip API for 5 minutes after 2 consecutive failures)
            if (Cache::has($offlineModeKey)) {
                \Log::info("Offline mode active for {$from}, using cache/DB only");
                $rates = null; // Force fallback to DB
            } else {
                // 2. Try Memory Cache first (Fastest)
                $rates = Cache::get($cacheKey);
            }
            
            if (!$rates && !Cache::has($offlineModeKey)) {
                // 3. Try Live API with 2s Timeout (Fast Failover)
                try {
                    $response = Http::timeout(2)->get($url);
                    
                    if ($response->successful()) {
                        $rates = $response->json();
                        // Cache in memory
                        Cache::put($cacheKey, $rates, $cacheDuration);
                        
                        // Clear failure counter on success
                        Cache::forget("api_failures_{$from}");
                        
                        // 4. PERSIST to Database
                        if (isset($rates['conversion_rates'])) {
                            foreach ($rates['conversion_rates'] as $target => $rate) {
                                if (in_array($target, $this->currencies)) {
                                    ExchangeRate::updateOrCreate(
                                        [
                                            'base_currency' => $from,
                                            'target_currency' => $target,
                                            'source' => 'api_backup',
                                            'type' => 'last_success'
                                        ],
                                        [
                                            'rate_value' => $rate,
                                            'timestamp' => now()
                                        ]
                                    );
                                }
                            }
                        }
                        
                        \Log::info("Successfully fetched API rates for {$from}");
                    } else {
                        $this->recordApiFailure($from);
                    }
                } catch (\Exception $e) {
                    \Log::warning("API request failed for {$from}: " . $e->getMessage());
                    $this->recordApiFailure($from);
                }
            }

            // Check if we have rates from Cache or Live API
            if ($rates && isset($rates['conversion_rates'][$to])) {
                $rate = $rates['conversion_rates'][$to];
                return round($amount * $rate, 4);
            }
            
            // 4. Fallback to Database (Persistent Last Success)
            // A. Try Direct Rate
            $backupRate = ExchangeRate::where('base_currency', $from)
                ->where('target_currency', $to)
                ->where('source', 'api_backup')
                ->where('type', 'last_success')
                ->value('rate_value');
                
            if ($backupRate) {
                \Log::info("Using persistent DB rate for {$from} -> {$to}");
                return round($amount * $backupRate, 4);
            }

            // B. Cross Rate logic removed as per requirements.
            // We strictly rely on Direct Rate from API Backup.
            
            \Log::warning("Rate for {$to} not found in API, Cache, or DB Backup for {$from}");
        } catch (\Exception $e) {
            \Log::error("API conversion failed: " . $e->getMessage());
        }

        return 0;
    }

    /**
     * Get exchange rates for a base currency.
     *
     * @param string $base
     * @return array
     */
    public function getRates($base)
    {
        // For backward compatibility and API usage, we might still need this.
        // But for our strict logic, we primarily use getBuySellRates inside convert.
        // However, the frontend might call this for display.
        
        return Cache::remember("exchange_rates_{$base}", 3600, function () use ($base) {
            return $this->fetchRatesFromApi($base);
        });
    }

    /**
     * Get structured Buy/Sell rates for all currencies relative to YER.
     * Returns ['SAR' => ['buy' => 0.00235, 'sell' => 0.00233], ...]
     */
    public function getBuySellRates()
    {
        return Cache::remember("buy_sell_rates_yer", 3600, function () {
            $rates = [];
            
            // Fetch from new backup_rates table
            $backupRates = BackupRate::all();
                
            foreach ($backupRates as $rate) {
                $rates[$rate->currency] = [
                    'buy' => (float) $rate->buy_rate,
                    'sell' => (float) $rate->sell_rate,
                ];
            }
            
            return $rates;
        });
    }

    /**
     * Fetch rates from external API.
     *
     * @param string $base
     * @return array
     */
    protected function fetchRatesFromApi($base)
    {
        try {
            // Get API configuration from database
            $config = $this->getApiConfig();
            
            // Check if API is enabled
            if (($config['api_enabled'] ?? 'false') !== 'true') {
                \Log::info("API is disabled, using fallback rates for {$base}");
                return $this->getFallbackRates($base);
            }
            
            $provider = $config['api_provider'] ?? 'exchangerate-api';
            $apiKey = $config['api_key'] ?? null;
            $baseUrl = $this->getApiUrl($provider);
            
            // Build request URL based on provider
            $url = $baseUrl;
            $params = [];
            
            switch ($provider) {
                case 'freecurrencyapi':
                    $params['apikey'] = $apiKey;
                    $params['base_currency'] = $base;
                    break;
                    
                case 'fixer':
                    $params['access_key'] = $apiKey;
                    $params['base'] = $base;
                    break;
                    
                case 'exchangerate-api':
                default:
                    // This API doesn't require a key for basic usage
                    $url .= $base;
                    break;
            }
            
            $response = Http::timeout(2)->get($url, $params);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Different APIs have different response structures
                $rates = match($provider) {
                    'freecurrencyapi' => $data['data'] ?? [],
                    'fixer' => $data['rates'] ?? [],
                    default => $data['rates'] ?? [],
                };
                
                // Validate that we actually got rates data
                if (!empty($rates) && is_array($rates)) {
                    \Log::info("Successfully fetched rates from {$provider} for {$base}");
                    return $rates;
                }
                
                \Log::warning("API returned empty rates for {$base} from {$provider}");
            } else {
                \Log::error("API request failed for {$base} from {$provider}: HTTP " . $response->status());
            }
        } catch (\Exception $e) {
            \Log::error("Failed to fetch rates for {$base}: " . $e->getMessage());
        }

        // Fallback to database rates if API fails
        return $this->getFallbackRates($base);
    }
    
    /**
     * Get fallback rates from database.
     *
     * @param string $base
     * @return array
     */
    protected function getFallbackRates($base)
    {
        $rates = [];
        
        try {
            // 1. Try to get direct rates (manual or api)
            // For global backup rates, prioritize 'buy' type
            $dbRates = ExchangeRate::where('base_currency', $base)
                ->whereIn('source', ['api', 'manual'])
                ->where(function($query) {
                    $query->where('type', 'buy')
                          ->orWhere('type', 'mid')
                          ->orWhereNull('type');
                })
                ->orderByRaw("CASE WHEN type = 'buy' THEN 1 WHEN type = 'mid' THEN 2 ELSE 3 END")
                ->get();
            
            foreach ($dbRates as $rate) {
                // Only set if not already set (prioritizes 'buy' due to ordering)
                if (!isset($rates[$rate->target_currency])) {
                    $rates[$rate->target_currency] = (float) $rate->rate_value;
                }
            }
            
            // 2. If we don't have rates (or not enough), try to calculate from YER base
            // This is crucial for backup rates which are stored relative to YER
            if (empty($rates) || count($rates) < count($this->currencies) - 1) {
                // Fetch YER rates, prioritizing 'buy' type
                $yerRatesQuery = ExchangeRate::where('base_currency', 'YER')
                    ->whereIn('source', ['api', 'manual'])
                    ->where(function($query) {
                        $query->where('type', 'buy')
                              ->orWhere('type', 'mid')
                              ->orWhereNull('type');
                    })
                    ->orderByRaw("CASE WHEN type = 'buy' THEN 1 WHEN type = 'mid' THEN 2 ELSE 3 END")
                    ->get();
                
                $yerRates = [];
                foreach ($yerRatesQuery as $rate) {
                    // Only set if not already set (prioritizes 'buy' due to ordering)
                    if (!isset($yerRates[$rate->target_currency])) {
                        $yerRates[$rate->target_currency] = (float) $rate->rate_value;
                    }
                }
                
                // Add YER itself to the array for calculation
                $yerRates['YER'] = 1.0;

                // Check if we have a rate for the requested base currency against YER
                if (isset($yerRates[$base]) && $yerRates[$base] > 0) {
                    // Calculate Base -> YER rate (inverse of YER -> Base)
                    $baseToYer = 1 / $yerRates[$base];
                    
                    foreach ($this->currencies as $target) {
                        if ($target === $base) continue;
                        
                        // If we already have a direct rate, skip
                        if (isset($rates[$target])) continue;

                        if (isset($yerRates[$target])) {
                            // Rate = (Base -> YER) * (YER -> Target)
                            $rates[$target] = round($baseToYer * $yerRates[$target], 6);
                        }
                    }
                }
            }
            
            if (!empty($rates)) {
                \Log::info("Using fallback database rates for {$base}");
                return $rates;
            }
        } catch (\Exception $e) {
            \Log::error("Failed to get fallback rates for {$base}: " . $e->getMessage());
        }
        
        return [];
    }

    /**
     * Get supported currencies.
     *
     * @return array
     */
    public function getSupportedCurrencies()
    {
        return $this->currencies;
    }
}
