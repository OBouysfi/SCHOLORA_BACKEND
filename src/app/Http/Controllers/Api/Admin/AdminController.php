<?php

// app/Http/Controllers/Api/Admin/AdminController.php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'super.admin']);
    }

    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_roles' => Role::count(),
            'recent_logins' => User::whereNotNull('last_login_at')
                                 ->where('last_login_at', '>=', now()->subDays(7))
                                 ->count()
        ];

        $recentUsers = User::with('roles')
                          ->latest()
                          ->limit(5)
                          ->get()
                          ->map(function ($user) {
                              return [
                                  'id' => $user->id,
                                  'full_name' => $user->full_name,
                                  'email' => $user->email,
                                  'roles' => $user->roles->pluck('display_name'),
                                  'created_at' => $user->created_at->format('Y-m-d H:i')
                              ];
                          });

        return response()->json([
            'success' => true,
            'message' => 'Dashboard Super Admin',
            'data' => [
                'stats' => $stats,
                'recent_users' => $recentUsers,
                'user' => auth()->user()->only([
                    'id', 'first_name', 'last_name', 'email'
                ])
            ]
        ]);
    }

    public function users(Request $request)
    {
        $query = User::with('roles');

        // Filtrage par statut
        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Recherche
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate($request->get('per_page', 15));

        $users->getCollection()->transform(function ($user) {
            return [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'is_active' => $user->is_active,
                'roles' => $user->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'display_name' => $role->display_name
                    ];
                }),
                'last_login_at' => $user->last_login_at?->format('Y-m-d H:i'),
                'created_at' => $user->created_at->format('Y-m-d H:i')
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function roles()
    {
        $roles = Role::with('permissions')
                    ->where('is_active', true)
                    ->get()
                    ->map(function ($role) {
                        return [
                            'id' => $role->id,
                            'name' => $role->name,
                            'display_name' => $role->display_name,
                            'description' => $role->description,
                            'permissions_count' => $role->permissions->count(),
                            'users_count' => $role->users->count()
                        ];
                    });

        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }
}