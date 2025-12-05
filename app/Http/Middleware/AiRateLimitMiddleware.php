<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AiRateLimitMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('ai.rate_limit.enabled')) {
            return $next($request);
        }

        $key = 'ai-requests:' . ($request->user()?->id ?? $request->ip());
        $maxAttempts = config('ai.rate_limit.max_requests', 20);
        $decayMinutes = config('ai.rate_limit.decay_minutes', 1);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            return response()->json([
                'success' => false,
                'error' => __('Too many AI requests. Please try again in :seconds seconds.', ['seconds' => $seconds]),
                'retry_after' => $seconds,
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        return $next($request);
    }
}
