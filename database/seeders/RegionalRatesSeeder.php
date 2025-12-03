<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExchangeRate;

class RegionalRatesSeeder extends Seeder
{
    public function run()
    {
        // Clear existing regional rates
        \App\Models\ExchangeRate::whereIn('region', ['Sanaa', 'Aden'])->delete();

        // Sana'a rates (1 SAR = X YER, 1 USD = X YER)
        $sanaaRates = [
            'SAR' => ['buy' => 139.70, 'sell' => 140.10],
            'USD' => ['buy' => 1750.0, 'sell' => 1755.0], // Placeholder - update with actual rates
        ];

        // Aden rates (1 SAR = X YER, 1 USD = X YER)
        $adenRates = [
            'SAR' => ['buy' => 425.0, 'sell' => 428.0],
            'USD' => ['buy' => 1580.0, 'sell' => 1585.0], // Placeholder - update with actual rates
        ];

        // Create Sana'a rates
        foreach ($sanaaRates as $currency => $rates) {
            ExchangeRate::create([
                'base_currency' => $currency,
                'target_currency' => 'YER',
                'rate_value' => $rates['buy'],
                'source' => 'manual',
                'timestamp' => now(),
                'region' => 'Sanaa',
                'type' => 'buy',
            ]);

            ExchangeRate::create([
                'base_currency' => $currency,
                'target_currency' => 'YER',
                'rate_value' => $rates['sell'],
                'source' => 'manual',
                'timestamp' => now(),
                'region' => 'Sanaa',
                'type' => 'sell',
            ]);
        }

        // Create Aden rates
        foreach ($adenRates as $currency => $rates) {
            ExchangeRate::create([
                'base_currency' => $currency,
                'target_currency' => 'YER',
                'rate_value' => $rates['buy'],
                'source' => 'manual',
                'timestamp' => now(),
                'region' => 'Aden',
                'type' => 'buy',
            ]);

            ExchangeRate::create([
                'base_currency' => $currency,
                'target_currency' => 'YER',
                'rate_value' => $rates['sell'],
                'source' => 'manual',
                'timestamp' => now(),
                'region' => 'Aden',
                'type' => 'sell',
            ]);
        }
    }
}
