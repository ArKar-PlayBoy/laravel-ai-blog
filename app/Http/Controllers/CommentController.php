<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

    public function flag(Request $request, Comment $comment): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'in:spam,offensive,harassment,other'],
        ]);

        if ($comment->status === 'flagged') {
            return back()->with('error', 'This comment is already flagged.');
        }

        if ($comment->user_id === auth()->id()) {
            return back()->with('error', 'You cannot flag your own comment.');
        }

        if (!\App\Models\FlagLimit::canFlag(auth()->id(), 10)) {
            return back()->with('error', 'You have reached your daily flag limit (10). Please try again tomorrow.');
        }

        $comment->update([
            'status' => 'flagged',
            'flagged_by' => auth()->id(),
            'flagged_at' => now(),
            'flag_reason' => $request->reason,
        ]);

        \App\Models\FlagLimit::incrementFlag(auth()->id());

        // Notify admins
        $admins = \App\Models\User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'super_admin']);
        })->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\CommentFlagged($comment));

        return back()->with('success', 'Comment has been flagged for review.');
    }
}
