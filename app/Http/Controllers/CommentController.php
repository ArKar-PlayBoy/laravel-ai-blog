<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    /**
     * Store a newly created comment.
     */
    public function store(StoreCommentRequest $request): RedirectResponse
    {
        $post = Post::findOrFail($request->post_id);

        Comment::create([
            'content' => $request->validated()['content'],
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
        ]);

        return redirect()->route('posts.show', $post)
            ->with('status', 'Comment added successfully.');
    }

    /**
     * Update the specified comment.
     */
    public function update(UpdateCommentRequest $request, Comment $comment): RedirectResponse
    {
        $this->authorize('update', $comment);
        
        $comment->update($request->validated());

        return redirect()->route('posts.show', $comment->post)
            ->with('status', 'Comment updated successfully.');
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);
        $post = $comment->post;
        $comment->delete();

        return redirect()->route('posts.show', $post)
            ->with('status', 'Comment deleted successfully.');
    }
}
