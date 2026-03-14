<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine whether the user can create comments.
     */
    public function create(User $user): bool
    {
        return !$user->is_banned;
    }

    /**
     * Determine whether the user can update the comment.
     */
    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    /**
     * Determine whether the user can delete the comment.
     */
    public function delete(User $user, Comment $comment): bool
    {
        if ($user->id === $comment->user_id) {
            return true;
        }
        
        if (!$comment->relationLoaded('post')) {
            $comment->load('post');
        }
        
        if ($user->id === $comment->post->user_id) {
            return true;
        }
        
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }
        
        return false;
    }
}
