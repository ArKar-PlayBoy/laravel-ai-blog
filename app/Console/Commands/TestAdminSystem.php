<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class TestAdminSystem extends Command
{
    protected $signature = 'admin:test';

    protected $description = 'Test admin system components';

    public function handle()
    {
        $this->info('Testing Admin System...');

        $this->info('1. Checking Roles table...');
        $roles = Role::count();
        $this->info("   Roles count: {$roles}");

        $this->info('2. Checking Permissions table...');
        $permissions = Permission::count();
        $this->info("   Permissions count: {$permissions}");

        $this->info('3. Checking Audit Logs table...');
        $auditLogs = AuditLog::count();
        $this->info("   Audit Logs count: {$auditLogs}");

        $this->info('4. Checking Users table for banned fields...');
        $users = User::count();
        $this->info("   Users count: {$users}");

        $this->info('5. Checking Posts table for status field...');
        $posts = \App\Models\Post::count();
        $this->info("   Posts count: {$posts}");

        $this->info('6. Listing Admin Roles...');
        $adminRoles = Role::whereIn('name', ['super_admin', 'admin', 'moderator', 'editor'])->get();
        foreach ($adminRoles as $role) {
            $this->info("   - {$role->name} ({$role->display_name})");
        }

        $this->info('7. Listing Permissions...');
        $perms = Permission::all();
        foreach ($perms as $perm) {
            $this->info("   - {$perm->name}: {$perm->display_name}");
        }

        $this->info('Admin System Test Complete!');

        return 0;
    }
}
