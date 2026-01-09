<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        $contact = ContactMessage::create([
            'user_id' => null,
            'name'    => $request->name,
            'email'   => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);
        // Resend usage
        try {
            $fromEmail = config('mail.from.address');
            $fromName  = config('mail.from.name');
            $toEmail   = config('services.contact.receiver');
            $html = '<p><strong>Name:</strong> ' . e($contact->name) . '</p>';
            $html .= '<p><strong>Email:</strong> ' . e($contact->email) . '</p>';
            $html .= '<hr>';
            $html .= '<p>' . nl2br(e($contact->message)) . '</p>';

            $response = Http::withToken(config('services.resend.key'))
                ->post('https://api.resend.com/emails', [
                    'from' => "{$fromName} <{$fromEmail}>",
                    'to' => [$toEmail],
                    'subject' => 'Contact Form Message: ' . $contact->subject,
                    'html' => $html,
                ]);

            Log::info('Resend response', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
        } catch (\Throwable $e) {
        Log::error('Resend email failed', [
            'error' => $e->getMessage(),
        ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Message envoyé avec succès',
        ], 201);
    }
}