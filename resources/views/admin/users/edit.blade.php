@extends('admin.layouts.main')

@section('title', 'Edit User')
@section('header', 'Edit User')

@section('content')
<form method="POST" action="{{ route('admin.users.update', $user) }}">
    @csrf
    @method('PUT')
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-lg font-semibold">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" value="{{ $user->name }}" 
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                    @if(!$isSuperAdmin && !$isAdmin && !$isModerator && !$isEditor)
                        <span class="text-gray-400 font-normal">(Read only)</span>
                    @endif
                </label>
                @if($isSuperAdmin || $isAdmin || $isModerator || $isEditor)
                    <input type="email" name="email" value="{{ $user->email }}" 
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                @else
                    <input type="email" value="{{ $user->email }}" 
                           class="w-full border border-gray-200 rounded px-3 py-2 bg-gray-50 text-gray-500" 
                           disabled>
                    <input type="hidden" name="email" value="{{ $user->email }}">
                @endif
            </div>
        </div>
        
        @if($isSuperAdmin || $isAdmin)
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">Assign Role</label>
            <div class="flex flex-wrap gap-3">
                @foreach($roles as $role)
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="role_id" value="{{ $role->id }}" 
                               {{ $user->roles->first()?->id == $role->id ? 'checked' : '' }}
                               class="form-radio h-4 w-4 text-blue-600">
                        <span class="ml-2 text-sm text-gray-700">{{ $role->display_name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        @endif
        
        <div class="flex gap-3 mt-6">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Save Changes
            </button>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                Cancel
            </a>
        </div>
    </div>
</form>
@endsection
