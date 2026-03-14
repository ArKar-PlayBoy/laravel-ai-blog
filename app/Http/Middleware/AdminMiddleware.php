<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access admin panel.');
        }

        $user = Auth::user();

        if ($user->is_banned) {
            Auth::logout();

            return redirect()->route('login')->with('error', 'Your account has been banned.');
        }

        $adminRoles = ['super_admin', 'admin'];
        $hasAdminRole = $this->userHasAdminRole($user, $adminRoles);

        if (! $hasAdminRole) {
            abort(403, 'Unauthorized access to admin panel.');
        }

        return $next($request);
    }

    private function userHasAdminRole($user, array $adminRoles): bool
    {
        $cacheKey = 'user_roles_' . $user->id;

        $roleNames = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user) {
            if (! $user->relationLoaded('roles')) {
                $user->load('roles');
            }
            return $user->roles->pluck('name')->toArray();
        });

        return !empty(array_intersect($adminRoles, $roleNames));
    }
}
