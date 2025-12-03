<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "All SAR -> YER rates:\n";
$sarRates = DB::table('exchange_rates')
    ->where('base_currency', 'SAR')
    ->where('target_currency', 'YER')
    ->orderBy('type')
    ->get();
foreach ($sarRates as $rate) {
    echo "  Type: " . ($rate->type ?? 'NULL') . ", Value: {$rate->rate_value}, Source: {$rate->source}, Region: {$rate->region}\n";
}

echo "\nAll YER -> SAR rates:\n";
$yerRates = DB::table('exchange_rates')
    ->where('base_currency', 'YER')
    ->where('target_currency', 'SAR')
    ->orderBy('type')
    ->get();
foreach ($yerRates as $rate) {
    echo "  Type: " . ($rate->type ?? 'NULL') . ", Value: {$rate->rate_value}, Source: {$rate->source}, Region: {$rate->region}\n";
}
