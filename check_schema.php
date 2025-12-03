<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$columns = DB::select("SHOW COLUMNS FROM exchange_rates WHERE Field = 'rate_value'");
foreach ($columns as $column) {
    echo "Field: " . $column->Field . "\n";
    echo "Type: " . $column->Type . "\n";
}
