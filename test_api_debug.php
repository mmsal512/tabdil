<?php

use Illuminate\Support\Facades\Http;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiKey = 'b79fc16303bd87c0a25c9b22';
$base = 'USD';
$url = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/{$base}";

echo "Testing API URL: $url\n";

try {
    $response = Http::timeout(10)->get($url);
    
    echo "Status: " . $response->status() . "\n";
    if ($response->successful()) {
        echo "Success!\n";
        $data = $response->json();
        echo "Result: " . json_encode($data['conversion_rates']['SAR'] ?? 'SAR not found') . "\n";
    } else {
        echo "Failed.\n";
        echo "Body: " . $response->body() . "\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
