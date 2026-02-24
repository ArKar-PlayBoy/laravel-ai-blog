<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'category']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $posts = $query->latest()->paginate(20);
        $categories = Category::getCached();

        return view('admin.posts.index', compact('posts', 'categories'));
    }

    public function show(Post $post)
    {
        $post->load(['user', 'category', 'comments.user'])
            ->loadCount(['likedBy', 'comments']);

        return view('admin.posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $categories = Category::getCached();

        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'featured_image' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);

        $oldValues = $post->toArray();

        $post->update($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'resource_type' => 'Post',
            'resource_id' => $post->id,
            'old_values' => $oldValues,
            'new_values' => $post->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post updated successfully.');
    }

    public function approve(Request $request, Post $post)
    {
        $currentUser = auth()->user();
        $currentUser->load('roles.permissions');
        
        if (! $currentUser->hasPermission('approve_posts')) {
            return back()->with('error', 'You do not have permission to approve posts.');
        }

        $oldValues = $post->toArray();

        $post->update(['status' => 'published']);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'approve',
            'resource_type' => 'Post',
            'resource_id' => $post->id,
            'old_values' => $oldValues,
            'new_values' => $post->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return back()->with('success', 'Post has been approved.');
    }

    public function archive(Request $request, Post $post)
    {
        $currentUser = auth()->user();
        $currentUser->load('roles.permissions');
        
        if (! $currentUser->hasPermission('approve_posts')) {
            return back()->with('error', 'You do not have permission to archive posts.');
        }

        $oldValues = $post->toArray();

        $post->update(['status' => 'archived']);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'archive',
            'resource_type' => 'Post',
            'resource_id' => $post->id,
            'old_values' => $oldValues,
            'new_values' => $post->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return back()->with('success', 'Post has been archived.');
    }

    public function destroy(Request $request, Post $post)
    {
        $currentUser = auth()->user();
        $currentUser->load('roles.permissions');
        
        if (! $currentUser->hasPermission('manage_posts')) {
            return back()->with('error', 'You do not have permission to delete posts.');
        }

        $oldValues = $post->toArray();

        $post->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete',
            'resource_type' => 'Post',
            'resource_id' => $post->id,
            'old_values' => $oldValues,
            'new_values' => null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post deleted successfully.');
    }

    public function bulkAction(Request $request)
    {
        $currentUser = auth()->user();
        $currentUser->load('roles.permissions');

        $request->validate([
            'action' => ['required', 'in:approve,archive,delete'],
            'post_ids' => ['required', 'array'],
            'post_ids.*' => ['exists:posts,id'],
        ]);

        if ($request->action === 'delete' && ! $currentUser->hasPermission('manage_posts')) {
            return back()->with('error', 'You do not have permission to delete posts.');
        }

        if (in_array($request->action, ['approve', 'archive']) && ! $currentUser->hasPermission('approve_posts')) {
            return back()->with('error', 'You do not have permission to perform this action.');
        }

        switch ($request->action) {
            case 'approve':
                Post::whereIn('id', $request->post_ids)->update(['status' => 'published']);
                $message = 'Selected posts have been approved.';
                break;
            case 'archive':
                Post::whereIn('id', $request->post_ids)->update(['status' => 'archived']);
                $message = 'Selected posts have been archived.';
                break;
            case 'delete':
                Post::whereIn('id', $request->post_ids)->delete();
                $message = 'Selected posts have been deleted.';
                break;
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'bulk_'.$request->action,
            'resource_type' => 'Post',
            'resource_id' => 0,
            'old_values' => null,
            'new_values' => ['post_ids' => $request->post_ids],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return back()->with('success', $message);
    }
}
