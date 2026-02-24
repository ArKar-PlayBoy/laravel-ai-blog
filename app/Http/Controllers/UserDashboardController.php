<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $stats = [
            'posts_count' => $user->posts()->count(),
            'published_posts' => $user->posts()->where('status', 'published')->count(),
            'draft_posts' => $user->posts()->where('status', 'draft')->count(),
            'comments_count' => $user->comments()->count(),
            'likes_received' => Post::where('user_id', $user->id)->withCount('likedBy')->get()->sum('liked_by_count'),
            'likes_given' => $user->likedPosts()->count(),
        ];

        $activeTab = $request->get('tab', 'posts');

        $posts = $user->posts()
            ->with('category')
            ->withCount(['likedBy', 'comments'])
            ->latest()
            ->paginate(5, ['*'], 'posts_page');

        $comments = $user->comments()
            ->with('post')
            ->latest()
            ->paginate(5, ['*'], 'comments_page');

        $likedPosts = $user->likedPosts()
            ->with('user', 'category')
            ->withCount(['likedBy', 'comments'])
            ->latest('post_likes.created_at')
            ->paginate(5, ['*'], 'likes_page');

        return view('dashboard', compact('stats', 'posts', 'comments', 'likedPosts', 'activeTab'));
    }
}
