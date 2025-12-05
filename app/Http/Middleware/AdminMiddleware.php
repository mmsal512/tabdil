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

        // --- DEBUG START: DELETE AFTER FIXING ---
        // هذا الكود سيوقف الصفحة ويطبع بيانات المستخدم. 
        // اذا ظهر لك هذا المربع، فالكود الجديد وصل للسيرفر.
        // ارسل لي صورة لما بداخله.
        if ($request->user()->email === 'admin@tabdil.com') {
             dd([
                'My ID' => $request->user()->id,
                'My Email' => $request->user()->email,
                'My User Type (DB)' => $request->user()->user_type,
                'Length' => strlen($request->user()->user_type),
                'Check Result' => trim(strtolower($request->user()->user_type)) === 'admin' ? 'SUCCESS' : 'FAIL',
             ]);
        }
        // --- DEBUG END ---

        // Check if user is admin (case insensitive and trimmed)
        if (trim(strtolower($request->user()->user_type)) !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
