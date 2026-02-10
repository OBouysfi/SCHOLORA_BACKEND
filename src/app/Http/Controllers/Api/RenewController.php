<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RenewController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:5',
        ]);

        try {

            $fromEmail = config('mail.from.address');
            $fromName  = config('mail.from.name');
            $toEmail   = config('services.contact.receiver');

            $html = "<p><strong>Request from:</strong> {$request->email}</p>";
            $html .= "<hr>";
            $html .= "<p>" . nl2br(e($request->message)) . "</p>";

            $response = Http::withToken(config('services.resend.key'))
                ->post('https://api.resend.com/emails', [
                    'from' => "{$fromName} <{$fromEmail}>",
                    'to' => [$toEmail],
                    'subject' => $request->subject,
                    'html' => $html,
                ]);

            Log::info('Renew email sent', $response->json());

            return response()->json([
                'success' => true,
                'message' => 'Renew request sent',
            ]);

        } catch (\Throwable $e) {

            Log::error('Renew email failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send renew request',
            ], 500);
        }
    }
}
