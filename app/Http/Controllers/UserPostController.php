<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserPostController extends Controller
{
    public function index(Request $request): View
    {
        $query = Post::with(['category'])
            ->where('user_id', auth()->id())
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $posts = $query->paginate(15);

        return view('posts.my-posts', compact('posts'));
    }
}
