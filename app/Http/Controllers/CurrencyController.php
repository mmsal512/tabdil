<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\CurrencyConversionService;

class CurrencyController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyConversionService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function index()
    {
        $currencies = $this->currencyService->getSupportedCurrencies();
        
        // Get the latest timestamp from API backups
        $lastUpdated = \App\Models\ExchangeRate::where('source', 'api_backup')
            ->where('type', 'last_success')
            ->latest('timestamp')
            ->value('timestamp');
            
        return view('currency.index', compact('currencies', 'lastUpdated'));
    }

    public function convert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
        ]);

        $amount = $request->input('amount');
        $from = $request->input('from');
        $to = $request->input('to');

        $result = $this->currencyService->convert($from, $to, $amount);

        return response()->json([
            'success' => true,
            'result' => $result,
            'rate' => $this->currencyService->convert($from, $to, 1),
        ]);
    }

    public function getHistory(Request $request)
    {
        $request->validate([
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
            'days' => 'integer|min:7|max:365',
        ]);

        $from = $request->input('from');
        $to = $request->input('to');
        $days = $request->input('days', 30);

        $rates = \App\Models\HistoricalRate::where('base_currency', $from)
            ->where('target_currency', $to)
            ->where('date', '>=', \Carbon\Carbon::now()->subDays($days))
            ->orderBy('date')
            ->get(['date', 'rate_value']);

        return response()->json([
            'success' => true,
            'rates' => $rates
        ]);
    }

    public function getComparisonRates(Request $request)
    {
        $request->validate([
            'base' => 'required|string|size:3',
            'amount' => 'required|numeric|min:0',
        ]);

        $base = $request->input('base');
        $amount = $request->input('amount');
        $currencies = $this->currencyService->getSupportedCurrencies();
        
        $comparisons = [];
        
        foreach ($currencies as $currency) {
            if ($currency === $base) continue;
            
            $rate = $this->currencyService->convert($base, $currency, 1);
            $converted = $this->currencyService->convert($base, $currency, $amount);
            
            $comparisons[] = [
                'currency' => $currency,
                'rate' => $rate,
                'amount' => $converted
            ];
        }

        return response()->json([
            'success' => true,
            'comparisons' => $comparisons
        ]);
    }
}
