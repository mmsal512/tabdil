<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExchangeRateController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyConversionService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Get the current exchange rate between two currencies.
     *
     * @param string $base_currency
     * @param string $target_currency
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRate($base_currency, $target_currency)
    {
        // 1. Input Validation
        // The system MUST strictly validate that both {base_currency} and {target_currency} 
        // parameters contain ONLY three uppercase alphabetical characters (A-Z).
        $validator = Validator::make(
            ['base_currency' => $base_currency, 'target_currency' => $target_currency],
            [
                'base_currency' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
                'target_currency' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid currency code. codes must be 3 uppercase letters (A-Z).',
                'details' => $validator->errors()
            ], 400);
        }

        // 2. Sanitization (Laravel route parameters are strings, but explicit upper-casing/trimming is good practice though regex already enforces format)
        $base = strtoupper(trim($base_currency));
        $target = strtoupper(trim($target_currency));

        // 3. Logic: Fetch Rate
        // currencyService->convert($from, $to, $amount)
        // If we want the rate, we convert 1 unit.
        $rate = $this->currencyService->convert($base, $target, 1);

        // 4. Response
        return response()->json([
            'success' => true,
            'base' => $base,
            'target' => $target,
            'purchase_rate' => $rate,
            'timestamp' => now()->utc()->format('Y-m-d\TH:i:s\Z')
        ]);
    }
}
