<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['posts_count'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Total Posts</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['published_posts'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Published</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['draft_posts'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Drafts</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['comments_count'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Comments</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['likes_received'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Likes Received</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-2xl font-bold text-pink-600 dark:text-pink-400">{{ $stats['likes_given'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Likes Given</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex -mb-px">
                        <a href="{{ route('dashboard', ['tab' => 'posts']) }}" class="px-6 py-3 text-sm font-medium {{ $activeTab === 'posts' ? 'border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            My Posts
                        </a>
                        <a href="{{ route('dashboard', ['tab' => 'comments']) }}" class="px-6 py-3 text-sm font-medium {{ $activeTab === 'comments' ? 'border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            My Comments
                        </a>
                        <a href="{{ route('dashboard', ['tab' => 'likes']) }}" class="px-6 py-3 text-sm font-medium {{ $activeTab === 'likes' ? 'border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            Liked Posts
                        </a>
                    </nav>
                </div>

                <div class="p-6">
                    @if($activeTab === 'posts')
                        @if($posts->isEmpty())
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">You haven't written any posts yet.</p>
                                <a href="{{ route('posts.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md font-medium hover:bg-indigo-700">Create your first post</a>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($posts as $post)
                                    <div class="flex items-start justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('posts.show', $post) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400">{{ $post->title }}</a>
                                                <span class="px-2 py-0.5 text-xs rounded {{ $post->status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">{{ $post->status }}</span>
                                            </div>
                                            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                {{ $post->category->name ?? 'Uncategorized' }} · {{ $post->created_at->diffForHumans() }}
                                            </div>
                                            <div class="mt-2 flex items-center gap-4 text-xs text-gray-400">
                                                <span>{{ $post->liked_by_count ?? 0 }} likes</span>
                                                <span>{{ $post->comments_count ?? 0 }} comments</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('posts.edit', $post) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Edit</a>
                                            <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Are you sure?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">{{ $posts->withQueryString()->links() }}</div>
                        @endif

                    @elseif($activeTab === 'comments')
                        @if($comments->isEmpty())
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">You haven't commented on any posts yet.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($comments as $comment)
                                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                        <p class="text-gray-900 dark:text-gray-100">{{ Str::limit($comment->body, 200) }}</p>
                                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            On: <a href="{{ route('posts.show', $comment->post) }}#comment-{{ $comment->id }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ $comment->post->title }}</a> · {{ $comment->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">{{ $comments->withQueryString()->links() }}</div>
                        @endif

                    @elseif($activeTab === 'likes')
                        @if($likedPosts->isEmpty())
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">You haven't liked any posts yet.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($likedPosts as $post)
                                    <div class="flex items-start justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                        <div class="flex-1">
                                            <a href="{{ route('posts.show', $post) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400">{{ $post->title }}</a>
                                            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                By {{ $post->user->name }} · {{ $post->category->name ?? 'Uncategorized' }}
                                            </div>
                                            <div class="mt-2 flex items-center gap-4 text-xs text-gray-400">
                                                <span>{{ $post->liked_by_count ?? 0 }} likes</span>
                                                <span>{{ $post->comments_count ?? 0 }} comments</span>
                                            </div>
                                        </div>
                                        <form method="POST" action="{{ route('posts.like', $post) }}">
                                            @csrf
                                            <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">Unlike</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">{{ $likedPosts->withQueryString()->links() }}</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
