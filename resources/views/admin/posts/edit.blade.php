@extends('admin.layouts.main')

@section('title', 'Edit Post')
@section('header', 'Edit Post')

@section('content')
<form method="POST" action="{{ route('admin.posts.update', $post) }}">
    @csrf
    @method('PUT')
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Title</label>
            <input type="text" name="title" value="{{ $post->title }}" 
                   class="w-full border rounded px-3 py-2" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Category</label>
            <select name="category_id" class="w-full border rounded px-3 py-2" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $post->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Body</label>
            <textarea name="body" rows="10" class="w-full border rounded px-3 py-2" required>{{ $post->body }}</textarea>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Featured Image URL</label>
            <input type="text" name="featured_image" value="{{ $post->featured_image }}" 
                   class="w-full border rounded px-3 py-2">
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2" required>
                <option value="draft" {{ $post->status == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ $post->status == 'published' ? 'selected' : '' }}>Published</option>
                <option value="archived" {{ $post->status == 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Update Post
            </button>
            <a href="{{ route('admin.posts.index') }}" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
                Cancel
            </a>
        </div>
    </div>
</form>
@endsection
