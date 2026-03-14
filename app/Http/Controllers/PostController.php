<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $userId = auth()->id();
        
        $query = Post::with(['user', 'category'])
            ->withCount(['likedBy', 'comments'])
            ->when($userId, function ($q) use ($userId) {
                $q->withExists(['likedBy as is_liked_by_auth_user' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }]);
            })
            ->published()
            ->latest();

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $posts = $query->paginate(12);
        $categories = Category::getCached();

        return view('posts.index', compact('posts', 'categories'));
    }

    public function create(): View|RedirectResponse
    {
        $this->authorize('create', Post::class);
        $categories = Category::getCached();

        return view('posts.create', compact('categories'));
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        Post::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'status' => 'published',
        ]);

        return redirect()->route('posts.index')->with('status', 'Post published successfully.');
    }

    public function show(Post $post): View
    {
        if ($post->status !== 'published' && (!auth()->check() || auth()->id() !== $post->user_id)) {
            abort(404);
        }

        $userId = auth()->id();
        
        $commentsQuery = $post->comments()->with('user');
        
        if (!auth()->check() || (!auth()->user()->isAdmin() && auth()->id() !== $post->user_id)) {
            $commentsQuery->where('status', '!=', 'archived');
        }
        
        $post->load(['user', 'category'])
            ->loadCount('likedBy');
        
        $post->comments = $commentsQuery->get();

        if ($userId) {
            $post->is_liked_by_auth_user = $post->likedBy()->where('user_id', $userId)->exists();
        } else {
            $post->is_liked_by_auth_user = false;
        }

        return view('posts.show', compact('post'));
    }

    public function edit(Post $post): View|RedirectResponse
    {
        $this->authorize('update', $post);
        $categories = Category::getCached();

        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $post->update($request->validated());

        return redirect()->route('posts.show', $post)->with('status', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);
        $post->delete();

        return redirect()->route('posts.index')->with('status', 'Post deleted successfully.');
    }
}
