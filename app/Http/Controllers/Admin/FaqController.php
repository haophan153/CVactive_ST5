<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::query();

        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(fn($q) => $q->where('question', 'like', "%$term%")->orWhere('answer', 'like', "%$term%"));
        }

        $faqs = $query->ordered()->get()->groupBy(fn($f) => $f->category);

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question'   => 'required|string|max:500',
            'answer'     => 'required|string',
            'category'   => 'required|string|in:' . implode(',', array_keys(Faq::CATEGORIES)),
            'sort_order' => 'nullable|integer',
            'is_active'  => 'boolean',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? (Faq::max('sort_order') ?? 0) + 1;
        $data['is_active']  = $request->boolean('is_active', true);

        Faq::create($data);

        return redirect()->route('admin.faqs.index')->with('success', 'Đã thêm FAQ.');
    }

    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question'   => 'required|string|max:500',
            'answer'     => 'required|string',
            'category'   => 'required|string|in:' . implode(',', array_keys(Faq::CATEGORIES)),
            'sort_order' => 'nullable|integer',
            'is_active'  => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $faq->update($data);

        return redirect()->route('admin.faqs.index')->with('success', 'Đã cập nhật FAQ.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faqs.index')->with('success', 'Đã xóa FAQ.');
    }

    public function toggle(Faq $faq)
    {
        $faq->update(['is_active' => !$faq->is_active]);
        return response()->json(['ok' => true, 'value' => (bool) $faq->fresh()->is_active]);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:faqs,id',
        ]);

        foreach ($data['ids'] as $i => $id) {
            Faq::where('id', $id)->update(['sort_order' => $i + 1]);
        }

        return response()->json(['ok' => true]);
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:faqs,id',
        ]);

        $ids = $request->ids;

        if ($request->action === 'delete') {
            Faq::whereIn('id', $ids)->delete();
            return back()->with('success', 'Đã xóa ' . count($ids) . ' FAQ.');
        }
        if ($request->action === 'activate') {
            Faq::whereIn('id', $ids)->update(['is_active' => true]);
            return back()->with('success', 'Đã kích hoạt.');
        }
        if ($request->action === 'deactivate') {
            Faq::whereIn('id', $ids)->update(['is_active' => false]);
            return back()->with('success', 'Đã tắt.');
        }
    }
}
