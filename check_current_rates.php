<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Current Global Backup Rates (YER base):\n";
$yerRates = DB::table('exchange_rates')
    ->where('base_currency', 'YER')
    ->where('region', 'Global')
    ->orderBy('target_currency')
    ->orderBy('type')
    ->get();
foreach ($yerRates as $rate) {
    echo "  {$rate->target_currency}: Type=" . ($rate->type ?? 'NULL') . ", Value={$rate->rate_value}, Source={$rate->source}\n";
}

echo "\nAll remaining rates:\n";
$allRates = DB::table('exchange_rates')->count();
echo "Total rates in DB: $allRates\n";
