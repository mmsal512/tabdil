<?php

use App\Services\CurrencyConversionService;
use Illuminate\Support\Facades\Cache;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Clear cache to ensure fresh rates
Cache::flush();

$service = new CurrencyConversionService();

echo "Testing Currency Conversions with Strict Buy/Sell Logic\n";
echo "=======================================================\n\n";

// Force flush output buffer
if (ob_get_level()) ob_end_flush();


$tests = [
    ['from' => 'SAR', 'to' => 'YER', 'amount' => 1000, 'desc' => 'Foreign -> YER (Buy Rate)'],
    ['from' => 'YER', 'to' => 'SAR', 'amount' => 425000, 'desc' => 'YER -> Foreign (Sell Rate)'],
    ['from' => 'USD', 'to' => 'SAR', 'amount' => 100, 'desc' => 'Foreign -> Foreign (Cross Rate)'],
];

foreach ($tests as $test) {
    $result = $service->convert($test['from'], $test['to'], $test['amount']);
    echo "Test: {$test['desc']}\n";
    echo "Convert {$test['amount']} {$test['from']} -> {$test['to']}\n";
    echo "Result: $result {$test['to']}\n";
    echo "-------------------------------------------------------\n";
}
