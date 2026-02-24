@extends('admin.layouts.main')

@section('title', 'Edit Category')
@section('header', 'Edit Category')

@section('content')
<form method="POST" action="{{ route('admin.categories.update', $category) }}">
    @csrf
    @method('PUT')
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
            <input type="text" name="name" value="{{ $category->name }}" 
                   class="w-full border rounded px-3 py-2" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Slug</label>
            <input type="text" name="slug" value="{{ $category->slug }}" 
                   class="w-full border rounded px-3 py-2">
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Update
            </button>
            <a href="{{ route('admin.categories.index') }}" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
                Cancel
            </a>
        </div>
    </div>
</form>
@endsection
