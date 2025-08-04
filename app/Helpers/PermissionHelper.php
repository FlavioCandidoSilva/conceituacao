<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    public static function userCan(string $permission): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }

        return $user->roles->contains(function ($role) use ($permission) {
            return is_array($role->permissions) && in_array($permission, $role->permissions);
        });
    }
    public static function userCanOrSelf(string $permission, int $targetUserId): bool
    {
        $user = Auth::user();

        return self::userCan($permission) || ($user && $user->id === $targetUserId);
    }
}
