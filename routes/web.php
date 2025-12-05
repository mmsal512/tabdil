<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\AdminController; // Moved this use statement up

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
    // Quick stats or zeros
    $stats = [
        'total_requests' => \App\Models\AiRequestLog::count(),
        'today_requests' => \App\Models\AiRequestLog::whereDate('created_at', now()->today())->count(),
        'total_tokens' => \App\Models\AiRequestLog::sum('tokens'),
        'last_request' => \App\Models\AiRequestLog::latest()->first()->created_at ?? null,
    ];
    
    return view('admin.ai.studio', compact('stats'));
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
});

require __DIR__.'/auth.php';
