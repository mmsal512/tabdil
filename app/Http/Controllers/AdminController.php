<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ExchangeRate;
use App\Models\AdminLog;
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
        // Get global backup rates with YER as base currency
        // Fetch buy rates
        $buyRatesRaw = ExchangeRate::where('region', 'Global')
            ->where('base_currency', 'YER')
            ->where('type', 'buy')
            ->get()
            ->pluck('rate_value', 'target_currency')
            ->toArray();

        // Fetch sell rates
        $sellRatesRaw = ExchangeRate::where('region', 'Global')
            ->where('base_currency', 'YER')
            ->where('type', 'sell')
            ->get()
            ->pluck('rate_value', 'target_currency')
            ->toArray();

        // Invert rates for display (stored as Unit/YER, display as YER/Unit)
        $buyRates = [];
        $sellRates = [];
        $currencies = ['SAR', 'USD', 'OMR', 'AED', 'KWD'];
        
        foreach ($currencies as $currency) {
            $buyVal = $buyRatesRaw[$currency] ?? 0;
            $sellVal = $sellRatesRaw[$currency] ?? 0;
            
            // Invert: if stored as 0.00235 (1 YER = 0.00235 SAR), display as 425 (1 SAR = 425 YER)
            $buyRates[$currency] = ($buyVal > 0) ? round(1 / $buyVal, 2) : 0;
            $sellRates[$currency] = ($sellVal > 0) ? round(1 / $sellVal, 2) : 0;
        }

        return view('admin.backup-rates', compact('buyRates', 'sellRates'));
    }

    public function updateBackupRates(Request $request)
    {
        $request->validate([
            'buy_rates.*' => 'required|numeric|min:0',
            'sell_rates.*' => 'required|numeric|min:0',
        ]);

        // Update Buy Rates
        if ($request->has('buy_rates')) {
            foreach ($request->buy_rates as $currency => $value) {
                // Input is "YER per Unit" (e.g. 425 for 1 SAR = 425 YER)
                // We need to store "Unit per YER" (e.g. 1/425 = 0.00235 for 1 YER = 0.00235 SAR)
                $storedValue = ($value > 0) ? (1 / $value) : 0;
                
                ExchangeRate::updateOrCreate(
                    [
                        'region' => 'Global',
                        'base_currency' => 'YER',
                        'target_currency' => $currency,
                        'type' => 'buy',
                        'source' => 'manual'
                    ],
                    [
                        'rate_value' => $storedValue,
                        'updated_at' => now()
                    ]
                );
            }
        }

        // Update Sell Rates
        if ($request->has('sell_rates')) {
            foreach ($request->sell_rates as $currency => $value) {
                // Input is "YER per Unit" (e.g. 428 for 1 SAR = 428 YER)
                // We need to store "Unit per YER" (e.g. 1/428 = 0.00234 for 1 YER = 0.00234 SAR)
                $storedValue = ($value > 0) ? (1 / $value) : 0;
                
                ExchangeRate::updateOrCreate(
                    [
                        'region' => 'Global',
                        'base_currency' => 'YER',
                        'target_currency' => $currency,
                        'type' => 'sell',
                        'source' => 'manual'
                    ],
                    [
                        'rate_value' => $storedValue,
                        'updated_at' => now()
                    ]
                );
            }
        }

        // Log action
        AdminLog::create([
            'admin_id' => Auth::id(),
            'action' => 'update_backup_rates',
            'data_before' => null,
            'data_after' => json_encode($request->all()),
        ]);

        // Clear cache for all supported currencies to ensure immediate update
        $currencies = (new \App\Services\CurrencyConversionService())->getSupportedCurrencies();
        foreach ($currencies as $currency) {
            \Illuminate\Support\Facades\Cache::forget("exchange_rates_{$currency}");
        }

        return redirect()->back()->with('success', __('Backup rates updated successfully.'));
    }

    public function users()
    {
        $users = User::where('is_admin', false)->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function toggleUserStatus(User $user)
    {
        if ($user->is_admin) {
            return redirect()->back()->with('error', 'Cannot disable admin users.');
        }

        $user->update(['is_active' => !$user->is_active]);

        AdminLog::create([
            'admin_id' => Auth::id(),
            'action' => $user->is_active ? 'enable_user' : 'disable_user',
            'data_before' => json_encode(['user_id' => $user->id, 'status' => !$user->is_active]),
            'data_after' => json_encode(['user_id' => $user->id, 'status' => $user->is_active]),
        ]);

        $status = $user->is_active ? 'enabled' : 'disabled';
        return redirect()->back()->with('success', "User {$status} successfully!");
    }

    public function resetUserPassword(User $user)
    {
        if ($user->is_admin) {
            return redirect()->back()->with('error', 'Cannot reset admin passwords.');
        }

        $newPassword = \Str::random(12);
        $user->update(['password' => \Hash::make($newPassword)]);

        AdminLog::create([
            'admin_id' => Auth::id(),
            'action' => 'reset_password',
            'data_before' => json_encode(['user_id' => $user->id]),
            'data_after' => json_encode(['user_id' => $user->id, 'message' => 'Password reset']),
        ]);

        return redirect()->back()->with('success', "Password reset successfully! New password: {$newPassword}");
    }
    public function apiBackupRates()
    {
        $rates = ExchangeRate::where('source', 'api_backup')
            ->where('type', 'last_success')
            ->orderBy('base_currency')
            ->orderBy('target_currency')
            ->get();

        return view('admin.api_backup_rates', compact('rates'));
    }
}
