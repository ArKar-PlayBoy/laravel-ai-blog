<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = DB::table('posts')
            ->selectRaw("
                (SELECT COUNT(*) FROM users) as total_users,
                (SELECT COUNT(*) FROM users WHERE is_banned = 0) as active_users,
                (SELECT COUNT(*) FROM users WHERE is_banned = 1) as banned_users,
                (SELECT COUNT(*) FROM posts) as total_posts,
                (SELECT COUNT(*) FROM posts WHERE status = 'published') as published_posts,
                (SELECT COUNT(*) FROM posts WHERE status = 'draft') as draft_posts,
                (SELECT COUNT(*) FROM posts WHERE status = 'archived') as archived_posts,
                (SELECT COUNT(*) FROM comments) as total_comments,
                (SELECT COUNT(*) FROM post_likes) as total_likes,
                (SELECT COUNT(*) FROM categories) as total_categories
            ")
            ->first();

        $recentUsers = User::with('roles')->latest()->take(5)->get();
        $recentPosts = Post::with(['user', 'category'])->latest()->take(5)->get();
        $recentComments = Comment::with(['user', 'post'])->latest()->take(5)->get();
        $recentActivity = AuditLog::with('user')->latest()->take(10)->get();

        $postsPerMonth = Post::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard.index', compact('stats', 'recentUsers', 'recentPosts', 'recentComments', 'recentActivity', 'postsPerMonth'));
    }

    public function settings()
    {
        $currentUser = auth()->user();
        $currentUser->load('roles.permissions');
        
        if (! $currentUser->hasRole('super_admin')) {
            return redirect()->route('admin.dashboard')->with('error', 'Only super admin can access settings.');
        }

        return view('admin.settings');
    }
}
