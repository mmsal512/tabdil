<?php

use App\Services\CurrencyConversionService;
use Illuminate\Support\Facades\Cache;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "Testing AED -> SAR Conversion (Fallback)...\n";

// Clear cache to force logic execution
Cache::forget('api_rates_AED');

$service = new CurrencyConversionService();
$start = microtime(true);
// We expect this to try API (fail/timeout) then use DB backup
$result = $service->convert('AED', 'SAR', 100); 
$end = microtime(true);

echo "Conversion Result (100 AED -> SAR): " . $result . "\n";
echo "Expected Result: " . (100 * 1.0204) . "\n";
echo "Time Taken: " . round($end - $start, 4) . " seconds\n";

if ($result > 0) {
    echo "SUCCESS: Fallback worked.\n";
} else {
    echo "FAILED: Result is 0.\n";
}
