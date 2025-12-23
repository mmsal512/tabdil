<?php

use App\Models\VisitorSetting;
use Illuminate\Support\Facades\Schema;

// Check if table exists
echo "Table exists: " . (Schema::hasTable('visitor_settings') ? 'YES' : 'NO') . "\n";

// Get first record
$settings = VisitorSetting::first();

if ($settings) {
    echo "ID: " . $settings->id . "\n";
    echo "Telegram Bot Token: " . ($settings->telegram_bot_token ? 'SET (' . substr($settings->telegram_bot_token, 0, 10) . '...)' : 'NULL') . "\n";
    echo "Telegram Chat ID: " . ($settings->telegram_chat_id ?? 'NULL') . "\n";
    echo "n8n URL: " . ($settings->n8n_webhook_url ?? 'NULL') . "\n";
    echo "Notifications Enabled: " . ($settings->notifications_enabled ? 'YES' : 'NO') . "\n";
} else {
    echo "No settings record found.\n";
    
    // Create one manually to test
    VisitorSetting::create([
        'notifications_enabled' => true,
        'telegram_bot_token' => 'TEST_TOKEN',
        'report_language' => 'auto',
    ]);
    echo "Created standard record.\n";
}
