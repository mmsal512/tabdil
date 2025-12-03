<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CurrencyConversionService;
use Illuminate\Support\Facades\DB;

$service = new CurrencyConversionService();

echo "--- DEBUGGING CONVERSION SAR -> YER ---\n";
$amount = 1000;
$from = 'SAR';
$to = 'YER';

// Check stored rates first
echo "Stored Rates for Base YER:\n";
$rates = DB::table('exchange_rates')
    ->where('base_currency', 'YER')
    ->where('target_currency', 'SAR')
    ->get();

// Flush cache to ensure fresh rates
Cache::flush();

$config = DB::table('api_settings')->pluck('value', 'key')->toArray();
echo "API Config: enabled=" . ($config['api_enabled'] ?? 'false') . ", provider=" . ($config['api_provider'] ?? 'none') . "\n";

foreach ($rates as $rate) {
    echo "  Type: {$rate->type}, Value: {$rate->rate_value} (Inverse: " . (1/$rate->rate_value) . ")\n";
}

echo "Stored Rates for Base SAR:\n";
$sarRates = DB::table('exchange_rates')
    ->where('base_currency', 'SAR')
    ->where('target_currency', 'YER')
    ->get();

foreach ($sarRates as $rate) {
    echo "  Type: {$rate->type}, Value: {$rate->rate_value}\n";
}

// Perform conversion
try {
    $result = $service->convert($from, $to, $amount);
    echo "\nConversion Result: $amount $from = $result $to\n";
    
    // Calculate effective rate
    $rate = $result / $amount;
    echo "Effective Rate: $rate\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
