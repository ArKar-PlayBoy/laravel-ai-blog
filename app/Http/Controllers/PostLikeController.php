<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;

class PostLikeController extends Controller
{
    /**
     * Toggle like on a post.
     */
    public function toggle(Post $post): RedirectResponse
    {
        $user = request()->user();
        if (! $user) {
            return redirect()->route('login');
        }

        if ($post->likedBy()->where('user_id', $user->id)->exists()) {
            $post->likedBy()->detach($user->id);
        } else {
            $post->likedBy()->attach($user->id);
        }

        return back();
    }
}
