<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\JobPost;
use App\Models\Template;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 2) {
            return response()->json(['results' => []]);
        }

        $like = '%' . $term . '%';

        $users = User::query()
            ->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)->orWhere('email', 'like', $like);
            })
            ->limit(5)->get(['id', 'name', 'email', 'role']);

        $jobs = JobPost::query()
            ->where('title', 'like', $like)
            ->orWhere('company_name', 'like', $like)
            ->limit(5)->get(['id', 'title', 'company_name', 'status']);

        $templates = Template::query()
            ->where('name', 'like', $like)
            ->limit(5)->get(['id', 'name']);

        $posts = BlogPost::query()
            ->where('title', 'like', $like)
            ->limit(5)->get(['id', 'title', 'status']);

        $results = [];

        if ($users->count() > 0) {
            $results[] = [
                'label' => 'Người dùng',
                'items' => $users->map(fn ($u) => [
                    'title'    => $u->name,
                    'subtitle' => ($u->email ?? '') . ' • ' . ucfirst($u->role),
                    'url'      => route('admin.users.show', $u),
                ])->all(),
            ];
        }

        if ($jobs->count() > 0) {
            $results[] = [
                'label' => 'Tin tuyển dụng',
                'items' => $jobs->map(fn ($j) => [
                    'title'    => $j->title,
                    'subtitle' => ($j->company_name ?? '') . ' • ' . ($j->status ?? ''),
                    'url'      => route('admin.job-posts.show', $j),
                ])->all(),
            ];
        }

        if ($templates->count() > 0) {
            $results[] = [
                'label' => 'Templates',
                'items' => $templates->map(fn ($t) => [
                    'title'    => $t->name,
                    'subtitle' => 'Template CV',
                    'url'      => route('admin.templates.edit', $t),
                ])->all(),
            ];
        }

        if ($posts->count() > 0) {
            $results[] = [
                'label' => 'Bài viết',
                'items' => $posts->map(fn ($p) => [
                    'title'    => $p->title,
                    'subtitle' => ucfirst($p->status ?? 'draft'),
                    'url'      => route('admin.blog.edit', $p),
                ])->all(),
            ];
        }

        return response()->json(['results' => $results]);
    }
}
