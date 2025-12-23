<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'ip_address',
        'user_agent',
        'page_url',
        'page_title',
        'referrer',
        'referrer_domain',
        'device_type',
        'browser',
        'os',
        'country_code',
        'country_name',
        'city',
        'is_new_visitor',
        'is_bot',
        'visitor_hash',
    ];

    protected $casts = [
        'is_new_visitor' => 'boolean',
        'is_bot' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============ SCOPES ============

    /**
     * Scope to exclude bots
     */
    public function scopeHumans($query)
    {
        return $query->where('is_bot', false);
    }

    /**
     * Scope for time period
     */
    public function scopeInPeriod($query, Carbon $from, Carbon $to = null)
    {
        $to = $to ?? now();
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope for today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for this week
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()]);
    }

    /**
     * Scope for new visitors only
     */
    public function scopeNewVisitors($query)
    {
        return $query->where('is_new_visitor', true);
    }

    /**
     * Scope for returning visitors
     */
    public function scopeReturningVisitors($query)
    {
        return $query->where('is_new_visitor', false);
    }

    // ============ STATIC METHODS ============

    /**
     * Get statistics for a given period
     */
    public static function getStats(Carbon $from, Carbon $to = null): array
    {
        $to = $to ?? now();
        
        $baseQuery = self::humans()->inPeriod($from, $to);
        
        $totalVisits = (clone $baseQuery)->count();
        $uniqueVisitors = (clone $baseQuery)->distinct('visitor_hash')->count('visitor_hash');
        $newVisitors = (clone $baseQuery)->newVisitors()->distinct('visitor_hash')->count('visitor_hash');
        $returningVisitors = (clone $baseQuery)->returningVisitors()->distinct('visitor_hash')->count('visitor_hash');
        
        // Top pages
        $topPages = (clone $baseQuery)
            ->select('page_url', \DB::raw('count(*) as visits'))
            ->groupBy('page_url')
            ->orderByDesc('visits')
            ->limit(5)
            ->get();
        
        // Top referrers
        $topReferrers = (clone $baseQuery)
            ->whereNotNull('referrer_domain')
            ->where('referrer_domain', '!=', '')
            ->select('referrer_domain', \DB::raw('count(*) as visits'))
            ->groupBy('referrer_domain')
            ->orderByDesc('visits')
            ->limit(5)
            ->get();
        
        // Device breakdown
        $deviceBreakdown = (clone $baseQuery)
            ->select('device_type', \DB::raw('count(*) as count'))
            ->groupBy('device_type')
            ->get()
            ->pluck('count', 'device_type')
            ->toArray();
        
        // Country breakdown
        $countryBreakdown = (clone $baseQuery)
            ->whereNotNull('country_name')
            ->select('country_name', 'country_code', \DB::raw('count(*) as visits'))
            ->groupBy('country_name', 'country_code')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();
        
        // Browser breakdown
        $browserBreakdown = (clone $baseQuery)
            ->whereNotNull('browser')
            ->select('browser', \DB::raw('count(*) as count'))
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return [
            'total_visits' => $totalVisits,
            'unique_visitors' => $uniqueVisitors,
            'new_visitors' => $newVisitors,
            'returning_visitors' => $returningVisitors,
            'top_pages' => $topPages,
            'top_referrers' => $topReferrers,
            'device_breakdown' => $deviceBreakdown,
            'country_breakdown' => $countryBreakdown,
            'browser_breakdown' => $browserBreakdown,
            'period_start' => $from->toDateTimeString(),
            'period_end' => $to->toDateTimeString(),
        ];
    }

    /**
     * Get hourly stats for charts
     */
    public static function getHourlyStats(Carbon $date = null): array
    {
        $date = $date ?? today();
        
        $stats = self::humans()
            ->whereDate('created_at', $date)
            ->select(\DB::raw('HOUR(created_at) as hour'), \DB::raw('count(*) as visits'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('visits', 'hour')
            ->toArray();
        
        // Fill missing hours with 0
        $result = [];
        for ($i = 0; $i < 24; $i++) {
            $result[$i] = $stats[$i] ?? 0;
        }
        
        return $result;
    }

    /**
     * Get daily stats for the last N days
     */
    public static function getDailyStats(int $days = 7): array
    {
        $stats = self::humans()
            ->where('created_at', '>=', now()->subDays($days))
            ->select(\DB::raw('DATE(created_at) as date'), \DB::raw('count(*) as visits'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('visits', 'date')
            ->toArray();
        
        // Fill missing days with 0
        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $result[$date] = $stats[$date] ?? 0;
        }
        
        return $result;
    }
}
