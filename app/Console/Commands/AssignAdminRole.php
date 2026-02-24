<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class AssignAdminRole extends Command
{
    protected $signature = 'admin:assign-role {email} {role=super_admin}';

    protected $description = 'Assign an admin role to a user';

    public function handle()
    {
        $email = $this->argument('email');
        $roleName = $this->argument('role');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email {$email} not found.");

            return 1;
        }

        $role = Role::where('name', $roleName)->first();

        if (! $role) {
            $this->error("Role {$roleName} not found.");
            $this->info('Available roles: super_admin, admin, moderator, editor');

            return 1;
        }

        $user->roles()->syncWithoutDetaching([$role->id]);

        $this->info("Role {$role->display_name} assigned to {$user->name} ({$user->email}) successfully!");

        return 0;
    }
}
