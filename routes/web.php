<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\VisitorController;
use App\Http\Controllers\SupportController;

// Locale Switching
Route::get('/locale/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
        
        // Update user preference if authenticated
        if (auth()->check()) {
            auth()->user()->update(['language' => $locale]);
        }
    }
    return redirect()->back();
})->name('locale.switch');

Route::get('/fix-system', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    return 'System Optimized and Cache Cleared!';
});

Route::get('/test-ai', function () {
    $debug = [
        'config_default' => config('ai.default'),
        'config_key_masked' => substr(config('ai.openrouter.api_key'), 0, 10) . '...',
        'service_check' => [],
    ];

    try {
        // Direct API Check
        $apiKey = config('ai.openrouter.api_key');
        $model = config('ai.openrouter.model');
        
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
            'HTTP-Referer' => config('app.url'),
            'X-Title' => config('app.name'),
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => 'Hello']],
        ]);

        $debug['direct_api_status'] = $response->status();
        $debug['direct_api_body'] = $response->json();

        // AiService Class Check
        $service = new \App\Services\AiService();
        $debug['service_response'] = $service->chat('Hello from Service');

    } catch (\Exception $e) {
        $debug['error'] = $e->getMessage();
        $debug['trace'] = $e->getTraceAsString();
    }

    return $debug;
});

// Direct AI Studio Route - Bypassing everything
Route::get('/smart-studio', function () {
    $user = Auth::user();
    $isAdmin = $user && $user->user_type === 'admin';
    
    // Admin sees global stats, regular user sees personal stats
    if ($isAdmin) {
        $stats = [
            'total_requests' => \App\Models\AiRequestLog::count(),
            'today_requests' => \App\Models\AiRequestLog::whereDate('created_at', now()->today())->count(),
            'total_tokens' => \App\Models\AiRequestLog::sum('tokens'),
        ];
    } else {
        $stats = [
            'total_requests' => \App\Models\AiRequestLog::where('user_id', $user->id)->count(),
            'today_requests' => \App\Models\AiRequestLog::where('user_id', $user->id)->whereDate('created_at', now()->today())->count(),
            'total_tokens' => \App\Models\AiRequestLog::where('user_id', $user->id)->sum('tokens'),
        ];
    }
    
    return view('admin.ai.studio', compact('stats', 'isAdmin'));
})->middleware(['auth'])->name('ai.studio.direct');

// Direct Content Writer Route
Route::get('/smart-writer', function () {
    return view('admin.ai.content-writer');
})->middleware(['auth'])->name('ai.writer.direct');

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/converter', [CurrencyController::class, 'index'])->name('currency.index');
Route::post('/convert', [CurrencyController::class, 'convert'])->name('currency.convert');
Route::get('/convert', function () {
    return redirect()->route('currency.index');
});

// Helpful redirects for common mistakes
Route::get('/favorites', function () {
    return redirect()->route('dashboard')->with('error', 'Favorites are accessible from your dashboard.');
});
Route::get('/auth/favorites', function () {
    return redirect()->route('dashboard')->with('error', 'Favorites are accessible from your dashboard.');
});
Route::get('/rates', function () {
    return redirect()->route('currency.index');
});

Route::get('/comparison', [CurrencyController::class, 'getComparisonRates'])->name('currency.comparison');

// Support Widget Route
Route::post('/support/send', [SupportController::class, 'send'])->name('support.send');

use App\Http\Controllers\FavoriteController;

Route::get('/dashboard', [FavoriteController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/favorites', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/bulk-delete', [FavoriteController::class, 'destroyMany'])->name('favorites.destroyMany');
    Route::delete('/favorites/{favorite}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    
    // Backup Rates Routes
    Route::get('/backup-rates', [AdminController::class, 'backupRates'])->name('backup-rates');
    Route::post('/backup-rates', [AdminController::class, 'updateBackupRates'])->name('backup-rates.update');
    
    // API Settings Routes
    Route::get('/api-settings', [AdminController::class, 'apiSettings'])->name('api-settings');
    Route::post('/api-settings', [AdminController::class, 'updateApiSettings'])->name('api-settings.update');
    Route::post('/test-api-connection', [AdminController::class, 'testApiConnection'])->name('test-api-connection');
    
    // User Management Routes
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/reset-password', [AdminController::class, 'resetUserPassword'])->name('users.reset-password');

    // Support Tickets Routes
    Route::get('/support', [SupportTicketController::class, 'index'])->name('support.index');
    Route::patch('/support/{ticket}/status', [SupportTicketController::class, 'updateStatus'])->name('support.updateStatus');
    Route::delete('/support/{ticket}', [SupportTicketController::class, 'destroy'])->name('support.destroy');

    // Visitor Analytics Routes
    Route::get('/visitors', [VisitorController::class, 'index'])->name('visitors.index');
    Route::get('/visitors/settings', [VisitorController::class, 'settings'])->name('visitors.settings');
    Route::post('/visitors/settings', [VisitorController::class, 'updateSettings'])->name('visitors.settings.update');
    Route::post('/visitors/test-notification', [VisitorController::class, 'sendTestNotification'])->name('visitors.test-notification');
    Route::get('/visitors/chart-data', [VisitorController::class, 'chartData'])->name('visitors.chart-data');
});

