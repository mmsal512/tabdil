<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

try {
    $url = 'https://n8ntabdil.n8ntabdil.online/form/51251c3f-c888-4fd0-976e-480226bd99d1';
    
    // Test the problematic type
    $type = 'مشكلة/شكوى';
    
    $data = [
        'field-0' => 'Test Complaint',
        'field-1' => 'comp@test.com',
        'field-2' => $type,
        'field-3' => 'This is a complaint test with slash',
        'field-4' => 'جديد',
        'field-5' => 'عادي',
    ];

    echo "Sending Type: '$type' to $url...\n";
    $response = Http::asMultipart()->withoutVerifying()->post($url, $data);
    
    echo "Status: " . $response->status() . "\n";
    echo "Body: " . $response->body() . "\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
