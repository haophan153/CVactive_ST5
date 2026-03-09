<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\TemplateCategory;

class TemplateController extends Controller
{
    /**
     * Display a listing of active templates (optional filter by category).
     */
    public function index(Request $request)
    {
        $categories = TemplateCategory::withCount(['templates' => function ($query) {
            $query->where('is_active', true);
        }])->get();

        $query = Template::where('is_active', true)->with('category');

        if ($request->filled('category')) {
            $category = TemplateCategory::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        $templates = $query->orderBy('usage_count', 'desc')->get();

        return view('templates.index', compact('categories', 'templates'));
    }

    /**
     * Preview template with sample data (no auth required for viewing).
     */
    public function preview(Template $template)
    {
        if (!$template->is_active) {
            abort(404);
        }

        $cv = $this->getSampleCv($template);

        return view('templates.preview', compact('template', 'cv'));
    }

    /**
     * Sample CV data for template preview.
     */
    private function getSampleCv(Template $template): \App\Models\Cv
    {
        $cv = new \App\Models\Cv([
            'title'       => 'CV Mẫu – ' . $template->name,
            'theme_color' => '#4F46E5',
            'font_family' => 'Inter',
            'objective'   => 'Mục tiêu nghề nghiệp ngắn gọn: mong muốn đóng góp vào đội ngũ phát triển sản phẩm và học hỏi thêm kinh nghiệm trong môi trường chuyên nghiệp.',
            'personal_info' => [
                'full_name' => 'Nguyễn Văn Mẫu',
                'email'     => 'email@example.com',
                'phone'     => '0901 234 567',
                'address'   => 'Hà Nội, Việt Nam',
                'website'   => 'https://example.com',
                'linkedin'  => 'linkedin.com/in/nguyenvanmau',
                'github'    => 'github.com/nguyenvanmau',
                'avatar'    => '',
            ],
        ]);
        $cv->setRelation('template', $template);

        $sections = collect([
            (object) [
                'type'       => 'experience',
                'title'      => 'Kinh nghiệm làm việc',
                'is_visible' => true,
                'items'      => collect([
                    (object) ['content' => [
                        'position'   => 'Senior Developer',
                        'company'    => 'Công ty Cổ phần ABC',
                        'location'   => 'Hà Nội',
                        'start_date' => '01/2022',
                        'end_date'   => 'Hiện tại',
                        'description' => 'Phát triển và tối ưu ứng dụng web. Làm việc với Laravel, Vue.js.',
                    ], 'sort_order' => 0],
                ]),
            ],
            (object) [
                'type'       => 'education',
                'title'      => 'Học vấn',
                'is_visible' => true,
                'items'      => collect([
                    (object) ['content' => [
                        'degree'     => 'Cử nhân Công nghệ Thông tin',
                        'school'     => 'Đại học Bách Khoa Hà Nội',
                        'gpa'        => '3.5/4.0',
                        'start_date' => '2018',
                        'end_date'   => '2022',
                    ], 'sort_order' => 0],
                ]),
            ],
            (object) [
                'type'       => 'skills',
                'title'      => 'Kỹ năng',
                'is_visible' => true,
                'items'      => collect([
                    (object) ['content' => ['name' => 'PHP', 'level' => 'expert'], 'sort_order' => 0],
                    (object) ['content' => ['name' => 'Laravel', 'level' => 'advanced'], 'sort_order' => 1],
                    (object) ['content' => ['name' => 'Vue.js', 'level' => 'intermediate'], 'sort_order' => 2],
                ]),
            ],
        ]);

        $cv->setRelation('sections', $sections);

        return $cv;
    }
}
