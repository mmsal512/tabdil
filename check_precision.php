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
    echo "Stored Value: " . $rate->rate_value . "\n";
    echo "Inverted (Display Value): " . (1 / $rate->rate_value) . "\n";
} else {
    echo "Rate not found.\n";
}
