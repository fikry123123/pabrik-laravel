<?php

namespace App\Helpers;

use App\Models\UserPermission;
use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Check apakah user memiliki permission untuk feature tertentu
     */
    public static function can(string $feature, string $permission = 'can_view'): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // Admin always has access
        if ($user->isAdmin()) {
            return true;
        }

        // Semua fitur dapat dilihat. Checklist per akun memberikan hak kelola.
        if ($permission === 'can_view') {
            return true;
        }

        return UserPermission::where('user_id', $user->id)
            ->where('feature', $feature)
            ->where('can_manage', true)
            ->exists();
    }

    /**
     * Check apakah user memiliki create permission untuk feature tertentu
     */
    public static function canCreate(string $feature): bool
    {
        return self::can($feature, 'can_create');
    }

    /**
     * Check apakah user memiliki update permission untuk feature tertentu
     */
    public static function canUpdate(string $feature): bool
    {
        return self::can($feature, 'can_update');
    }

    /**
     * Check apakah user memiliki delete permission untuk feature tertentu
     */
    public static function canDelete(string $feature): bool
    {
        return self::can($feature, 'can_delete');
    }

    /**
     * Check apakah user memiliki view permission untuk feature tertentu
     */
    public static function canView(string $feature): bool
    {
        return self::can($feature, 'can_view');
    }
}
