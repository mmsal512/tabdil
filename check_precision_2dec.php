<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- CHECKING SAR RATES ---\n";
$rates = DB::table('exchange_rates')
    ->where('base_currency', 'YER')
    ->where('target_currency', 'SAR')
    ->whereIn('type', ['buy', 'sell'])
    ->get();

foreach ($rates as $rate) {
    echo "Type: {$rate->type}\n";
    echo "  Raw Stored Value: {$rate->rate_value}\n";
    echo "  Inverted (Display): " . (1 / $rate->rate_value) . "\n";
    echo "  Updated At: {$rate->updated_at}\n";
}
echo "--------------------------\n";
