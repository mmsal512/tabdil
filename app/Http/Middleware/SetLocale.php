<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user has a session locale
        if (Session::has('locale')) {
            $locale = Session::get('locale');
        } 
        // Check if authenticated user has a language preference
        elseif (auth()->check() && auth()->user()->language) {
            $locale = auth()->user()->language;
            Session::put('locale', $locale);
        }
        // Default to app locale
        else {
            $locale = config('app.locale');
        }

        // Validate locale
        if (in_array($locale, ['en', 'ar'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
