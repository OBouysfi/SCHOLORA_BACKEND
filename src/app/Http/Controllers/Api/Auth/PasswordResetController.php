<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    /**
     * Envoyer le lien de réinitialisation par email
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255'
        ]);

        try {
            $user = User::where('email', $request->email)->first();
            
            // Best practice: Toujours retourner le même message pour éviter l'enumération d'emails
            if (!$user) {
                return response()->json([
                    'message' => 'Si cet email existe dans notre système, un lien de réinitialisation a été envoyé.'
                ], 200);
            }

            // Générer le token de réinitialisation
            $token = Password::createToken($user);
            
            // Construire l'URL de réinitialisation
            $frontendUrl = env('APP_URL_FRONT', 'http://localhost:3000');
            $url = "{$frontendUrl}/password/reset/{$token}?email=" . urlencode($user->email);

            // Envoyer l'email
            Mail::to($user->email)->send(new PasswordResetMail($user, $url));

            // Log pour le débogage (en développement seulement)
            if (env('APP_DEBUG')) {
                Log::info('Password reset link sent', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'token' => $token
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Un lien de réinitialisation a été envoyé à votre adresse email.'
            ]);

        } catch (\Exception $e) {
            // Log l'erreur complète pour le débogage
            Log::error('Password reset error: ' . $e->getMessage(), [
                'exception' => $e,
                'email' => $request->email
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'envoi du lien de réinitialisation.'
            ], 500);
        }
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => bcrypt($password)
                    ])->save();

                    // Révoquer tous les tokens existants (sécurité)
                    $user->tokens()->delete();
                }
            );

            return $status === Password::PASSWORD_RESET
                ? response()->json(['message' => __($status)], 200)
                : response()->json(['message' => __($status)], 400);

        } catch (\Exception $e) {
            Log::error('Password reset completion error: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la réinitialisation du mot de passe.'
            ], 500);
        }
    }
}