<?php

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Cache;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Delete all rates that are NOT 'buy' or 'sell' type
// This targets the 'mid' rates which are the legacy/test ones
$deleted = ExchangeRate::whereNotIn('type', ['buy', 'sell'])->delete();

echo "Deleted $deleted legacy rates.\n";

// Clear cache to ensure new rates are used immediately
$currencies = ['USD', 'SAR', 'YER', 'OMR', 'AED', 'KWD'];
foreach ($currencies as $currency) {
    Cache::forget("exchange_rates_{$currency}");
}
echo "Cache cleared.\n";

$count = ExchangeRate::count();
echo "Remaining Total Rates: " . $count . "\n";
