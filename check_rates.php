<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "YER -> SAR rates:\n";
$yerToSar = DB::table('exchange_rates')
    ->where('base_currency', 'YER')
    ->where('target_currency', 'SAR')
    ->get();
foreach ($yerToSar as $rate) {
    echo "  Rate: {$rate->rate_value}, Region: {$rate->region}, Source: {$rate->source}\n";
}

echo "\nSAR -> YER rates:\n";
$sarToYer = DB::table('exchange_rates')
    ->where('base_currency', 'SAR')
    ->where('target_currency', 'YER')
    ->get();
foreach ($sarToYer as $rate) {
    echo "  Rate: {$rate->rate_value}, Region: {$rate->region}, Source: {$rate->source}\n";
}
