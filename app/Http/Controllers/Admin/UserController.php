<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'posts', 'comments']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['roles', 'posts.category', 'comments.post']);

        $userStats = [
            'posts_count' => $user->posts()->count(),
            'comments_count' => $user->comments()->count(),
            'likes_given' => $user->likedPosts()->count(),
        ];

        $recentActivity = AuditLog::where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        return view('admin.users.show', compact('user', 'userStats', 'recentActivity'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $currentUser = auth()->user();

        if (! $currentUser->hasPermission('manage_users')) {
            abort(403, 'You do not have permission to manage users.');
        }

        $isSuperAdmin = $currentUser->hasRole('super_admin');
        $isAdmin = $currentUser->hasRole('admin');
        $isModerator = $currentUser->hasRole('moderator');
        $isEditor = $currentUser->hasRole('editor');

        if ($user->hasRole('super_admin') && ! $isSuperAdmin) {
            abort(403, 'Only super admins can edit other super admin accounts.');
        }

        if ($user->hasRole('admin') && ! $isSuperAdmin && ! $isAdmin) {
            abort(403, 'You cannot edit admin accounts.');
        }

        if (($user->hasRole('moderator') || $user->hasRole('editor')) && ! $isSuperAdmin && ! $isAdmin) {
            abort(403, 'You cannot edit moderator or editor accounts.');
        }

        if (!$isSuperAdmin && !$isAdmin && !$isModerator && !$isEditor) {
            abort(403, 'You do not have permission to edit user accounts.');
        }

        if ($isSuperAdmin) {
            $roles = Role::all();
        } elseif ($isAdmin) {
            $roles = Role::whereNotIn('name', ['super_admin'])->get();
        } else {
            $roles = Role::where('name', 'user')->get();
        }

        $userRoles = $user->roles->pluck('id')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'userRoles', 'isSuperAdmin', 'isAdmin', 'isModerator', 'isEditor'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $currentUser = auth()->user();
        $currentUser->load('roles.permissions');

        if (! $currentUser->hasPermission('manage_users')) {
            abort(403, 'You do not have permission to manage users.');
        }

        $isSuperAdmin = $currentUser->hasRole('super_admin');
        $isAdmin = $currentUser->hasRole('admin');
        $isModerator = $currentUser->hasRole('moderator');
        $isEditor = $currentUser->hasRole('editor');

        if ($user->hasRole('super_admin') && ! $isSuperAdmin) {
            abort(403, 'Only super admins can edit other super admin accounts.');
        }

        if ($user->hasRole('admin') && ! $isSuperAdmin && ! $isAdmin) {
            abort(403, 'You cannot edit admin accounts.');
        }

        if (($user->hasRole('moderator') || $user->hasRole('editor')) && ! $isSuperAdmin && ! $isAdmin) {
            abort(403, 'You cannot edit moderator or editor accounts.');
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ];

        if ($isSuperAdmin) {
            $rules['roles'] = ['array'];
            $rules['roles.*'] = ['exists:roles,id'];
        }

        $validated = $request->validate($rules);

        $oldValues = $user->toArray();

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        $user->update($updateData);

        if (($isSuperAdmin || $isAdmin) && $request->has('role_id')) {
            $user->roles()->sync([$request->input('role_id')]);
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'resource_type' => 'User',
            'resource_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => $user->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Ban the specified user.
     */
    public function ban(Request $request, User $user)
    {
        $currentUser = auth()->user();

        if (! $currentUser->hasPermission('ban_users')) {
            abort(403, 'You do not have permission to ban users.');
        }

        if ($user->id === $currentUser->id) {
            return back()->with('error', 'You cannot ban yourself.');
        }

        if ($user->hasRole('super_admin') && ! $currentUser->hasRole('super_admin')) {
            return back()->with('error', 'You cannot ban a super admin.');
        }

        if ($user->hasRole('super_admin') && $currentUser->hasRole('super_admin') && $user->id !== $currentUser->id) {
            return back()->with('error', 'Only super admin can ban another super admin.');
        }

        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $oldValues = $user->toArray();

        $user->update([
            'is_banned' => true,
            'banned_at' => now(),
            'banned_reason' => $request->reason,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'ban',
            'resource_type' => 'User',
            'resource_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => $user->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'metadata' => ['reason' => $request->reason],
        ]);

        return back()->with('success', 'User has been banned.');
    }

    /**
     * Unban the specified user.
     */
    public function unban(Request $request, User $user)
    {
        $currentUser = auth()->user();

        if (! $currentUser->hasPermission('ban_users')) {
            abort(403, 'You do not have permission to unban users.');
        }

        if ($user->hasRole('super_admin') && ! $currentUser->hasRole('super_admin')) {
            return back()->with('error', 'Only super admins can unban another super admin.');
        }

        $oldValues = $user->toArray();

        $user->update([
            'is_banned' => false,
            'banned_at' => null,
            'banned_reason' => null,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'unban',
            'resource_type' => 'User',
            'resource_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => $user->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return back()->with('success', 'User has been unbanned.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user)
    {
        $currentUser = auth()->user();

        if (! $currentUser->hasPermission('manage_users')) {
            abort(403, 'You do not have permission to delete users.');
        }

        if ($user->id === $currentUser->id) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        if ($user->hasRole('super_admin') && ! $currentUser->hasRole('super_admin')) {
            return back()->with('error', 'You cannot delete a super admin.');
        }

        $oldValues = $user->toArray();

        $user->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete',
            'resource_type' => 'User',
            'resource_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Bulk action on users.
     */
    public function bulkAction(Request $request)
    {
        $currentUser = auth()->user();

        $request->validate([
            'action' => ['required', 'in:ban,unban,delete'],
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $action = $request->action;
        if (in_array($action, ['ban', 'unban'])) {
            if (! $currentUser->hasPermission('ban_users')) {
                abort(403, 'You do not have permission to ban/unban users.');
            }
        } elseif ($action === 'delete') {
            if (! $currentUser->hasPermission('manage_users')) {
                abort(403, 'You do not have permission to delete users.');
            }
        }

        $isSuperAdmin = $currentUser->hasRole('super_admin');

        $userIds = array_diff($request->user_ids, [auth()->id()]);

        if (! $isSuperAdmin) {
            $superAdminIds = User::whereHas('roles', function ($q) {
                $q->where('name', 'super_admin');
            })->pluck('id')->toArray();
            $userIds = array_diff($userIds, $superAdminIds);
        }

        if (empty($userIds)) {
            return back()->with('error', 'No valid users selected for this action.');
        }

        switch ($request->action) {
            case 'ban':
                User::whereIn('id', $userIds)->update([
                    'is_banned' => true,
                    'banned_at' => now(),
                    'banned_reason' => 'Bulk ban action',
                ]);
                $message = 'Selected users have been banned.';
                break;
            case 'unban':
                User::whereIn('id', $userIds)->update([
                    'is_banned' => false,
                    'banned_at' => null,
                    'banned_reason' => null,
                ]);
                $message = 'Selected users have been unbanned.';
                break;
            case 'delete':
                User::whereIn('id', $userIds)->delete();
                $message = 'Selected users have been deleted.';
                break;
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'bulk_'.$request->action,
            'resource_type' => 'User',
            'resource_id' => 0,
            'old_values' => null,
            'new_values' => ['user_ids' => $userIds],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'metadata' => ['count' => count($userIds)],
        ]);

        return back()->with('success', $message);
    }
}
