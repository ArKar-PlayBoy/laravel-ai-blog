@extends('admin.layouts.main')

@section('title', 'Post Details')
@section('header', 'Post Details')

@section('content')
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h2 class="text-2xl font-bold">{{ $post->title }}</h2>
            <p class="text-gray-500">by {{ $post->user->name }} in {{ $post->category->name }}</p>
        </div>
        <div>
            @if($post->status == 'published')
                <span class="px-3 py-1 rounded-full bg-green-100 text-green-800">Published</span>
            @elseif($post->status == 'pending')
                <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800">Pending</span>
            @elseif($post->status == 'draft')
                <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-800">Draft</span>
            @else
                <span class="px-3 py-1 rounded-full bg-red-100 text-red-800">Archived</span>
            @endif
        </div>
    </div>
    
    @if($post->featured_image)
        <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-64 object-cover rounded mb-4">
    @endif
    
    <div class="prose max-w-none">
        {!! nl2br(e($post->body)) !!}
    </div>
    
    <div class="mt-6 flex gap-2">
        <a href="{{ route('admin.posts.edit', $post) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Edit</a>
        @if($post->status === 'archived')
            <form method="POST" action="{{ route('admin.posts.approve', $post) }}">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Restore</button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.posts.archive', $post) }}">
                @csrf
                <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">Archive</button>
            </form>
        @endif
        <form method="POST" action="{{ route('admin.posts.destroy', $post) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Delete</button>
        </form>
        <a href="{{ route('posts.show', $post) }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">View on Site</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold">{{ $post->comments_count }}</p>
        <p class="text-gray-500">Comments</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold">{{ $post->liked_by_count }}</p>
        <p class="text-gray-500">Likes</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-sm font-bold">{{ $post->created_at->format('M d, Y') }}</p>
        <p class="text-gray-500">Published</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">Comments</h3>
    @forelse($post->comments as $comment)
        <div class="border-b pb-3 mb-3">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-medium">{{ $comment->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                </div>
            </div>
            <p class="mt-2">{{ $comment->body }}</p>
        </div>
    @empty
        <p class="text-gray-500">No comments yet.</p>
    @endforelse
</div>
@endsection
