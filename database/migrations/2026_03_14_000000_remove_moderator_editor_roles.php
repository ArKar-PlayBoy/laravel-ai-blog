<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->whereIn('name', ['moderator', 'editor'])->delete();
        
        DB::table('role_user')
            ->whereIn('role_id', function ($query) {
                $query->select('id')->from('roles')->whereIn('name', ['moderator', 'editor']);
            })
            ->delete();
        
        DB::table('permission_role')
            ->whereIn('role_id', function ($query) {
                $query->select('id')->from('roles')->whereIn('name', ['moderator', 'editor']);
            })
            ->delete();
    }

    public function down(): void
    {
    }
};
