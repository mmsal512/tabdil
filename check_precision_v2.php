<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$rate = DB::table('exchange_rates')
    ->where('base_currency', 'YER')
    ->where('target_currency', 'SAR')
    ->where('type', 'buy')
    ->first();

if ($rate) {
    echo "Raw Stored Value: " . $rate->rate_value . "\n";
    echo "Formatted Stored Value: " . number_format($rate->rate_value, 12) . "\n";
    echo "Inverted (Display Value): " . (1 / $rate->rate_value) . "\n";
} else {
    echo "Rate not found.\n";
}
