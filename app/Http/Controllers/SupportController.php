<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SupportController extends Controller
{
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
            $ticket = SupportTicket::create([
                'name' => $request->name,
                'email' => $request->email,
                'type' => $request->type ?? 'Other',
                'message' => $request->message,
                'ip_address' => $request->ip(),
                'status' => 'new',
                'priority' => 'normal',
            ]);

            // 3. Send to n8n
            $n8nSuccess = $this->sendToN8n($ticket);

            if (!$n8nSuccess) {
                Log::warning('Failed to send support ticket #' . $ticket->id . ' to n8n.');
            }

            return response()->json(['message' => 'Sent successfully']);

        } catch (\Exception $e) {
            Log::error('Support Ticket Error: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }

    private function sendToN8n($ticket)
    {
        $n8nUrl = 'https://n8ntabdil.n8ntabdil.online/form/51251c3f-c888-4fd0-976e-480226bd99d1';
        
        try {
            // Fix: n8n Form Trigger uses the Field Labels as keys for the input data.
            // Based on the workflow details, the labels are: "الاسم", "البريد الالكتروني", "نوع الرسالة", "الرسالة"
            
            // Map Type values if necessary (e.g., 'Inquiry' -> 'استفسار')
            // Assuming your values stored in DB match n8n dropdown options exactly or close enough.
            // If DB has English 'Inquiry' but n8n expects 'استفسار', we need mapping.
            // Let's assume the frontend sends Arabic values as they are in the select options?
            // In support-widget.blade.php values are: value="استفسار", value="اقتراح"...
            // So ticket->type is likely already Arabic.

            // Correct Mapping based on n8n Form HTML inspection:
            // field-0: Name (الاسم)
            // field-1: Email (البريد الالكتروني)
            // field-2: Type (نوع الرسالة) -> Values: 'استفسار', 'اقتراح', 'مشكلة/شكوى', 'اخرى'
            // field-3: Message (الرسالة)
            // field-4: Status (الحالة) -> Value: 'جديد'
            // field-5: Priority (الاولوية) -> Value: 'عادي'

            // Explicit mapping to handle potential encoding issues, spaces, or language variants
            $typeMapping = [
                'استفسار' => 'استفسار',
                'Inquiry' => 'استفسار',
                'اقتراح' => 'اقتراح',
                'Suggestion' => 'اقتراح',
                'مشكلة' => 'مشكلة',
                'Problem' => 'مشكلة',
                'مشكلة/شكوى' => 'مشكلة', // Map legacy value to new value
                'Complaint' => 'مشكلة',
                'اخرى' => 'اخرى',
                'Other' => 'اخرى',
                // Handle potential variations with spaces
                'مشكلة / شكوى' => 'مشكلة',
            ];

            $inputType = trim($ticket->type);
            $type = $typeMapping[$inputType] ?? 'اخرى';

            $data = [
                'field-0' => $ticket->name ?? 'Anonymous',
                'field-1' => $ticket->email ?? '',
                'field-2' => $type,
                'field-3' => $ticket->message,
                'field-4' => 'جديد',
                'field-5' => 'عادي',
            ];

            // Use asMultipart as proven by test script
            // Added withoutVerifying just in case SSL is strict on the server
            $response = Http::asMultipart()->withoutVerifying()->post($n8nUrl, $data);

            if ($response->successful() || $response->status() === 302) {
                Log::info('n8n Success: ' . $response->body());
                return true;
            } else {
                Log::error('n8n Field Error: ' . $response->status() . ' - ' . $response->body());
                // Fallback: If 500, maybe type matches but something else?
                return false;
            }
        } catch (\Exception $e) {
            Log::error('N8n Connection Exception: ' . $e->getMessage());
            return false;
        }
    }
}
