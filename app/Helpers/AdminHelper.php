<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class AdminHelper
{
    public static function isAdmin(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        $adminRoles = ['super_admin', 'admin', 'moderator', 'editor'];

        return Auth::user()->roles()->whereIn('name', $adminRoles)->exists();
    }

    public static function isSuperAdmin(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole('super_admin');
    }

    public static function canManageUsers(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('admin');
    }

    public static function canManagePosts(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('admin') || Auth::user()->hasRole('editor');
    }

    public static function canApprovePosts(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('admin') || Auth::user()->hasRole('moderator');
    }

    public static function canManageCategories(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('admin');
    }

    public static function getAdminRoles(): array
    {
        return ['super_admin', 'admin', 'moderator', 'editor'];
    }

    public static function getRoleDisplayName(string $roleName): string
    {
        $roles = [
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'moderator' => 'Moderator',
            'editor' => 'Editor',
        ];

        return $roles[$roleName] ?? $roleName;
    }
}
