<?php

use App\Models\ExchangeRate;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "Adding AED -> SAR backup rate...\n";

ExchangeRate::updateOrCreate(
    [
        'base_currency' => 'AED',
        'target_currency' => 'SAR',
        'source' => 'api_backup',
        'type' => 'last_success'
    ],
    [
        'rate_value' => 1.0204,
        'timestamp' => now()
    ]
);

echo "Done.\n";
