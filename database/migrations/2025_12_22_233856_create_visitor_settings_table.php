<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visitor_settings', function (Blueprint $table) {
            $table->id();
            
            // Notification Settings
            $table->boolean('notifications_enabled')->default(true);
            $table->integer('notification_interval_hours')->default(24); // Every 24 hours by default
            $table->timestamp('last_notification_sent_at')->nullable();
            
            // Telegram Settings
            $table->string('telegram_bot_token')->nullable();
            $table->string('telegram_chat_id')->nullable();
            
            // n8n Webhook Settings
            $table->string('n8n_webhook_url')->nullable();
            $table->boolean('use_n8n')->default(true);
            
            // Report Language
            $table->enum('report_language', ['ar', 'en', 'auto'])->default('auto');
            
            // Smart Alerts
            $table->boolean('smart_alerts_enabled')->default(true);
            $table->integer('spike_threshold_percent')->default(200); // Alert if traffic increases by 200%
            $table->integer('drop_threshold_percent')->default(50); // Alert if traffic drops by 50%
            
            // Statistics
            $table->unsignedBigInteger('total_visitors_reported')->default(0);
            $table->integer('total_reports_sent')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_settings');
    }
};
