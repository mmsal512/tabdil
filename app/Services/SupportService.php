<?php

namespace App\Services;

use App\Models\SupportTicket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupportService
{
    public function sendToN8n(SupportTicket $ticket)
    {
        $n8nUrl = 'https://n8ntabdil.n8ntabdil.online/form/51251c3f-c888-4fd0-976e-480226bd99d1';
        
        try {
            // Explicit mapping to handle potential encoding issues, spaces, or language variants
            $typeMapping = [
                'استفسار' => 'استفسار',
                'Inquiry' => 'استفسار',
                'اقتراح' => 'اقتراح',
                'Suggestion' => 'اقتراح',
                'مشكلة' => 'مشكلة',
                'Problem' => 'مشكلة',
                'مشكلة/شكوى' => 'مشكلة',
                'Complaint' => 'مشكلة',
                'اخرى' => 'اخرى',
                'Other' => 'اخرى',
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

            $response = Http::asMultipart()->withoutVerifying()->post($n8nUrl, $data);

            if ($response->successful() || $response->status() === 302) {
                Log::info('n8n Success for ticket #' . $ticket->id);
                return true;
            } else {
                Log::error('n8n Field Error for ticket #' . $ticket->id . ': ' . $response->status());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('N8n Connection Exception for ticket #' . $ticket->id . ': ' . $e->getMessage());
            return false;
        }
    }
}
