<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class ExchangeRateApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        \App\Models\BackupRate::create([
            'currency' => 'SAR',
            'buy_rate' => 425.00,
            'sell_rate' => 428.00,
        ]);
        
        \App\Models\BackupRate::create([
            'currency' => 'USD',
            'buy_rate' => 1500.00,
            'sell_rate' => 1510.00,
        ]);
    }

    /**
     * Test valid exchange rate request.
     */
    public function test_get_exchange_rate_success()
    {
        $response = $this->getJson('/api/v1/exchange-rate/SAR/YER');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'base',
                     'target',
                     'purchase_rate',
                     'timestamp'
                 ])
                 ->assertJson([
                     'success' => true,
                     'base' => 'SAR',
                     'target' => 'YER',
                 ]);
    }

    /**
     * Test invalid currency code format (lowercase).
     */
    public function test_invalid_currency_code_format()
    {
        $response = $this->getJson('/api/v1/exchange-rate/sar/YER');

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                     'error' => 'Invalid currency code. codes must be 3 uppercase letters (A-Z).',
                 ]);
    }

    /**
     * Test invalid currency code length.
     */
    public function test_invalid_currency_code_length()
    {
        $response = $this->getJson('/api/v1/exchange-rate/SA/YER');

        $response->assertStatus(400);
    }
}
