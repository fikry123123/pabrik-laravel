<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Izinkan akses hanya untuk role tertentu.
     *
     * Penggunaan di routes:
     *   ->middleware('role:admin')
     *   ->middleware('role:admin,editor')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Akses ditolak. Role Anda tidak memiliki izin.',
                ], 403);
            }

            abort(403, 'Akses Ditolak.');
        }

        return $next($request);
    }
}
