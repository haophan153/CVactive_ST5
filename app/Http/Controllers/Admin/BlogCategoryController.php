<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::withCount('posts')->orderBy('name')->get();
        return view('admin.blog-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.blog-categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:blog_categories,name',
            'slug'        => 'required|string|max:100|unique:blog_categories,slug',
            'color'       => 'required|string|max:20',
            'icon'        => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        BlogCategory::create($data);

        return redirect()->route('admin.blog-categories.index')->with('success', 'Đã tạo danh mục.');
    }

    public function edit(BlogCategory $blogCategory)
    {
        return view('admin.blog-categories.edit', compact('blogCategory'));
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:blog_categories,name,' . $blogCategory->id,
            'slug'        => 'required|string|max:100|unique:blog_categories,slug,' . $blogCategory->id,
            'color'       => 'required|string|max:20',
            'icon'        => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        $blogCategory->update($data);

        return redirect()->route('admin.blog-categories.index')->with('success', 'Đã cập nhật.');
    }

    public function destroy(BlogCategory $blogCategory)
    {
        if ($blogCategory->posts()->count() > 0) {
            return back()->with('error', "Không thể xóa: danh mục có {$blogCategory->posts()->count()} bài viết.");
        }
        $blogCategory->delete();
        return redirect()->route('admin.blog-categories.index')->with('success', 'Đã xóa.');
    }
}
