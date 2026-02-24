@extends('layouts.blog')

@section('title', 'My Posts')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">My Posts</h1>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage your submitted posts</p>
</div>

<div class="mb-6 flex gap-2">
    <a href="{{ route('posts.my-posts') }}"
       class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('status') ? 'bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        All
    </a>
    <a href="{{ route('posts.my-posts', ['status' => 'pending']) }}"
       class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'pending' ? 'bg-yellow-500 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        Pending
    </a>
    <a href="{{ route('posts.my-posts', ['status' => 'published']) }}"
       class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'published' ? 'bg-green-500 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        Published
    </a>
    <a href="{{ route('posts.my-posts', ['status' => 'archived']) }}"
       class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'archived' ? 'bg-gray-500 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        Archived
    </a>
</div>

@if ($posts->isEmpty())
    <div class="rounded-lg bg-white dark:bg-gray-800 p-12 text-center shadow-sm">
        <p class="text-gray-500 dark:text-gray-400">You haven't created any posts yet.</p>
        <a href="{{ route('posts.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 rounded-md font-medium hover:bg-gray-700 dark:hover:bg-gray-300">
            Create your first post
        </a>
    </div>
@else
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($posts as $post)
                <tr>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $post->title }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $post->category->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($post->status == 'pending')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending Approval
                            </span>
                        @elseif($post->status == 'published')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Published
                            </span>
                        @elseif($post->status == 'archived')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Archived
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Draft
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $post->created_at->diffForHumans() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('posts.show', $post) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">View</a>
                        <a href="{{ route('posts.edit', $post) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Edit</a>
                        <form method="POST" action="{{ route('posts.destroy', $post) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this post?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $posts->withQueryString()->links() }}
    </div>
@endif
@endsection
