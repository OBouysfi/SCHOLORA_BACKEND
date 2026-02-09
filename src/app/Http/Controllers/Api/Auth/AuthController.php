<?php

// app/Http/Controllers/Api/Auth/AuthController.php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
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
        $user->update(['last_login_at' => now(),
                        'is_online' => true]);

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
        $user->load(['roles', 'tutor.pricingPack']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'roles' => $user->roles->pluck('name'),
                'is_super_admin' => $user->isSuperAdmin(),
                'last_login_at' => $user->last_login_at,
                'tutor' => $user->tutor ? [
                    'id' => $user->tutor->id,
                    'pack_subscribed_at' => $user->tutor->pack_subscribed_at,
                    'pack_expires_at' => $user->tutor->pack_expires_at,
                    'pricing_pack' => $user->tutor->pricingPack ? [
                        'id' => $user->tutor->pricingPack->id,
                        'name' => $user->tutor->pricingPack->name,
                        'slug' => $user->tutor->pricingPack->slug,
                        'price' => $user->tutor->pricingPack->price,
                    ] : null,
                ] : null
            ]
        ]);
    }

    public function logout()
    {
        $user = auth('api')->user();
        if (auth('api')->check()) {
            $user->update(['is_online' => false]);
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
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'roles' => $user->roles->pluck('name'),
                    'is_super_admin' => $user->isSuperAdmin()
                ]
            ]
        ]);
    }
    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();

        $data = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name'  => 'sometimes|string|max:255',
            'email'      => 'sometimes|email|unique:users,email,' . $user->id,
            'phone'      => 'nullable|string|max:50',
            'address'    => 'nullable|string|max:255',
        ]);

        $user->update($data);

        $user->load('roles');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'roles' => $user->roles->pluck('name'),
                'is_super_admin' => $user->isSuperAdmin(),
            ]
        ]);
    }

    public function userStats()
    {
        $totalUsers = User::count();
        $totalOnlineUsers = User::where('is_online', true)->count();

        return response()->json([
            'totalUsers' => $totalUsers,
            'totalOnlineUsers' => $totalOnlineUsers,
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:6',
            'role'       => 'required|in:student,tutor'
        ]);

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'is_active'  => true
        ]);

        // Attach role
        $roleId = $data['role'] === 'student' ? 2 : 3;
        $user->roles()->attach($roleId);
        if ($data['role'] === 'student') {
        Student::create([
                'user_id' => $user->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name']
            ]);
        }
        return response()->json([
            'success' => true,
            'user' => $user->load('roles')
        ], 201);
    }
}