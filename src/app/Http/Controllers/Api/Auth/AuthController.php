<?php

// app/Http/Controllers/Api/Auth/AuthController.php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        $credentials['is_active'] = true;

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants invalides'
            ], 401);
        }

        $user = auth('api')->user();
        $user->update(['last_login_at' => now()]);

        return $this->respondWithToken($token);
    }

    public function me()
    {
        if (!auth('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        $user = auth('api')->user();
        $user->load(['roles']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'roles' => $user->roles->pluck('name'),
                'is_super_admin' => $user->isSuperAdmin(),
                'last_login_at' => $user->last_login_at
            ]
        ]);
    }

    public function logout()
    {
        if (auth('api')->check()) {
            auth('api')->logout();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }

    public function refresh()
    {
        if (!auth('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Token invalide'
            ], 401);
        }

        return $this->respondWithToken(auth('api')->refresh());
    }

    protected function respondWithToken($token)
    {
        $user = auth('api')->user();
        
        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => 3600, // 1 heure en dur pour éviter l'erreur factory
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name'),
                    'is_super_admin' => $user->isSuperAdmin()
                ]
            ]
        ]);
    }
}