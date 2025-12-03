<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HistoricalRate;
use Carbon\Carbon;

class HistoricalRatesSeeder extends Seeder
{
    public function run()
    {
        $currencies = ['SAR', 'YER', 'OMR', 'USD', 'AED', 'KWD'];
        $base = 'USD';
        
        // Generate data for the last 30 days
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            
            foreach ($currencies as $target) {
                if ($base === $target) continue;
                
                // Generate a somewhat realistic random rate fluctuation
                $baseRate = match($target) {
                    'SAR' => 3.75,
                    'YER' => 250.35,
                    'OMR' => 0.38,
                    'AED' => 3.67,
                    'KWD' => 0.31,
                    default => 1.0,
                };
                
                $fluctuation = (rand(-100, 100) / 10000); // +/- 0.01
                $rate = $baseRate + $fluctuation;

                HistoricalRate::create([
                    'date' => $date,
                    'base_currency' => $base,
                    'target_currency' => $target,
                    'rate_value' => $rate,
                ]);
            }
        }
    }
}
