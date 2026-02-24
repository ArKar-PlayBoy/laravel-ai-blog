@extends('layouts.blog')

@section('title', 'Blog')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Blog</h1>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Browse our latest posts</p>
</div>

@if ($categories->isNotEmpty())
    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('posts.index') }}"
           class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('category') ? 'bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            All
        </a>
        @foreach ($categories as $category)
            <a href="{{ route('posts.index', ['category' => $category->id]) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('category') == $category->id ? 'bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                {{ $category->name }}
            </a>
        @endforeach
    </div>
@endif

@if ($posts->isEmpty())
    <div class="rounded-lg bg-white dark:bg-gray-800 p-12 text-center shadow-sm">
        <p class="text-gray-500 dark:text-gray-400">No posts yet.</p>
        @auth
            <a href="{{ route('posts.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 rounded-md font-medium hover:bg-gray-700 dark:hover:bg-gray-300">Create your first post</a>
        @endauth
    </div>
@else
    <div class="grid w-full grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($posts as $post)
            <article class="group min-w-0 rounded-xl bg-white dark:bg-gray-800 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <a href="{{ route('posts.show', $post) }}" class="block">
                    <img src="{{ $post->featured_image }}" alt="" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $post->category->name }}</span>
                        <h2 class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-gray-600 dark:group-hover:text-gray-300 line-clamp-2">{{ $post->title }}</h2>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ Str::limit(strip_tags($post->body), 100) }}</p>
                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">By {{ $post->user->name }} · {{ $post->created_at->diffForHumans() }}</p>
                    </div>
                </a>
                @auth
                    <div class="px-4 pb-3">
                        <form method="POST" action="{{ route('posts.like', $post) }}" class="inline">
                            @csrf
                            @php $isLiked = $post->is_liked_by_auth_user ?? false; @endphp
                            <button type="submit" class="flex items-center gap-1 text-sm {{ $isLiked ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }}">
                                <svg class="w-4 h-4" fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span>{{ $post->liked_by_count ?? 0 }}</span>
                            </button>
                        </form>
                        <div class="mt-1 flex items-center gap-1 text-sm text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span>{{ $post->comments_count ?? 0 }}</span>
                        </div>
                    </div>
                @else
                    <div class="px-4 pb-3 flex items-center gap-1 text-sm text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span>{{ $post->liked_by_count ?? 0 }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>{{ $post->comments_count ?? 0 }}</span>
                    </div>
                @endauth
            </article>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $posts->withQueryString()->links() }}
    </div>
@endif
@endsection
