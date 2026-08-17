<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPermission;
use Symfony\Component\HttpFoundation\Response;

class CheckFeaturePermission
{
    public function handle(Request $request, Closure $next, string $feature, string $permission = 'can_view'): Response
    {
        if (!Auth::check()) {
            return response('Unauthorized', 401);
        }

        $user = Auth::user();

        // Admin always has access
        if ($user->isAdmin()) {
            return $next($request);
        }

        $allowed = $permission === 'can_view' || UserPermission::where('user_id', $user->id)
            ->where('feature', $feature)
            ->where('can_manage', true)
            ->exists();

        if (!$allowed) {
            // Log attempt
            logger()->warning("User {$user->username} attempted to access restricted feature: {$feature} ({$permission})");

            return response('Fitur tidak dapat diakses', 403);
        }

        return $next($request);
    }
}
