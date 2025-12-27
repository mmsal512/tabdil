<?php

namespace App\Http\Middleware;

use App\Models\Visitor;
use App\Services\BotDetector;
use App\Services\GeoIpService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    protected BotDetector $botDetector;
    protected GeoIpService $geoIpService;

    public function __construct(BotDetector $botDetector, GeoIpService $geoIpService)
    {
        $this->botDetector = $botDetector;
        $this->geoIpService = $geoIpService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip AJAX requests and API calls
        if ($request->ajax() || $request->is('api/*') || $request->is('cron/*')) {
            return $next($request);
        }

        // Skip asset requests
        if ($this->isAssetRequest($request)) {
            return $next($request);
        }

        // Track the visit asynchronously (don't slow down the response)
        $this->trackVisit($request);

        return $next($request);
    }

    /**
     * Track the visit
     */
    protected function trackVisit(Request $request): void
    {
        try {
            $userAgent = $request->userAgent();
            $ip = $this->getClientIp($request);
            $sessionId = session()->getId() ?: md5($ip . $userAgent);
            
            // Create visitor hash for unique visitor tracking
            $visitorHash = md5($ip . $userAgent);
            
            // Check if this is a new visitor (first time seeing this hash)
            $isNewVisitor = !Visitor::where('visitor_hash', $visitorHash)->exists();
            
            // Get bot and device info
            $isBot = $this->botDetector->isBot($userAgent);
            $deviceType = $this->botDetector->getDeviceType($userAgent);
            $browser = $this->botDetector->getBrowser($userAgent);
            $os = $this->botDetector->getOS($userAgent);
            
            // Get referrer info
            $referrer = $request->header('referer');
            $referrerDomain = $this->botDetector->extractDomain($referrer);
            
            // Skip if referrer is our own domain
            $ownDomain = parse_url(config('app.url'), PHP_URL_HOST);
            if ($referrerDomain === $ownDomain) {
                $referrer = null;
                $referrerDomain = null;
            }
            
            // Get GeoIP info (will be cached)
            $location = $this->geoIpService->getLocation($ip);
            
            // Get page URL and title
            $pageUrl = $request->fullUrl();
            $pageTitle = $this->getPageTitle($request);

            // Create visitor record
            Visitor::create([
                'session_id' => $sessionId,
                'ip_address' => $ip,
                'user_agent' => substr($userAgent ?? '', 0, 500), // Limit length
                'page_url' => substr($pageUrl, 0, 500),
                'page_title' => $pageTitle,
                'referrer' => $referrer ? substr($referrer, 0, 500) : null,
                'referrer_domain' => $referrerDomain,
                'device_type' => $deviceType,
                'browser' => $browser,
                'os' => $os,
                'country_code' => $location['country_code'],
                'country_name' => $location['country_name'],
                'city' => $location['city'],
                'is_new_visitor' => $isNewVisitor,
                'is_bot' => $isBot,
                'visitor_hash' => $visitorHash,
            ]);
            
        } catch (\Exception $e) {
            // Don't let tracking errors affect the user experience
            Log::error('Visitor tracking error: ' . $e->getMessage());
        }
    }

    /**
     * Get the real client IP
     */
    protected function getClientIp(Request $request): string
    {
        // Check for common proxy headers
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
        ];

        foreach ($headers as $header) {
            $ip = $request->server($header);
            if ($ip) {
                // X-Forwarded-For can contain multiple IPs, get the first one
                if (str_contains($ip, ',')) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $request->ip() ?? '127.0.0.1';
    }

    /**
     * Check if request is for an asset
     */
    protected function isAssetRequest(Request $request): bool
    {
        $path = $request->path();
        
        $assetPatterns = [
            'css', 'js', 'images', 'img', 'fonts', 'assets',
            'favicon', 'robots.txt', 'sitemap'
        ];

        foreach ($assetPatterns as $pattern) {
            if (str_starts_with($path, $pattern) || str_contains($path, '.' . $pattern)) {
                return true;
            }
        }

        // Check for common file extensions
        $assetExtensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot', 'map'];
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        return in_array(strtolower($extension), $assetExtensions);
    }

    /**
     * Get page title from route name
     */
    protected function getPageTitle(Request $request): ?string
    {
        $route = $request->route();
        
        if ($route && $route->getName()) {
            return $route->getName();
        }

        return null;
    }
}
