<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Models\VisitorSetting;
use App\Services\VisitorReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VisitorController extends Controller
{
    /**
     * Display visitor analytics dashboard
     */
    public function index()
    {
        // Today's stats
        $todayStats = Visitor::getStats(today());
        
        // Yesterday's stats for comparison
        $yesterdayStats = Visitor::getStats(today()->subDay(), today()->subDay()->endOfDay());
        
        // Weekly stats
        $weeklyStats = Visitor::getStats(now()->startOfWeek());
        
        // Monthly stats
        $monthlyStats = Visitor::getStats(now()->startOfMonth());
        
        // Daily chart data (last 7 days)
        $dailyChartData = Visitor::getDailyStats(7);
        
        // Hourly chart data (today)
        $hourlyChartData = Visitor::getHourlyStats();
        
        // Real-time visitors (last 5 minutes)
        $realtimeVisitors = Visitor::humans()
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count();
        
        // Calculate percentage changes
        $visitorChange = $this->calculatePercentChange(
            $yesterdayStats['unique_visitors'],
            $todayStats['unique_visitors']
        );

        return view('admin.visitors.index', compact(
            'todayStats',
            'yesterdayStats',
            'weeklyStats',
            'monthlyStats',
            'dailyChartData',
            'hourlyChartData',
            'realtimeVisitors',
            'visitorChange'
        ));
    }

    /**
     * Display notification settings
     */
    public function settings()
    {
        $settings = VisitorSetting::getInstance();
        
        return view('admin.visitors.settings', compact('settings'));
    }

    /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'notifications_enabled' => 'boolean',
            'notification_interval_hours' => 'required|integer|min:1|max:720',
            'telegram_bot_token' => 'nullable|string',
            'telegram_chat_id' => 'nullable|string',
            'n8n_webhook_url' => 'nullable|url',
            'use_n8n' => 'boolean',
            'report_language' => 'required|in:ar,en,auto',
            'smart_alerts_enabled' => 'boolean',
            'spike_threshold_percent' => 'required|integer|min:50|max:1000',
            'drop_threshold_percent' => 'required|integer|min:10|max:100',
        ]);

        $settings = VisitorSetting::getInstance();
        
        // Handle checkboxes
        $validated['notifications_enabled'] = $request->has('notifications_enabled');
        $validated['use_n8n'] = $request->has('use_n8n');
        $validated['smart_alerts_enabled'] = $request->has('smart_alerts_enabled');
        
        $settings->update($validated);

        return back()->with('success', __('visitors.settings_saved'));
    }

    /**
     * Send test notification
     */
    public function sendTestNotification()
    {
        $reportService = new VisitorReportService();
        
        // Force send by temporarily modifying settings
        $settings = VisitorSetting::getInstance();
        $originalLastSent = $settings->last_notification_sent_at;
        $settings->update(['last_notification_sent_at' => null]);
        
        $success = $reportService->sendReport();
        
        // Restore original last sent time
        $settings->update(['last_notification_sent_at' => $originalLastSent]);

        if ($success) {
            return back()->with('success', __('visitors.test_notification_sent'));
        }

        return back()->with('error', __('visitors.test_notification_failed'));
    }

    /**
     * Get chart data via AJAX
     */
    public function chartData(Request $request)
    {
        $period = $request->get('period', 'week');
        
        switch ($period) {
            case 'today':
                $data = Visitor::getHourlyStats();
                $labels = array_map(fn($h) => sprintf('%02d:00', $h), array_keys($data));
                break;
            
            case 'week':
                $data = Visitor::getDailyStats(7);
                $labels = array_map(fn($d) => Carbon::parse($d)->format('D'), array_keys($data));
                break;
            
            case 'month':
                $data = Visitor::getDailyStats(30);
                $labels = array_map(fn($d) => Carbon::parse($d)->format('M d'), array_keys($data));
                break;
            
            default:
                $data = Visitor::getDailyStats(7);
                $labels = array_map(fn($d) => Carbon::parse($d)->format('D'), array_keys($data));
        }

        return response()->json([
            'labels' => $labels,
            'data' => array_values($data),
        ]);
    }

    /**
     * Calculate percentage change
     */
    protected function calculatePercentChange(int $previous, int $current): array
    {
        if ($previous === 0) {
            return [
                'value' => $current > 0 ? 100 : 0,
                'direction' => $current > 0 ? 'up' : 'neutral',
            ];
        }

        $change = (($current - $previous) / $previous) * 100;
        
        return [
            'value' => abs(round($change, 1)),
            'direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'neutral'),
        ];
    }
}
