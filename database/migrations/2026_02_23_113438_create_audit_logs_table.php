<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->comment('User who performed the action');
            $table->string('action')->comment('Action performed (create, update, delete, etc.)');
            $table->string('resource_type')->comment('Type of resource (User, Post, Category, etc.)');
            $table->bigInteger('resource_id')->comment('ID of the resource affected');
            $table->text('old_values')->comment('JSON of old values before change')->nullable();
            $table->text('new_values')->comment('JSON of new values after change')->nullable();
            $table->text('ip_address')->comment('IP address of the user')->nullable();
            $table->text('user_agent')->comment('User agent/browser info')->nullable();
            $table->string('url')->comment('URL where action was performed')->nullable();
            $table->text('metadata')->comment('Additional metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'action', 'resource_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
