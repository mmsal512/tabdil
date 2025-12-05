<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login')->with('error', 'Please login to access this area.');
        }

        // Check if user is admin (case insensitive and trimmed)
        $userType = trim(strtolower($request->user()->user_type));
        
        if ($userType !== 'admin') {
            // Debug info in flash message
            return redirect()->route('dashboard')->with('error', 'Access Denied. Found user_type: [' . $userType . ']');
        }

        return $next($request);

        return $next($request);
    }
}
