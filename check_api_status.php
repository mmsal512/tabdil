<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$apiEnabled = DB::table('api_settings')->where('key', 'api_enabled')->value('value');
echo "API Enabled: " . ($apiEnabled ?? 'not set') . "\n";

// Check if there are any SAR rates
echo "\nSAR Rates in DB:\n";
$sarRates = DB::table('exchange_rates')
    ->where('base_currency', 'SAR')
    ->where('target_currency', 'YER')
    ->get();
foreach ($sarRates as $rate) {
    echo "  Type: {$rate->type}, Value: {$rate->rate_value}, Source: {$rate->source}, Region: {$rate->region}\n";
}
