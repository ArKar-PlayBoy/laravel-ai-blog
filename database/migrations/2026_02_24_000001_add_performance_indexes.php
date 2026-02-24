<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Posts table indexes
        Schema::table('posts', function (Blueprint $table) {
            $table->index('status', 'idx_posts_status');
            $table->index(['user_id', 'status'], 'idx_posts_user_status');
            $table->index(['category_id', 'status'], 'idx_posts_category_status');
            $table->index(['user_id', 'created_at'], 'idx_posts_user_created');
        });

        // Post likes table indexes
        Schema::table('post_likes', function (Blueprint $table) {
            $table->unique(['user_id', 'post_id'], 'uq_post_likes_user_post');
            $table->index('post_id', 'idx_post_likes_post');
        });

        // Comments table indexes
        Schema::table('comments', function (Blueprint $table) {
            $table->index('post_id', 'idx_comments_post');
            $table->index(['user_id', 'created_at'], 'idx_comments_user_created');
        });

        // Users table index for banned status
        Schema::table('users', function (Blueprint $table) {
            $table->index('is_banned', 'idx_users_banned');
        });

        // Audit logs indexes for faster queries
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'idx_audit_user_created');
            $table->index(['resource_type', 'resource_id'], 'idx_audit_resource');
            $table->index('action', 'idx_audit_action');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('idx_posts_status');
            $table->dropIndex('idx_posts_user_status');
            $table->dropIndex('idx_posts_category_status');
            $table->dropIndex('idx_posts_user_created');
        });

        Schema::table('post_likes', function (Blueprint $table) {
            $table->dropUnique('uq_post_likes_user_post');
            $table->dropIndex('idx_post_likes_post');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('idx_comments_post');
            $table->dropIndex('idx_comments_user_created');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_banned');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('idx_audit_user_created');
            $table->dropIndex('idx_audit_resource');
            $table->dropIndex('idx_audit_action');
        });
    }
};
