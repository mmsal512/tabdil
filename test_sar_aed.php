<?php

use App\Services\CurrencyConversionService;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Cache;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "Testing SAR -> AED Conversion...\n";

// 1. Check DB directly
$rate = ExchangeRate::where('base_currency', 'SAR')
    ->where('target_currency', 'AED')
    ->where('source', 'api_backup')
    ->where('type', 'last_success')
    ->first();

if ($rate) {
    echo "DB Rate Found: " . $rate->rate_value . "\n";
} else {
    echo "DB Rate NOT Found!\n";
}

// 2. Clear Cache to force logic execution
Cache::forget('api_rates_SAR');

// 3. Test Service
$service = new CurrencyConversionService();
$start = microtime(true);
$result = $service->convert('SAR', 'AED', 1);
$end = microtime(true);

echo "Conversion Result: " . $result . "\n";
echo "Time Taken: " . round($end - $start, 4) . " seconds\n";

if ($result == 0) {
    echo "ERROR: Result is 0. Check logs.\n";
} else {
    echo "SUCCESS: Conversion working.\n";
}
