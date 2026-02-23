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
    /**
     * Display a listing of posts.
     */
    public function index(Request $request): View
    {
        $query = Post::with(['user', 'category'])
            ->withCount(['likedBy', 'comments'])
            ->latest();

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $posts = $query->paginate(12);
        $categories = Category::orderBy('name')->get();

        $likedPostIds = auth()->check()
            ? auth()->user()->likedPosts()->whereIn('post_id', $posts->pluck('id'))->pluck('post_id')->toArray()
            : [];

        return view('posts.index', compact('posts', 'categories', 'likedPostIds'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create(): View|RedirectResponse
    {
        $this->authorize('create', Post::class);
        $categories = Category::orderBy('name')->get();

        return view('posts.create', compact('categories'));
    }

    /**
     * Store a newly created post.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        Post::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('posts.index')->with('status', 'Post created successfully.');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): View
    {
        $post->load(['user', 'category', 'comments.user'])
            ->loadCount('likedBy');

        $post->isLikedByAuthUser = auth()->check()
            ? $post->likedBy()->where('user_id', auth()->id())->exists()
            : false;

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the post.
     */
    public function edit(Post $post): View|RedirectResponse
    {
        $this->authorize('update', $post);
        $categories = Category::orderBy('name')->get();

        return view('posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified post.
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $post->update($request->validated());

        return redirect()->route('posts.show', $post)->with('status', 'Post updated successfully.');
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);
        $post->delete();

        return redirect()->route('posts.index')->with('status', 'Post deleted successfully.');
    }
}
