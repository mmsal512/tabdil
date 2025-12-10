<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ExchangeRate;
use App\Models\AdminLog;
use App\Models\BackupRate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalRates = ExchangeRate::count();
        $recentLogs = AdminLog::with('admin')->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalUsers', 'totalRates', 'recentLogs'));
    }

    public function backupRates()
    {
        // Get backup rates from the new backup_rates table
        $backupRates = BackupRate::all()->pluck(null, 'currency')->toArray();
        
        $buyRates = [];
        $sellRates = [];
        $currencies = ['SAR', 'USD', 'OMR', 'AED', 'KWD'];
        
        foreach ($currencies as $currency) {
            $buyRates[$currency] = $backupRates[$currency]['buy_rate'] ?? 0;
            $sellRates[$currency] = $backupRates[$currency]['sell_rate'] ?? 0;
        }

        return view('admin.backup-rates', compact('buyRates', 'sellRates'));
    }

    public function updateBackupRates(Request $request)
    {
        $request->validate([
            'buy_rates.*' => 'required|numeric|min:0',
            'sell_rates.*' => 'required|numeric|min:0',
        ]);

        // Update backup rates
        $currencies = ['SAR', 'USD', 'OMR', 'AED', 'KWD'];
        
        foreach ($currencies as $currency) {
            $buyRate = $request->buy_rates[$currency] ?? 0;
            $sellRate = $request->sell_rates[$currency] ?? 0;
            
            BackupRate::updateOrCreate(
                ['currency' => $currency],
                [
                    'buy_rate' => $buyRate,
                    'sell_rate' => $sellRate,
                    'updated_at' => now()
                ]
            );
        }

        // Log action
        AdminLog::create([
            'admin_id' => Auth::id(),
            'action' => 'update_backup_rates',
            'description' => 'Updated backup exchange rates',
        ]);

        // Clear cache
        $supportedCurrencies = (new \App\Services\CurrencyConversionService())->getSupportedCurrencies();
        foreach ($supportedCurrencies as $currency) {
            \Illuminate\Support\Facades\Cache::forget("exchange_rates_{$currency}");
        }

        // Clear the specific buy/sell rates cache used for YER conversions
        \Illuminate\Support\Facades\Cache::forget("buy_sell_rates_yer");

        return redirect()->back()->with('success', __('Backup rates updated successfully.'));
    }

    public function users()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function apiSettings()
    {
        // Get current API settings from api_settings table
        $settings = DB::table('api_settings')->pluck('value', 'key')->toArray();
        
        return view('admin.api-settings', compact('settings'));
    }

    public function updateApiSettings(Request $request)
    {
        $request->validate([
            'api_provider' => 'required|string',
            'api_key' => 'required|string',
            'cache_duration' => 'required|integer|min:1',
            'api_enabled' => 'required|boolean',
        ]);

        // Update API settings
        DB::table('api_settings')->updateOrInsert(
            ['key' => 'api_provider'],
            ['value' => $request->api_provider, 'updated_at' => now()]
        );

        DB::table('api_settings')->updateOrInsert(
            ['key' => 'api_key'],
            ['value' => $request->api_key, 'updated_at' => now()]
        );

        DB::table('api_settings')->updateOrInsert(
            ['key' => 'cache_duration'],
            ['value' => $request->cache_duration, 'updated_at' => now()]
        );

        DB::table('api_settings')->updateOrInsert(
            ['key' => 'api_enabled'],
            ['value' => $request->api_enabled ? 'true' : 'false', 'updated_at' => now()]
        );

        // Log action
        AdminLog::create([
            'admin_id' => Auth::id(),
            'action' => 'update_api_settings',
            'description' => 'Updated API configuration settings',
        ]);

        // Clear all exchange rate caches
        $supportedCurrencies = (new \App\Services\CurrencyConversionService())->getSupportedCurrencies();
        foreach ($supportedCurrencies as $currency) {
            \Illuminate\Support\Facades\Cache::forget("exchange_rates_{$currency}");
        }

        return redirect()->back()->with('success', __('API settings updated successfully.'));
    }

    public function testApiConnection()
    {
        try {
            $service = new \App\Services\CurrencyConversionService();
            // Test with a simple conversion
            $result = $service->convert('USD', 'SAR', 1);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'API connection successful',
                    'sample_rate' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'API connection failed or returned invalid data'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'API connection error: ' . $e->getMessage()
            ], 500);
        }
    }
}
