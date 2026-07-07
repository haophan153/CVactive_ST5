<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Models\BlogCategory;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $term = trim((string) $request->input('q', ''));

        $posts = BlogPost::with(['author', 'category'])
            ->published()
            ->when($request->category, fn($q) => $q->ofCategory($request->category))
            ->when($term !== '', fn($q) => $q->search($term))
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $featured = BlogPost::with(['author', 'category'])
            ->published()
            ->featured()
            ->latest('published_at')
            ->limit(1)
            ->first();

        $popular = BlogPost::with(['author', 'category'])
            ->published()
            ->orderByDesc('views_count')
            ->limit(5)
            ->get();

        $recent = BlogPost::with('category')
            ->published()
            ->latest('published_at')
            ->limit(5)
            ->get();

        $categories = BlogCategory::withCount(['posts' => fn($q) => $q->published()])
            ->orderByDesc('posts_count')
            ->get();

        $stats = [
            'total_posts'    => BlogPost::published()->count(),
            'total_views'    => (int) BlogPost::published()->sum('views_count'),
            'total_authors'  => BlogPost::published()->distinct('author_id')->count('author_id'),
        ];

        return view('blog.index', compact(
            'posts', 'categories', 'featured', 'popular', 'recent', 'stats', 'term'
        ));
    }

    public function show(string $slug)
    {
        $post = BlogPost::with(['author', 'category'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $post->increment('views_count');

        $related = BlogPost::with('author')
            ->published()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn($q) => $q->where('category_id', $post->category_id))
            ->latest('published_at')
            ->limit(3)
            ->get();

        $popular = BlogPost::published()
            ->orderByDesc('views_count')
            ->where('id', '!=', $post->id)
            ->limit(4)
            ->get();

        $next = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where('published_at', '>', $post->published_at)
            ->orderBy('published_at', 'asc')
            ->first();

        return view('blog.show', compact('post', 'related', 'popular', 'next'));
    }
}