// External Cron Webhook for Support Ticket Sync
// This can be called by services like cron-job.org every minute
Route::get('/cron/sync-support/{secret}', function ($secret) {
    // Verify secret key to prevent unauthorized access
    if ($secret !== config('app.cron_secret', 'tabdil-sync-512')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    // Run the sync command
    \Illuminate\Support\Facades\Artisan::call('support:sync-n8n');
    $output = \Illuminate\Support\Facades\Artisan::output();
    
    return response()->json([
        'status' => 'success',
        'output' => $output,
        'timestamp' => now()->toDateTimeString()
    ]);
});

// External Cron Webhook for Visitor Reports
// This can be called by services like cron-job.org every minute
Route::get('/cron/visitor-report/{secret}', function ($secret) {
    // Verify secret key to prevent unauthorized access
    if ($secret !== config('app.cron_secret', 'tabdil-sync-512')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    // Run the visitor report command
    \Illuminate\Support\Facades\Artisan::call('visitors:send-report');
    $output = \Illuminate\Support\Facades\Artisan::output();
    
    return response()->json([
        'status' => 'success',
        'output' => $output,
        'timestamp' => now()->toDateTimeString()
    ]);
});

// External Cron Webhook for Visitor Smart Alerts
Route::get('/cron/visitor-alerts/{secret}', function ($secret) {
    // Verify secret key to prevent unauthorized access
    if ($secret !== config('app.cron_secret', 'tabdil-sync-512')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    // Run the alerts check command
    \Illuminate\Support\Facades\Artisan::call('visitors:check-alerts');
    $output = \Illuminate\Support\Facades\Artisan::output();
    
    return response()->json([
        'status' => 'success',
        'output' => $output,
        'timestamp' => now()->toDateTimeString()
    ]);
});

Route::get('/debug-error', function() {
    try {
        echo "<h1>🔍 Deep Diagnostic</h1>";
        
        // 1. Check getStats (Already passed)
        $todayStats = \App\Models\Visitor::getStats(today());
        echo "✅ getStats (Today) OK<br>";
        
        $yesterdayStats = \App\Models\Visitor::getStats(today()->subDay(), today()->subDay()->endOfDay());
        echo "✅ getStats (Yesterday) OK<br>";
        
        // 2. Check getDailyStats (Potential Issue)
        $dailyChartData = \App\Models\Visitor::getDailyStats(7);
        echo "✅ getDailyStats OK (" . count($dailyChartData) . " days)<br>";
        
        // 3. Check getHourlyStats (Potential Issue - SQL Group By)
        $hourlyChartData = \App\Models\Visitor::getHourlyStats();
        echo "✅ getHourlyStats OK (" . count($hourlyChartData) . " hours)<br>";
        
        // 4. Other Calculations
        $weeklyStats = \App\Models\Visitor::getStats(now()->startOfWeek());
        $monthlyStats = \App\Models\Visitor::getStats(now()->startOfMonth());
        
        $realtimeVisitors = \App\Models\Visitor::humans()
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count();
            
        // Mock the controller method
        $controller = new \App\Http\Controllers\Admin\VisitorController();
        // Since calculatePercentChange is protected, we'll mimic it simple here or reflect it, 
        // but let's assume it works as it's pure PHP.
        // Or better, let's just try to render the View directly with this data!
        
        $visitorChange = [
            'value' => 0,
            'direction' => 'neutral' 
        ];

        echo "✅ All Data Fetched! Attempting to render View...<br>";
        
        // 5. Try Rendering the View
        $view = view('admin.visitors.index', compact(
            'todayStats',
            'yesterdayStats',
            'weeklyStats',
            'monthlyStats',
            'dailyChartData',
            'hourlyChartData',
            'realtimeVisitors',
            'visitorChange'
        ))->render();
        
        echo "✅ View Rendered Successfully! (Preview below)<br><hr>";
        return $view;
        
    } catch (\Exception $e) {
        return "<div style='background:#fee; color:#b00; padding:20px; border:2px solid #f00;'>
            <h1>🚨 FATAL ERROR FOUND:</h1>
            <h2>" . $e->getMessage() . "</h2>
            <strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "<br>
            <pre>" . $e->getTraceAsString() . "</pre>
        </div>";
    }
});

require __DIR__.'/auth.php';
