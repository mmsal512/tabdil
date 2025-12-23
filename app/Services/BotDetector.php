<?php

namespace App\Services;

class BotDetector
{
    /**
     * Common bot user agent patterns
     */
    protected array $botPatterns = [
        'googlebot',
        'bingbot',
        'slurp',
        'duckduckbot',
        'baiduspider',
        'yandexbot',
        'sogou',
        'exabot',
        'facebot',
        'facebookexternalhit',
        'ia_archiver',
        'alexabot',
        'mj12bot',
        'ahrefsbot',
        'semrushbot',
        'dotbot',
        'rogerbot',
        'screaming frog',
        'uptimerobot',
        'pingdom',
        'gtmetrix',
        'bot',
        'spider',
        'crawler',
        'scraper',
        'curl',
        'wget',
        'python-requests',
        'java',
        'go-http-client',
        'headlesschrome',
        'phantomjs',
        'selenium',
        'puppeteer',
        'lighthouse',
        'pagespeed',
        'chrome-lighthouse',
    ];

    /**
     * Check if a user agent belongs to a bot
     */
    public function isBot(?string $userAgent): bool
    {
        if (empty($userAgent)) {
            return true; // No user agent = likely a bot
        }

        $userAgentLower = strtolower($userAgent);

        foreach ($this->botPatterns as $pattern) {
            if (str_contains($userAgentLower, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Parse device type from user agent
     */
    public function getDeviceType(?string $userAgent): string
    {
        if (empty($userAgent)) {
            return 'unknown';
        }

        $userAgentLower = strtolower($userAgent);

        // Check for tablets first (some tablets contain 'mobile')
        if (preg_match('/tablet|ipad|playbook|silk/i', $userAgent)) {
            return 'tablet';
        }

        // Check for mobile devices
        if (preg_match('/mobile|android|iphone|ipod|blackberry|opera mini|opera mobi|iemobile|windows phone/i', $userAgent)) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Parse browser name from user agent
     */
    public function getBrowser(?string $userAgent): ?string
    {
        if (empty($userAgent)) {
            return null;
        }

        $browsers = [
            'Edge' => '/edg/i',
            'Opera' => '/opera|opr/i',
            'Chrome' => '/chrome/i',
            'Safari' => '/safari/i',
            'Firefox' => '/firefox/i',
            'IE' => '/msie|trident/i',
            'Samsung Browser' => '/samsungbrowser/i',
            'UC Browser' => '/ucbrowser/i',
        ];

        foreach ($browsers as $name => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $name;
            }
        }

        return 'Other';
    }

    /**
     * Parse OS from user agent
     */
    public function getOS(?string $userAgent): ?string
    {
        if (empty($userAgent)) {
            return null;
        }

        $osPatterns = [
            'Windows 11' => '/windows nt 10.*build.*2[2-9]/i',
            'Windows 10' => '/windows nt 10/i',
            'Windows 8.1' => '/windows nt 6.3/i',
            'Windows 8' => '/windows nt 6.2/i',
            'Windows 7' => '/windows nt 6.1/i',
            'macOS' => '/macintosh|mac os x/i',
            'iOS' => '/iphone|ipad|ipod/i',
            'Android' => '/android/i',
            'Linux' => '/linux/i',
            'Chrome OS' => '/cros/i',
        ];

        foreach ($osPatterns as $name => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $name;
            }
        }

        return 'Other';
    }

    /**
     * Extract domain from referrer URL
     */
    public function extractDomain(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $parsed = parse_url($url);
        
        if (isset($parsed['host'])) {
            // Remove www. prefix
            return preg_replace('/^www\./i', '', $parsed['host']);
        }

        return null;
    }
}
