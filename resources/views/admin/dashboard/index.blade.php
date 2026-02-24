@extends('admin.layouts.main')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Posts</p>
                    <p class="text-3xl font-bold">{{ $stats->total_posts }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-file-alt text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Published</p>
                    <p class="text-3xl font-bold">{{ $stats->published_posts }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">Comments</p>
                    <p class="text-3xl font-bold">{{ $stats->total_comments }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-comments text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Likes</p>
                    <p class="text-3xl font-bold">{{ $stats->total_likes }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-heart text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Posts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Recent Posts</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentPosts as $post)
                <div class="p-4 flex items-center gap-4 hover:bg-gray-50">
                    <img src="{{ $post->featured_image ?? 'https://via.placeholder.com/50' }}" alt="" class="w-12 h-12 rounded-lg object-cover">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 truncate">{{ $post->title }}</p>
                        <p class="text-sm text-gray-500">by {{ $post->user->name }}</p>
                    </div>
                    <span class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <div class="p-4 text-center text-gray-500">No posts yet</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Comments -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Recent Comments</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentComments as $comment)
                <div class="p-4">
                    <p class="text-gray-700 text-sm">{{ Str::limit($comment->body, 80) }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $comment->user->name }} on {{ $comment->post->title }}
                    </p>
                </div>
                @empty
                <div class="p-4 text-center text-gray-500">No comments yet</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Users -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Users</h3>
                <span class="bg-blue-700 text-xs px-2 py-1 rounded-full-100 text-blue">{{ $stats->total_users }}</span>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Active</span>
                        <span class="font-medium">{{ $stats->active_users }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Banned</span>
                        <span class="font-medium text-red-500">{{ $stats->banned_users }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Posts Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Posts Status</h3>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Published</span>
                        <span class="font-medium text-green-600">{{ $stats->published_posts }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Draft</span>
                        <span class="font-medium text-yellow-600">{{ $stats->draft_posts }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Archived</span>
                        <span class="font-medium text-gray-600">{{ $stats->archived_posts }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Recent Activity</h3>
            </div>
            <div class="p-4 max-h-48 overflow-y-auto">
                <div class="space-y-3">
                    @forelse($recentActivity as $log)
                    <div class="text-sm">
                        <p class="text-gray-700">
                            <span class="font-medium">{{ $log->user->name }}</span>
                            <span class="text-gray-500">{{ $log->action }}</span>
                            <span class="font-medium">{{ $log->resource_type }}</span>
                        </p>
                        <p class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">No activity yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
