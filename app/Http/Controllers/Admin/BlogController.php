<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with(['author', 'category'])->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('author')) {
            $query->where('author_id', $request->author);
        }

        if ($request->filled('from')) $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to'))   $query->whereDate('created_at', '<=', $request->to);

        $sort = $request->input('sort', 'latest');
        $query = match($sort) {
            'oldest'    => $query->orderBy('created_at', 'asc'),
            'views'     => $query->orderByDesc('views_count'),
            'title'     => $query->orderBy('title', 'asc'),
            default     => $query->orderBy('created_at', 'desc'),
        };

        $posts      = $query->paginate(15)->withQueryString();
        $categories = BlogCategory::orderBy('name')->get();
        $authors    = \App\Models\User::orderBy('name')->limit(50)->get();

        return view('admin.blog.index', compact('posts', 'categories', 'authors'));
    }

    public function create()
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.blog.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'category_id'    => 'nullable|exists:blog_categories,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'status'         => 'required|in:draft,published',
            'excerpt'        => 'nullable|string|max:500',
        ]);

        $data = $request->only('title', 'content', 'category_id', 'status', 'excerpt');
        $data['author_id']    = auth()->id();
        $data['slug']         = Str::slug($request->title) . '-' . Str::random(5);
        $data['published_at'] = $request->status === 'published' ? now() : null;

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        BlogPost::create($data);

        return redirect()->route('admin.blog.index')->with('success', 'Bài viết đã được tạo.');
    }

    public function edit(BlogPost $blog)
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.blog.edit', compact('blog', 'categories'));
    }

    public function update(Request $request, BlogPost $blog)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'category_id'    => 'nullable|exists:blog_categories,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'status'         => 'required|in:draft,published',
            'excerpt'        => 'nullable|string|max:500',
        ]);

        $data = $request->only('title', 'content', 'category_id', 'status', 'excerpt');

        if ($request->status === 'published' && !$blog->published_at) {
            $data['published_at'] = now();
        } elseif ($request->status === 'draft') {
            $data['published_at'] = null;
        }

        if ($request->hasFile('featured_image')) {
            if ($blog->featured_image) {
                Storage::disk('public')->delete($blog->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        $blog->update($data);

        return redirect()->route('admin.blog.index')->with('success', "Đã cập nhật bài viết.");
    }

    public function destroy(BlogPost $blog)
    {
        if ($blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
        }
        $blog->delete();
        return redirect()->route('admin.blog.index')->with('success', 'Đã xóa bài viết.');
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'action' => 'required|in:publish,unpublish,delete',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:blog_posts,id',
        ]);

        $ids = $request->ids;

        if ($request->action === 'delete') {
            BlogPost::whereIn('id', $ids)->get()->each(function ($p) {
                if ($p->featured_image) Storage::disk('public')->delete($p->featured_image);
                $p->delete();
            });
            return back()->with('success', 'Đã xóa ' . count($ids) . ' bài viết.');
        }

        if ($request->action === 'publish') {
            BlogPost::whereIn('id', $ids)->update([
                'status' => 'published',
                'published_at' => now(),
            ]);
            return back()->with('success', 'Đã đăng ' . count($ids) . ' bài viết.');
        }

        if ($request->action === 'unpublish') {
            BlogPost::whereIn('id', $ids)->update([
                'status' => 'draft',
                'published_at' => null,
            ]);
            return back()->with('success', 'Đã hủy đăng ' . count($ids) . ' bài viết.');
        }
    }
}
