<?php

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = ExchangeRate::count();
echo "Total Rates: " . $count . "\n\n";

$rates = ExchangeRate::all();

echo str_pad("ID", 5) . str_pad("Region", 10) . str_pad("Base", 6) . str_pad("Target", 8) . str_pad("Type", 6) . str_pad("Rate", 15) . "\n";
echo str_repeat("-", 60) . "\n";

foreach ($rates as $rate) {
    echo str_pad($rate->id, 5) . 
         str_pad($rate->region, 10) . 
         str_pad($rate->base_currency, 6) . 
         str_pad($rate->target_currency, 8) . 
         str_pad($rate->type ?? 'N/A', 6) . 
         str_pad($rate->rate_value, 15) . "\n";
}
