<?php

use Illuminate\Support\Facades\Http;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(\App\Services\CurrencyConversionService::class);

echo "=== اختبار شامل لجميع حالات التحويل ===\n\n";

// Test Case 1: Foreign -> YER
echo "الحالة 1: عملة أجنبية → YER\n";
echo "-----------------------------------\n";
$result1 = $service->convert('USD', 'YER', 1000);
echo "تحويل 1000 USD إلى YER: " . number_format($result1, 2) . " YER\n";
echo "المتوقع: 1,617,000 YER (1000 × 1617)\n\n";

// Test Case 2: YER -> Foreign
echo "الحالة 2: YER → عملة أجنبية\n";
echo "-----------------------------------\n";
$result2 = $service->convert('YER', 'USD', 1617000);
echo "تحويل 1,617,000 YER إلى USD: " . number_format($result2, 2) . " USD\n";
echo "المتوقع: 1,000 USD (1617000 ÷ 1617)\n\n";

// Test Case 3a: Foreign -> Foreign (USD to SAR)
echo "الحالة 3أ: عملة أجنبية → عملة أجنبية (USD → SAR)\n";
echo "-----------------------------------\n";
$result3a = $service->convert('USD', 'SAR', 1000);
echo "تحويل 1000 USD إلى SAR: " . number_format($result3a, 2) . " SAR\n";
echo "ملاحظة: يعتمد على API أو Cross Rate عبر YER\n\n";

// Test Case 3b: Foreign -> Foreign (SAR to USD)
echo "الحالة 3ب: عملة أجنبية → عملة أجنبية (SAR → USD)\n";
echo "-----------------------------------\n";
$result3b = $service->convert('SAR', 'USD', 1000);
echo "تحويل 1000 SAR إلى USD: " . number_format($result3b, 2) . " USD\n";
echo "ملاحظة: يعتمد على API أو Cross Rate عبر YER\n\n";

// Test Case 3c: Foreign -> Foreign (OMR to AED)
echo "الحالة 3ج: عملة أجنبية → عملة أجنبية (OMR → AED)\n";
echo "-----------------------------------\n";
$result3c = $service->convert('OMR', 'AED', 1000);
echo "تحويل 1000 OMR إلى AED: " . number_format($result3c, 2) . " AED\n";
echo "ملاحظة: يعتمد على API أو Cross Rate عبر YER\n\n";

// Test Case 4: Same currency
echo "الحالة 4: نفس العملة\n";
echo "-----------------------------------\n";
$result4 = $service->convert('USD', 'USD', 1000);
echo "تحويل 1000 USD إلى USD: " . number_format($result4, 2) . " USD\n";
echo "المتوقع: 1,000 USD\n\n";

echo "=== انتهى الاختبار ===\n";
