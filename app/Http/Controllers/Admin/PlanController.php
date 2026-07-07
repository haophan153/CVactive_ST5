<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('users')->withSum('payments as revenue', 'amount')->get();

        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'slug'     => 'required|string|max:100|unique:plans,slug',
            'price'    => 'required|numeric|min:0',
            'cv_limit' => 'nullable|integer|min:0',
            'features' => 'nullable|string',
            'is_active'=> 'boolean',
        ]);

        $data['features'] = $this->parseFeatures($request->input('features'));
        $data['is_active'] = $request->boolean('is_active', true);

        Plan::create($data);

        return redirect()->route('admin.plans.index')->with('success', 'Đã tạo gói dịch vụ.');
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'slug'     => 'required|string|max:100|unique:plans,slug,' . $plan->id,
            'price'    => 'required|numeric|min:0',
            'cv_limit' => 'nullable|integer|min:0',
            'features' => 'nullable|string',
            'is_active'=> 'boolean',
        ]);

        $data['features'] = $this->parseFeatures($request->input('features'));
        $data['is_active'] = $request->boolean('is_active');

        if (!$data['is_active'] && $plan->users()->count() > 0 && !$request->boolean('confirmed')) {
            return back()->withInput()->with('error', "Gói đang có {$plan->users()->count()} người dùng. Xác nhận deactivate để tiếp tục.");
        }

        $plan->update($data);

        return redirect()->route('admin.plans.index')->with('success', 'Đã cập nhật gói.');
    }

    public function destroy(Plan $plan)
    {
        if ($plan->users()->count() > 0) {
            return back()->with('error', "Không thể xóa: gói đang có {$plan->users()->count()} người dùng.");
        }
        $plan->delete();
        return redirect()->route('admin.plans.index')->with('success', 'Đã xóa gói.');
    }

    public function toggle(Plan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);
        return response()->json(['ok' => true, 'value' => (bool) $plan->fresh()->is_active]);
    }

    private function parseFeatures(?string $features): array
    {
        if (!$features) return [];
        return array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $features))));
    }
}
