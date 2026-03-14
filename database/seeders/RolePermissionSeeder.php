<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'access_admin_panel', 'display_name' => 'Access Admin Panel', 'description' => 'Can access the admin panel'],
            ['name' => 'manage_users', 'display_name' => 'Manage Users', 'description' => 'Create, edit, delete users'],
            ['name' => 'ban_users', 'display_name' => 'Ban Users', 'description' => 'Ban or unban users'],
            ['name' => 'manage_posts', 'display_name' => 'Manage Posts', 'description' => 'Create, edit, delete any posts'],
            ['name' => 'approve_posts', 'display_name' => 'Approve Posts', 'description' => 'Approve or archive posts'],
            ['name' => 'manage_categories', 'display_name' => 'Manage Categories', 'description' => 'Create, edit, delete categories'],
            ['name' => 'view_audit_logs', 'display_name' => 'View Audit Logs', 'description' => 'View system audit logs'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        $superAdmin = Role::create([
            'name' => 'super_admin',
            'display_name' => 'Super Admin',
            'description' => 'Full access to all features',
            'is_active' => true,
        ]);

        $admin = Role::create([
            'name' => 'admin',
            'display_name' => 'Admin',
            'description' => 'Administrative access',
            'is_active' => true,
        ]);

        $superAdmin->permissions()->attach(Permission::all()->pluck('id'));
        $admin->permissions()->attach(Permission::whereNotIn('name', ['view_audit_logs'])->pluck('id'));
    }
}
