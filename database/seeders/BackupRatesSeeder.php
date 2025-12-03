<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BackupRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * These are the default backup rates for YER conversions.
     * Buy Rate: Used when FOREIGN → YER (Amount × Buy Rate)
     * Sell Rate: Used when YER → FOREIGN (Amount / Sell Rate)
     */
    public function run(): void
    {
        $rates = [
            [
                'currency' => 'SAR',
                'buy_rate' => 425.00,
                'sell_rate' => 428.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'currency' => 'USD',
                'buy_rate' => 1617.00,
                'sell_rate' => 1632.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'currency' => 'OMR',
                'buy_rate' => 3233.58,
                'sell_rate' => 3252.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'currency' => 'AED',
                'buy_rate' => 338.94,
                'sell_rate' => 341.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'currency' => 'KWD',
                'buy_rate' => 4069.97,
                'sell_rate' => 4093.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Use updateOrCreate to avoid duplicates on re-seeding
        foreach ($rates as $rate) {
            DB::table('backup_rates')->updateOrInsert(
                ['currency' => $rate['currency']],
                $rate
            );
        }
    }
}
