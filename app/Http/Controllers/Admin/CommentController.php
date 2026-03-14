<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::with(['user', 'post']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $comments = $query->latest()->paginate(20);

        return view('admin.comments.index', compact('comments'));
    }

    public function destroy(Request $request, Comment $comment)
    {
        $currentUser = auth()->user();

        if (!$currentUser->hasAnyRole(['super_admin', 'admin'])) {
            return back()->with('error', 'You do not have permission to delete comments.');
        }

        $oldValues = $comment->toArray();
        $comment->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_comment',
            'resource_type' => 'Comment',
            'resource_id' => $comment->id,
            'old_values' => $oldValues,
            'new_values' => null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return back()->with('success', 'Comment deleted successfully.');
    }

    public function restore(Request $request, Comment $comment)
    {
        $currentUser = auth()->user();

        if (!$currentUser->hasAnyRole(['super_admin', 'admin'])) {
            return back()->with('error', 'You do not have permission to restore comments.');
        }

        $oldValues = $comment->toArray();

        $comment->update([
            'status' => 'published',
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'restore_comment',
            'resource_type' => 'Comment',
            'resource_id' => $comment->id,
            'old_values' => $oldValues,
            'new_values' => $comment->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return back()->with('success', 'Comment restored successfully.');
    }

    public function archive(Request $request, Comment $comment)
    {
        $currentUser = auth()->user();

        if (!$currentUser->hasAnyRole(['super_admin', 'admin'])) {
            return back()->with('error', 'You do not have permission to archive comments.');
        }

        $oldValues = $comment->toArray();

        // Find all other identical flagged comments and archive them together
        Comment::where('content', $comment->content)
            ->where('post_id', $comment->post_id)
            ->where('status', 'flagged')
            ->where('id', '!=', $comment->id)
            ->update(['status' => 'archived']);

        $comment->update([
            'status' => 'archived',
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'archive_comment',
            'resource_type' => 'Comment',
            'resource_id' => $comment->id,
            'old_values' => $oldValues,
            'new_values' => $comment->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return back()->with('success', 'Comment(s) archived successfully.');
    }
}
