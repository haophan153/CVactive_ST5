<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\TemplateCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    public function index()
    {
        $templates  = Template::with('category')->latest()->paginate(15);
        $categories = TemplateCategory::orderBy('name')->get();
        return view('admin.templates.index', compact('templates', 'categories'));
    }

    public function create()
    {
        $categories = TemplateCategory::orderBy('name')->get();
        return view('admin.templates.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'blade_view'  => 'required|string|max:100',
            'category_id' => 'nullable|exists:template_categories,id',
            'thumbnail'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_premium'  => 'boolean',
            'is_active'   => 'boolean',
        ]);

        $data = $request->only('name', 'blade_view', 'category_id');
        $data['slug']       = Str::slug($request->name) . '-' . Str::random(4);
        $data['is_premium'] = $request->boolean('is_premium');
        $data['is_active']  = $request->boolean('is_active', true);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = asset('storage/' . $request->file('thumbnail')->store('templates', 'public'));
        }

        Template::create($data);

        return redirect()->route('admin.templates.index')->with('success', 'Đã tạo template mới.');
    }

    public function edit(Template $template)
    {
        $categories = TemplateCategory::orderBy('name')->get();
        return view('admin.templates.edit', compact('template', 'categories'));
    }

    public function update(Request $request, Template $template)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'blade_view'  => 'required|string|max:100',
            'category_id' => 'nullable|exists:template_categories,id',
            'thumbnail'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_premium'  => 'boolean',
            'is_active'   => 'boolean',
        ]);

        $data = $request->only('name', 'blade_view', 'category_id');
        $data['is_premium'] = $request->boolean('is_premium');
        $data['is_active']  = $request->boolean('is_active');

        if ($request->hasFile('thumbnail')) {
            // Xóa thumbnail cũ
            if ($template->thumbnail && !str_starts_with($template->thumbnail, 'http')) {
                $oldPath = str_replace(asset('storage/'), '', $template->thumbnail);
                Storage::disk('public')->delete($oldPath);
            }
            $data['thumbnail'] = asset('storage/' . $request->file('thumbnail')->store('templates', 'public'));
        }

        $template->update($data);

        return redirect()->route('admin.templates.index')->with('success', "Đã cập nhật template {$template->name}.");
    }

    public function destroy(Template $template)
    {
        if ($template->cvs()->count() > 0) {
            return back()->with('error', "Không thể xóa: template đang được dùng bởi {$template->cvs()->count()} CV.");
        }

        $template->delete();
        return redirect()->route('admin.templates.index')->with('success', 'Đã xóa template.');
    }
}
