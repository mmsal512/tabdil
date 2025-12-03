<?php

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Cache;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(\App\Services\CurrencyConversionService::class);

echo "=== اختبار التحويل باستخدام النسخة الاحتياطية من قاعدة البيانات ===\n\n";

// 1. Clear Cache to force logic to check DB
Cache::forget('api_rates_USD');
echo "1. تم مسح الـ Cache.\n";

// 2. Insert a 'Last Success' record manually
ExchangeRate::updateOrCreate(
    [
        'base_currency' => 'USD',
        'target_currency' => 'SAR',
        'source' => 'api_backup',
        'type' => 'last_success'
    ],
    [
        'rate_value' => 3.75, // Exact rate
        'timestamp' => now()
    ]
);
echo "2. تم إدخال سعر احتياطي يدوي: 1 USD = 3.75 SAR (api_backup)\n";

// 3. Test Conversion
echo "3. جاري اختبار التحويل...\n";
$result = $service->convert('USD', 'SAR', 1000);

echo "النتيجة: " . number_format($result, 2) . " SAR\n";
echo "المتوقع: 3,750.00 SAR (باستخدام السعر الاحتياطي 3.75)\n";

if ($result == 3750) {
    echo "✅ الاختبار ناجح! تم استخدام السعر الاحتياطي.\n";
} else {
    echo "❌ الاختبار فشل. النتيجة غير متطابقة.\n";
}
