<?php

use App\Models\ExchangeRate;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "Seeding API Backup Rates...\n";

// Base rates against USD
$baseRates = [
    'USD' => 1.0,
    'SAR' => 3.75,
    'AED' => 3.6725,
    'OMR' => 0.385,
    'KWD' => 0.307,
    // YER is handled separately via manual buy/sell, but we can add a backup rate too
    'YER' => 530.0 // Approximate
];

$currencies = array_keys($baseRates);

foreach ($currencies as $from) {
    foreach ($currencies as $to) {
        if ($from === $to) continue;

        // Calculate Cross Rate
        // Rate = (USD -> To) / (USD -> From)
        // Example: SAR -> AED
        // 1 USD = 3.75 SAR  => 1 SAR = 1/3.75 USD
        // 1 USD = 3.67 AED
        // 1 SAR = (1/3.75) * 3.67 AED = 3.67 / 3.75
        
        $rate = $baseRates[$to] / $baseRates[$from];
        
        // Update or Create
        ExchangeRate::updateOrCreate(
            [
                'base_currency' => $from,
                'target_currency' => $to,
                'source' => 'api_backup',
                'type' => 'last_success'
            ],
            [
                'rate_value' => $rate,
                'timestamp' => now()
            ]
        );
        
        echo "Set {$from} -> {$to}: " . round($rate, 4) . "\n";
    }
}

echo "Done seeding backup rates.\n";
