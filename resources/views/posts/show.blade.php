@extends('layouts.blog')

@section('title', $post->title)

@section('content')
<article class="max-w-4xl mx-auto">
    <a href="{{ route('posts.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 mb-4 inline-block">← Back to posts</a>

    <header>
        <img src="{{ e($post->featured_image) }}" alt="" class="w-full h-64 md:h-80 object-cover rounded-xl">
        <span class="mt-4 inline-block text-sm font-medium text-gray-500 dark:text-gray-400">{{ $post->category->name }}</span>
        <h1 class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $post->title }}</h1>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">By {{ $post->user->name }} · {{ $post->created_at->format('M j, Y') }}</p>
        @auth
            <form method="POST" action="{{ route('posts.like', $post) }}" class="mt-3 inline-block">
                @csrf
                <button type="submit" class="flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-medium transition
                    {{ $post->is_liked_by_auth_user ?? false ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'bg-gray-100 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-500' }}">
                    <svg class="w-5 h-5" fill="{{ ($post->is_liked_by_auth_user ?? false) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span>{{ $post->liked_by_count ?? 0 }}</span>
                </button>
            </form>
            @if (auth()->user()->can('update', $post))
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('posts.edit', $post) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">Edit</a>
                    <form method="POST" action="{{ route('posts.destroy', $post) }}" class="inline" onsubmit="return confirm('Delete this post?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200">Delete</button>
                    </form>
                </div>
            @endif
        @endauth
    </header>

    <div class="mt-6 max-w-none text-gray-900 dark:text-gray-100">
        {!! nl2br(e($post->body)) !!}
    </div>

    <section class="mt-12 border-t border-gray-200 dark:border-gray-700 pt-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $post->comments->count() }} {{ Str::plural('comment', $post->comments->count()) }}</h2>

        @auth
            <form method="POST" action="{{ route('comments.store') }}" class="mt-4">
                @csrf
                <input type="hidden" name="post_id" value="{{ $post->id }}">
                <textarea name="content" rows="3" required
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Write a comment...">{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <button type="submit" class="mt-2 px-4 py-2 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 rounded-md font-medium hover:bg-gray-700 dark:hover:bg-gray-300">Post comment</button>
            </form>
        @else
            <p class="mt-4 text-gray-500 dark:text-gray-400"><a href="{{ route('login') }}" class="underline">Log in</a> to leave a comment.</p>
        @endauth

        <div class="mt-6 space-y-4">
            @foreach ($post->comments as $comment)
                @php
                    $isAdmin    = auth()->check() && auth()->user()->hasAnyRole(['super_admin', 'admin']);
                    $isArchived = $comment->status === 'archived';
                    $isFlagged  = $comment->status === 'flagged';
                @endphp

                @if ($isArchived && !$isAdmin)
                    @continue
                @endif

                <div class="rounded-lg {{ $isArchived ? 'bg-gray-100 dark:bg-gray-900/50 border border-dashed border-gray-300 dark:border-gray-600 opacity-60' : ($isFlagged ? 'bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-700/40' : 'bg-gray-50 dark:bg-gray-800/50') }} p-4">
                    {{-- Admin-only status action bar --}}
                    @if ($isAdmin && $isArchived)
                        <div class="mb-2 flex items-center gap-2">
                            <span class="text-xs font-semibold text-gray-500 bg-gray-200 dark:bg-gray-700 px-2 py-0.5 rounded">Archived</span>
                            <form method="POST" action="{{ route('admin.comments.restore', $comment) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-blue-600 hover:underline">Restore</button>
                            </form>
                            <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" class="inline" onsubmit="return confirm('Permanently delete this comment?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:underline">Delete</button>
                            </form>
                        </div>
                    @elseif ($isAdmin && $isFlagged)
                        <div class="mb-2 flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-semibold text-yellow-700 bg-yellow-100 dark:bg-yellow-800/40 px-2 py-0.5 rounded">⚑ Flagged — {{ $comment->flag_reason ?? 'no reason' }}</span>
                            <form method="POST" action="{{ route('admin.comments.archive', $comment) }}" class="inline" onsubmit="return confirm('Archive this comment?');">
                                @csrf
                                <button type="submit" class="text-xs text-red-600 hover:underline">Archive</button>
                            </form>
                            <form method="POST" action="{{ route('admin.comments.restore', $comment) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-green-600 hover:underline">Clear Flag</button>
                            </form>
                        </div>
                    @endif

                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $comment->user->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</p>
                        </div>
                        @auth
                            <div class="flex items-center gap-3">
                                @if (auth()->user()->can('update', $comment) && !$isArchived)
                                    <button type="button" onclick="document.getElementById('edit-comment-{{ $comment->id }}').classList.toggle('hidden')"
                                        class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Edit</button>
                                @endif
                                @if (auth()->user()->can('delete', $comment))
                                    <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="inline" onsubmit="return confirm('Delete this comment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-500 hover:text-red-700 dark:hover:text-red-400">Delete</button>
                                    </form>
                                @endif
                                {{-- Only allow flagging published comments that you didn't write --}}
                                @if ($comment->status === 'published' && $comment->user_id !== auth()->id())
                                    <button type="button" onclick="document.getElementById('flag-form-{{ $comment->id }}').classList.toggle('hidden')" class="text-sm text-yellow-600 hover:text-yellow-800">Flag</button>
                                    <form method="POST" action="{{ route('comments.flag', $comment) }}" id="flag-form-{{ $comment->id }}" class="hidden inline-flex items-center gap-2 mt-1" onsubmit="return confirm('Flag this comment?');">
                                        @csrf
                                        <select name="reason" required class="text-xs border rounded px-1 py-1">
                                            <option value="">Select reason</option>
                                            <option value="spam">Spam</option>
                                            <option value="offensive">Offensive</option>
                                            <option value="harassment">Harassment</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <button type="submit" class="text-sm text-yellow-600 hover:text-yellow-800">Submit</button>
                                    </form>
                                @endif
                            </div>
                        @endauth
                    </div>
                    <div id="comment-content-{{ $comment->id }}" class="{{ $isArchived ? 'opacity-60' : '' }}">
                        <p class="mt-2 text-gray-700 dark:text-gray-300">{!! nl2br(e($comment->content)) !!}</p>
                        {{-- Public sees a simple "flagged" badge; admin sees reason + actions above --}}
                        @if ($isFlagged && !$isAdmin)
                            <span class="mt-2 inline-block text-xs text-yellow-700 bg-yellow-100 px-2 py-0.5 rounded">⚑ Flagged for review</span>
                        @endif
                    </div>
                    <div id="edit-comment-{{ $comment->id }}" class="hidden mt-2">
                        <form method="POST" action="{{ route('comments.update', $comment) }}">
                            @csrf
                            @method('PATCH')
                            <textarea name="content" rows="2" required class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">{{ old('content', $comment->content) }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <div class="mt-2 flex gap-2">
                                <button type="submit" class="text-sm px-3 py-1 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 rounded">Save</button>
                                <button type="button" onclick="document.getElementById('edit-comment-{{ $comment->id }}').classList.add('hidden')" class="text-sm text-gray-500">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</article>
@endsection
