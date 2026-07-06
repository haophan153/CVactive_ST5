<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(Request $request): View
    {
        $selectedCategory = $request->query('category', 'all');
        $search           = trim((string) $request->query('q', ''));

        $query = Faq::query()->active()->ordered();

        if ($selectedCategory !== 'all' && array_key_exists($selectedCategory, Faq::CATEGORIES)) {
            $query->ofCategory($selectedCategory);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        $faqs = $query->get();

        $byCategory = Faq::query()
            ->active()
            ->ordered()
            ->get()
            ->groupBy('category');

        $popularFaqs = Faq::query()
            ->active()
            ->ordered()
            ->orderByDesc('views_count')
            ->limit(5)
            ->get();

        $totalFaqs = Faq::query()->active()->count();

        return view('faq', [
            'faqs'            => $faqs,
            'byCategory'      => $byCategory,
            'popularFaqs'     => $popularFaqs,
            'categories'      => Faq::CATEGORIES,
            'selectedCategory'=> $selectedCategory,
            'search'          => $search,
            'totalFaqs'       => $totalFaqs,
        ]);
    }
}