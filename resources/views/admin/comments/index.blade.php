@extends('admin.layouts.main')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Manage Comments</h1>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Review and manage flagged comments</p>
</div>

<div class="mb-6 flex gap-2">
    <a href="{{ route('admin.comments.index') }}"
       class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('status') ? 'bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        All
    </a>
    <a href="{{ route('admin.comments.index', ['status' => 'flagged']) }}"
       class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'flagged' ? 'bg-yellow-500 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        Flagged
    </a>
    <a href="{{ route('admin.comments.index', ['status' => 'published']) }}"
       class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'published' ? 'bg-green-500 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        Published
    </a>
    <a href="{{ route('admin.comments.index', ['status' => 'archived']) }}"
       class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'archived' ? 'bg-gray-500 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        Archived
    </a>
</div>

@if ($comments->isEmpty())
    <div class="rounded-lg bg-white dark:bg-gray-800 p-12 text-center shadow-sm">
        <p class="text-gray-500 dark:text-gray-400">No comments found.</p>
    </div>
@else
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Comment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Post</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Flag Reason</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($comments as $comment)
                <tr>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ Str::limit($comment->content, 100) }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $comment->user->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        <a href="{{ route('posts.show', $comment->post) }}" class="hover:text-blue-500">
                            {{ Str::limit($comment->post->title, 30) }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($comment->status == 'flagged')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Flagged
                            </span>
                        @elseif($comment->status == 'published')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Published
                            </span>
                        @elseif($comment->status == 'archived')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Archived
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($comment->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        @if($comment->flag_reason)
                            <span class="capitalize">{{ $comment->flag_reason }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('posts.show', $comment->post) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 mr-3">View</a>
                        @if($comment->status === 'archived')
                            <form method="POST" action="{{ route('admin.comments.restore', $comment) }}" class="inline" onsubmit="return confirm('Restore this comment?');">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400 mr-3">Restore</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.comments.archive', $comment) }}" class="inline" onsubmit="return confirm('Archive this comment? It will be hidden from public view.');">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 mr-3">Archive</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" class="inline" onsubmit="return confirm('Delete this comment permanently?');">
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
        {{ $comments->withQueryString()->links() }}
    </div>
@endif
@endsection
