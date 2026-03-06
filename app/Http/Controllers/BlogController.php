<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Models\BlogCategory;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with(['author', 'category'])
            ->where('status', 'published')
            ->latest('published_at');

        if ($request->category) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        $posts      = $query->paginate(9);
        $categories = BlogCategory::withCount(['posts' => fn($q) => $q->where('status', 'published')])->get();

        return view('blog.index', compact('posts', 'categories'));
    }

    public function show(string $slug)
    {
        $post = BlogPost::with(['author', 'category'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $post->increment('views_count');

        $related = BlogPost::with('author')
            ->where('status', 'published')
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'related'));
    }
}
