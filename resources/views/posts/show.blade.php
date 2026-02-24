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
                <div class="rounded-lg bg-gray-50 dark:bg-gray-800/50 p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $comment->user->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</p>
                        </div>
                        @auth
                            @if (auth()->user()->can('update', $comment))
                                <div class="flex gap-3">
                                    <button type="button" onclick="document.getElementById('edit-comment-{{ $comment->id }}').classList.toggle('hidden')"
                                        class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Edit</button>
                                    <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="inline" onsubmit="return confirm('Delete this comment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-500 hover:text-red-700 dark:hover:text-red-400">Delete</button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>
                    <div id="comment-content-{{ $comment->id }}">
                        <p class="mt-2 text-gray-700 dark:text-gray-300">{!! nl2br(e($comment->content)) !!}</p>
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
