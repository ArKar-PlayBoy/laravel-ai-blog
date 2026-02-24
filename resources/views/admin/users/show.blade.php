@extends('admin.layouts.main')

@section('title', 'User Details')
@section('header', 'User Details')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">User Information</h3>
        <div class="space-y-3">
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Status:</strong> 
                @if($user->is_banned)
                    <span class="text-red-600">Banned</span>
                @else
                    <span class="text-green-600">Active</span>
                @endif
            </p>
            <p><strong>Joined:</strong> {{ $user->created_at->format('M d, Y') }}</p>
            @if($user->is_banned && $user->banned_at)
                <p><strong>Banned At:</strong> {{ $user->banned_at->format('M d, Y') }}</p>
                <p><strong>Ban Reason:</strong> {{ $user->banned_reason }}</p>
            @endif
        </div>
        
        <div class="mt-4 flex gap-2">
            @if(auth()->user()->hasPermission('manage_users'))
            <a href="{{ route('admin.users.edit', $user) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Edit
            </a>
            @endif
            @if(auth()->user()->hasPermission('ban_users'))
                @if($user->is_banned)
                    <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            Unban
                        </button>
                    </form>
                @else
                    <button type="button" onclick="openBanModal({{ $user->id }})" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Ban
                    </button>
                @endif
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">User Stats</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="text-center p-4 bg-gray-50 rounded">
                <p class="text-2xl font-bold">{{ $userStats['posts_count'] }}</p>
                <p class="text-gray-500">Posts</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded">
                <p class="text-2xl font-bold">{{ $userStats['comments_count'] }}</p>
                <p class="text-gray-500">Comments</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded">
                <p class="text-2xl font-bold">{{ $userStats['likes_given'] }}</p>
                <p class="text-gray-500">Likes Given</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6 mt-6">
    <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
    <div class="space-y-3">
        @forelse($recentActivity as $log)
            <div class="border-b pb-2">
                <p class="text-sm">
                    <span class="font-medium">{{ $log->action }}</span>
                    {{ $log->resource_type }}
                </p>
                <p class="text-xs text-gray-400">{{ $log->created_at->format('M d, Y H:i') }}</p>
            </div>
        @empty
            <p class="text-gray-500">No recent activity</p>
        @endforelse
    </div>
</div>

<div id="banModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-semibold mb-4">Ban User</h3>
        <form id="banForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Reason</label>
                <textarea name="reason" required class="w-full border rounded px-3 py-2" rows="3"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeBanModal()" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Ban User</button>
            </div>
        </form>
    </div>
</div>

<script>
function openBanModal(userId) {
    document.getElementById('banForm').action = '/admin/users/' + userId + '/ban';
    document.getElementById('banModal').classList.remove('hidden');
}
function closeBanModal() {
    document.getElementById('banModal').classList.add('hidden');
}
</script>
@endsection
