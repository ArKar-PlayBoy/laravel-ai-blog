<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('posts')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $currentUser = auth()->user();
        $currentUser->load('roles.permissions');
        
        if (! $currentUser->hasPermission('manage_categories')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to manage categories.');
        }

        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();
        $currentUser->load('roles.permissions');
        
        if (! $currentUser->hasPermission('manage_categories')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to manage categories.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories'],
        ]);

        $category = Category::create($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create',
            'resource_type' => 'Category',
            'resource_id' => $category->id,
            'old_values' => null,
            'new_values' => $category->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $currentUser = auth()->user();
        $currentUser->load('roles.permissions');
        
        if (! $currentUser->hasPermission('manage_categories')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to manage categories.');
        }

        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $currentUser = auth()->user();
        $currentUser->load('roles.permissions');
        
        if (! $currentUser->hasPermission('manage_categories')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to manage categories.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
        ]);

        $oldValues = $category->toArray();

        $category->update($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'resource_type' => 'Category',
            'resource_id' => $category->id,
            'old_values' => $oldValues,
            'new_values' => $category->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Request $request, Category $category)
    {
        $currentUser = auth()->user();
        $currentUser->load('roles.permissions');
        
        if (! $currentUser->hasPermission('manage_categories')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to manage categories.');
        }

        if ($category->posts()->count() > 0) {
            return back()->with('error', 'Cannot delete category with associated posts.');
        }

        $oldValues = $category->toArray();

        $category->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete',
            'resource_type' => 'Category',
            'resource_id' => $category->id,
            'old_values' => $oldValues,
            'new_values' => null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
