<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SupportController extends Controller
{
    protected $supportService;

    public function __construct(\App\Services\SupportService $supportService)
    {
        $this->supportService = $supportService;
    }

    public function send(Request $request)
    {
        // 1. Validate Request
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:3',
            'name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            // 2. Save to Local Database FIRST
            // Prepare Data with Backend Fallback for Logged In Users
            $name = $request->name;
            $email = $request->email;

            if (auth()->check()) {
                $user = auth()->user();
                // Fallback to user data if fields are empty
                if (empty($name)) {
                    $name = $user->name;
                }
                if (empty($email)) {
                    $email = $user->email;
                }
            }

            // 2. Save to Local Database FIRST
            $ticket = SupportTicket::create([
                'name' => $name,
                'email' => $email,
                'type' => $request->type ?? 'Other',
                'message' => $request->message,
                'ip_address' => $request->ip(),
                'status' => 'new',
                'priority' => 'normal',
            ]);

            // 3. Send to n8n using Service
            $n8nSuccess = $this->supportService->sendToN8n($ticket);

            if ($n8nSuccess) {
                $ticket->update(['synced_to_n8n' => true]);
            } else {
                Log::warning('Failed to send support ticket #' . $ticket->id . ' to n8n. Will be synced later via scheduled task.');
            }

            return response()->json(['message' => 'Sent successfully']);

        } catch (\Exception $e) {
            Log::error('Support Ticket Error: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }
}
