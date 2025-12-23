<?php

namespace App\Services;

use App\Models\Visitor;
use App\Models\VisitorSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class VisitorReportService
{
    /**
     * Send visitor report to Telegram via n8n or directly
     */
    public function sendReport(): bool
    {
        $settings = VisitorSetting::getInstance();
        
        if (!$settings->shouldSendNotification()) {
            return false;
        }

        // Get stats for the period since last notification
        $from = $settings->last_notification_sent_at ?? now()->subHours($settings->notification_interval_hours);
        $stats = Visitor::getStats($from);

        if ($stats['total_visits'] === 0) {
            // No visitors, still record but maybe skip notification
            Log::info('Visitor Report: No visitors in period, skipping notification.');
            return false;
        }

        // Generate report message
        $message = $this->generateReportMessage($stats, $settings);

        // Send via n8n or direct Telegram
        $success = $settings->use_n8n 
            ? $this->sendViaN8n($message, $stats, $settings)
            : $this->sendDirectTelegram($message, $settings);

        if ($success) {
            $settings->markNotificationSent($stats['unique_visitors']);
            Log::info('Visitor Report sent successfully. Visitors: ' . $stats['unique_visitors']);
        }

        return $success;
    }

    /**
     * Send smart alert (spike/drop detection)
     */
    public function checkAndSendSmartAlert(): bool
    {
        $settings = VisitorSetting::getInstance();
        
        if (!$settings->smart_alerts_enabled) {
            return false;
        }

        // Compare current hour with same hour yesterday
        $currentHourVisitors = Visitor::humans()
            ->where('created_at', '>=', now()->startOfHour())
            ->count();

        $yesterdaySameHourVisitors = Visitor::humans()
            ->whereBetween('created_at', [
                now()->subDay()->startOfHour(),
                now()->subDay()->endOfHour()
            ])
            ->count();

        if ($yesterdaySameHourVisitors === 0) {
            return false; // Can't compare
        }

        $changePercent = (($currentHourVisitors - $yesterdaySameHourVisitors) / $yesterdaySameHourVisitors) * 100;

        $alertType = null;
        if ($changePercent >= $settings->spike_threshold_percent) {
            $alertType = 'spike';
        } elseif ($changePercent <= -$settings->drop_threshold_percent) {
            $alertType = 'drop';
        }

        if ($alertType) {
            $message = $this->generateSmartAlertMessage($alertType, $currentHourVisitors, $yesterdaySameHourVisitors, $changePercent, $settings);
            
            return $settings->use_n8n 
                ? $this->sendViaN8n($message, ['alert_type' => $alertType], $settings)
                : $this->sendDirectTelegram($message, $settings);
        }

        return false;
    }

    /**
     * Generate report message in the appropriate language
     */
    protected function generateReportMessage(array $stats, VisitorSetting $settings): string
    {
        $lang = $settings->getReportLanguage();

        if ($lang === 'ar') {
            return $this->generateArabicReport($stats);
        }

        return $this->generateEnglishReport($stats);
    }

    /**
     * Generate Arabic report
     */
    protected function generateArabicReport(array $stats): string
    {
        $deviceEmojis = ['mobile' => '📱', 'desktop' => '💻', 'tablet' => '📲', 'unknown' => '❓'];
        
        $message = "📊 *تقرير الزوار*\n";
        $message .= "━━━━━━━━━━━━━━━━━\n\n";
        
        $message .= "⏰ *الفترة:*\n";
        $message .= "من: " . Carbon::parse($stats['period_start'])->format('Y-m-d H:i') . "\n";
        $message .= "إلى: " . Carbon::parse($stats['period_end'])->format('Y-m-d H:i') . "\n\n";
        
        $message .= "👥 *الإحصائيات العامة:*\n";
        $message .= "├ إجمالي الزيارات: {$stats['total_visits']}\n";
        $message .= "├ الزوار الفريدون: {$stats['unique_visitors']}\n";
        $message .= "├ زوار جدد: {$stats['new_visitors']}\n";
        $message .= "└ زوار عائدون: {$stats['returning_visitors']}\n\n";
        
        // Device breakdown
        $message .= "📱 *الأجهزة:*\n";
        foreach ($stats['device_breakdown'] as $device => $count) {
            $emoji = $deviceEmojis[$device] ?? '📍';
            $message .= "{$emoji} {$device}: {$count}\n";
        }
        $message .= "\n";
        
        // Top countries
        if (count($stats['country_breakdown']) > 0) {
            $message .= "🌍 *أفضل الدول:*\n";
            foreach ($stats['country_breakdown']->take(5) as $country) {
                $flag = $this->getCountryFlag($country->country_code);
                $message .= "{$flag} {$country->country_name}: {$country->visits}\n";
            }
            $message .= "\n";
        }
        
        // Top pages
        if (count($stats['top_pages']) > 0) {
            $message .= "📄 *أكثر الصفحات زيارة:*\n";
            foreach ($stats['top_pages']->take(3) as $page) {
                $pageName = str_replace(url('/'), '', $page->page_url) ?: '/';
                $message .= "• {$pageName}: {$page->visits}\n";
            }
            $message .= "\n";
        }
        
        // Top referrers
        if (count($stats['top_referrers']) > 0) {
            $message .= "🔗 *مصادر الزيارات:*\n";
            foreach ($stats['top_referrers']->take(3) as $ref) {
                $message .= "• {$ref->referrer_domain}: {$ref->visits}\n";
            }
        }
        
        $message .= "\n━━━━━━━━━━━━━━━━━\n";
        $message .= "🚀 _Tabdil Analytics_";

        return $message;
    }

    /**
     * Generate English report
     */
    protected function generateEnglishReport(array $stats): string
    {
        $deviceEmojis = ['mobile' => '📱', 'desktop' => '💻', 'tablet' => '📲', 'unknown' => '❓'];
        
        $message = "📊 *Visitor Report*\n";
        $message .= "━━━━━━━━━━━━━━━━━\n\n";
        
        $message .= "⏰ *Period:*\n";
        $message .= "From: " . Carbon::parse($stats['period_start'])->format('Y-m-d H:i') . "\n";
        $message .= "To: " . Carbon::parse($stats['period_end'])->format('Y-m-d H:i') . "\n\n";
        
        $message .= "👥 *General Statistics:*\n";
        $message .= "├ Total Visits: {$stats['total_visits']}\n";
        $message .= "├ Unique Visitors: {$stats['unique_visitors']}\n";
        $message .= "├ New Visitors: {$stats['new_visitors']}\n";
        $message .= "└ Returning Visitors: {$stats['returning_visitors']}\n\n";
        
        // Device breakdown
        $message .= "📱 *Devices:*\n";
        foreach ($stats['device_breakdown'] as $device => $count) {
            $emoji = $deviceEmojis[$device] ?? '📍';
            $message .= "{$emoji} {$device}: {$count}\n";
        }
        $message .= "\n";
        
        // Top countries
        if (count($stats['country_breakdown']) > 0) {
            $message .= "🌍 *Top Countries:*\n";
            foreach ($stats['country_breakdown']->take(5) as $country) {
                $flag = $this->getCountryFlag($country->country_code);
                $message .= "{$flag} {$country->country_name}: {$country->visits}\n";
            }
            $message .= "\n";
        }
        
        // Top pages
        if (count($stats['top_pages']) > 0) {
            $message .= "📄 *Top Pages:*\n";
            foreach ($stats['top_pages']->take(3) as $page) {
                $pageName = str_replace(url('/'), '', $page->page_url) ?: '/';
                $message .= "• {$pageName}: {$page->visits}\n";
            }
            $message .= "\n";
        }
        
        // Top referrers
        if (count($stats['top_referrers']) > 0) {
            $message .= "🔗 *Traffic Sources:*\n";
            foreach ($stats['top_referrers']->take(3) as $ref) {
                $message .= "• {$ref->referrer_domain}: {$ref->visits}\n";
            }
        }
        
        $message .= "\n━━━━━━━━━━━━━━━━━\n";
        $message .= "🚀 _Tabdil Analytics_";

        return $message;
    }

    /**
     * Generate smart alert message
     */
    protected function generateSmartAlertMessage(string $type, int $current, int $previous, float $changePercent, VisitorSetting $settings): string
    {
        $lang = $settings->getReportLanguage();
        $changePercent = abs(round($changePercent));

        if ($lang === 'ar') {
            if ($type === 'spike') {
                return "🔥 *تنبيه: ارتفاع مفاجئ في الزيارات!*\n\n" .
                    "الزوار الآن: {$current}\n" .
                    "نفس الساعة أمس: {$previous}\n" .
                    "نسبة الزيادة: {$changePercent}%\n\n" .
                    "💡 قد يكون هناك حملة ناجحة أو محتوى viral!";
            } else {
                return "⚠️ *تنبيه: انخفاض مفاجئ في الزيارات!*\n\n" .
                    "الزوار الآن: {$current}\n" .
                    "نفس الساعة أمس: {$previous}\n" .
                    "نسبة الانخفاض: {$changePercent}%\n\n" .
                    "🔍 تحقق من أن الموقع يعمل بشكل طبيعي.";
            }
        }

        if ($type === 'spike') {
            return "🔥 *Alert: Sudden Traffic Spike!*\n\n" .
                "Current visitors: {$current}\n" .
                "Same hour yesterday: {$previous}\n" .
                "Increase: {$changePercent}%\n\n" .
                "💡 You might have a successful campaign or viral content!";
        } else {
            return "⚠️ *Alert: Sudden Traffic Drop!*\n\n" .
                "Current visitors: {$current}\n" .
                "Same hour yesterday: {$previous}\n" .
                "Decrease: {$changePercent}%\n\n" .
                "🔍 Check if your site is working properly.";
        }
    }

    /**
     * Send message via n8n webhook
     */
    protected function sendViaN8n(string $message, array $data, VisitorSetting $settings): bool
    {
        if (empty($settings->n8n_webhook_url)) {
            Log::warning('Visitor Report: n8n webhook URL not configured');
            return $this->sendDirectTelegram($message, $settings); // Fallback
        }

        try {
            $payload = [
                'message' => $message,
                'stats' => $data,
                'telegram_chat_id' => $settings->telegram_chat_id,
                'timestamp' => now()->toDateTimeString(),
            ];

            $response = Http::timeout(10)
                ->withoutVerifying()
                ->post($settings->n8n_webhook_url, $payload);

            if ($response->successful()) {
                return true;
            }

            Log::error('Visitor Report n8n failed: ' . $response->status());
            return $this->sendDirectTelegram($message, $settings); // Fallback
            
        } catch (\Exception $e) {
            Log::error('Visitor Report n8n exception: ' . $e->getMessage());
            return $this->sendDirectTelegram($message, $settings); // Fallback
        }
    }

    /**
     * Send message directly to Telegram
     */
    protected function sendDirectTelegram(string $message, VisitorSetting $settings): bool
    {
        $token = $settings->telegram_bot_token;
        $chatId = $settings->telegram_chat_id;

        if (empty($token) || empty($chatId)) {
            Log::error('Visitor Report: Telegram credentials not configured');
            return false;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);

            return $response->successful();
            
        } catch (\Exception $e) {
            Log::error('Visitor Report Telegram exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get country flag emoji from country code
     */
    protected function getCountryFlag(?string $countryCode): string
    {
        if (empty($countryCode) || strlen($countryCode) !== 2) {
            return '🌍';
        }

        $countryCode = strtoupper($countryCode);
        $flag = '';
        
        foreach (str_split($countryCode) as $char) {
            $flag .= mb_chr(ord($char) - ord('A') + 0x1F1E6);
        }
        
        return $flag;
    }
}
