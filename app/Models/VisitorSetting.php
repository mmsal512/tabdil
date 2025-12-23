<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'notifications_enabled',
        'notification_interval_hours',
        'last_notification_sent_at',
        'telegram_bot_token',
        'telegram_chat_id',
        'n8n_webhook_url',
        'use_n8n',
        'report_language',
        'smart_alerts_enabled',
        'spike_threshold_percent',
        'drop_threshold_percent',
        'total_visitors_reported',
        'total_reports_sent',
    ];

    protected $casts = [
        'notifications_enabled' => 'boolean',
        'use_n8n' => 'boolean',
        'smart_alerts_enabled' => 'boolean',
        'last_notification_sent_at' => 'datetime',
        'notification_interval_hours' => 'integer',
        'spike_threshold_percent' => 'integer',
        'drop_threshold_percent' => 'integer',
        'total_visitors_reported' => 'integer',
        'total_reports_sent' => 'integer',
    ];

    /**
     * Get the singleton settings instance
     */
    public static function getInstance(): self
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'notifications_enabled' => true,
                'notification_interval_hours' => 24,
                'telegram_bot_token' => config('services.telegram.bot_token'),
                'telegram_chat_id' => config('services.telegram.chat_id'),
                'use_n8n' => true,
                'report_language' => 'auto',
                'smart_alerts_enabled' => true,
                'spike_threshold_percent' => 200,
                'drop_threshold_percent' => 50,
            ]);
        }
        
        return $settings;
    }

    /**
     * Check if it's time to send a notification
     */
    public function shouldSendNotification(): bool
    {
        if (!$this->notifications_enabled) {
            return false;
        }

        if (!$this->last_notification_sent_at) {
            return true;
        }

        $hoursSinceLastNotification = $this->last_notification_sent_at->diffInHours(now());
        
        return $hoursSinceLastNotification >= $this->notification_interval_hours;
    }

    /**
     * Mark notification as sent
     */
    public function markNotificationSent(int $visitorsCount): void
    {
        $this->update([
            'last_notification_sent_at' => now(),
            'total_visitors_reported' => $this->total_visitors_reported + $visitorsCount,
            'total_reports_sent' => $this->total_reports_sent + 1,
        ]);
    }

    /**
     * Get the report language based on settings
     */
    public function getReportLanguage(): string
    {
        if ($this->report_language === 'auto') {
            return app()->getLocale();
        }
        
        return $this->report_language;
    }
}
