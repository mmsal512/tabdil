<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiRequestLog;

class AiController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * AI Studio Dashboard
     */
    public function studio()
    {
        $stats = [
            'total_requests' => AiRequestLog::count(),
            'today_requests' => AiRequestLog::whereDate('created_at', today())->count(),
            'total_tokens' => AiRequestLog::sum('tokens'),
            'recent_logs' => AiRequestLog::with('user')->latest()->take(10)->get(),
        ];

        return view('admin.ai.studio', compact('stats'));
    }

    /**
     * Content Writer Page
     */
    public function contentWriter()
    {
        return view('admin.ai.content-writer');
    }

    /**
     * AI Logs Page
     */
    public function logs()
    {
        $logs = AiRequestLog::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.ai.logs', compact('logs'));
    }
}
