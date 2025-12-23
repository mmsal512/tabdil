<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GeoIpService
{
    /**
     * Get location data for an IP address using ip-api.com (free)
     */
    public function getLocation(string $ip): array
    {
        // Skip localhost/private IPs
        if ($this->isPrivateIp($ip)) {
            return $this->getDefaultLocation();
        }

        // Check cache first (cache for 24 hours)
        $cacheKey = "geoip:{$ip}";
        
        return Cache::remember($cacheKey, 86400, function () use ($ip) {
            return $this->fetchFromApi($ip);
        });
    }

    /**
     * Fetch location from ip-api.com
     */
    protected function fetchFromApi(string $ip): array
    {
        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}", [
                'fields' => 'status,country,countryCode,city',
                'lang' => 'en',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'success') {
                    return [
                        'country_code' => $data['countryCode'] ?? null,
                        'country_name' => $data['country'] ?? null,
                        'city' => $data['city'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('GeoIP lookup failed for IP: ' . $ip . ' - ' . $e->getMessage());
        }

        return $this->getDefaultLocation();
    }

    /**
     * Check if IP is private/localhost
     */
    protected function isPrivateIp(string $ip): bool
    {
        if ($ip === '127.0.0.1' || $ip === '::1' || $ip === 'localhost') {
            return true;
        }

        // Check for private IP ranges
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }

    /**
     * Get default location for private/unknown IPs
     */
    protected function getDefaultLocation(): array
    {
        return [
            'country_code' => null,
            'country_name' => null,
            'city' => null,
        ];
    }
}
