<?php

use App\Services\CurrencyConversionService;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Cache;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$service = new CurrencyConversionService();
$currencies = ['SAR', 'YER', 'OMR', 'USD', 'AED', 'KWD'];

echo "Starting Comprehensive Conversion Test...\n";
echo "---------------------------------------\n";

$results = [
    'Foreign -> Foreign' => ['success' => 0, 'fail' => 0],
    'Foreign -> YER' => ['success' => 0, 'fail' => 0],
    'YER -> Foreign' => ['success' => 0, 'fail' => 0],
];

$failedPairs = [];

foreach ($currencies as $from) {
    foreach ($currencies as $to) {
        if ($from === $to) continue;

        // Determine Case
        $case = '';
        if ($from !== 'YER' && $to !== 'YER') {
            $case = 'Foreign -> Foreign';
        } elseif ($to === 'YER') {
            $case = 'Foreign -> YER';
        } elseif ($from === 'YER') {
            $case = 'YER -> Foreign';
        }

        // Clear cache for Foreign->Foreign to test robustness (optional, but good for testing fallback)
        // Cache::forget("api_rates_{$from}");

        echo "Testing {$from} -> {$to} ({$case})... ";
        
        $start = microtime(true);
        $amount = 100;
        $result = $service->convert($from, $to, $amount);
        $time = round(microtime(true) - $start, 4);

        if ($result > 0) {
            echo "OK ({$result}) [{$time}s]\n";
            $results[$case]['success']++;
        } else {
            echo "FAILED (Result: 0)\n";
            $results[$case]['fail']++;
            $failedPairs[] = "{$from} -> {$to} ({$case})";
        }
    }
}

echo "\n---------------------------------------\n";
echo "Summary:\n";
foreach ($results as $case => $data) {
    echo "{$case}: Success: {$data['success']}, Fail: {$data['fail']}\n";
}

if (!empty($failedPairs)) {
    echo "\nFailed Pairs:\n";
    foreach ($failedPairs as $pair) {
        echo "- {$pair}\n";
    }
} else {
    echo "\nALL TESTS PASSED!\n";
}
