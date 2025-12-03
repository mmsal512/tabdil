<?php

$apiKey = 'b79fc16303bd87c0a25c9b22';
$base = 'USD';
$url = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/{$base}";

echo "Testing API URL with file_get_contents: $url\n";

$options = [
    "http" => [
        "method" => "GET",
        "timeout" => 10,
        "header" => "User-Agent: PHP\r\n"
    ]
];

$context = stream_context_create($options);

try {
    $response = file_get_contents($url, false, $context);
    
    if ($response !== false) {
        echo "Success!\n";
        $data = json_decode($response, true);
        echo "Result: " . ($data['conversion_rates']['SAR'] ?? 'SAR not found') . "\n";
    } else {
        echo "Failed to fetch content.\n";
        $error = error_get_last();
        echo "Error: " . $error['message'] . "\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
