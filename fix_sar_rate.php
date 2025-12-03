<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Deleting direct SAR -> YER rates...\n";

$deleted = DB::table('exchange_rates')
    ->where('base_currency', 'SAR')
    ->where('target_currency', 'YER')
    ->delete();

echo "Deleted $deleted records.\n";

// Also check for other direct rates that might be wrong
$otherDirect = DB::table('exchange_rates')
    ->where('base_currency', '!=', 'YER')
    ->where('target_currency', 'YER')
    ->count();

echo "Remaining direct rates to YER (excluding YER base): $otherDirect\n";
