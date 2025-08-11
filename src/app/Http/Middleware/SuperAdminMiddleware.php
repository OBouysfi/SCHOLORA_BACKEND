<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé. Seuls les super administrateurs peuvent accéder à cette ressource.'
            ], 403);
        }

        return $next($request);
    }
}