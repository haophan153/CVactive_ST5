<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\TemplateCategory;

class TemplateController extends Controller
{
    public function index(Request $request)
    {
        $term = trim((string) $request->input('q', ''));
        $sort = $request->input('sort', 'popular');
        $filter = $request->input('filter', 'all');

        $categories = TemplateCategory::withCount(['activeTemplates'])
            ->orderByDesc('active_templates_count')
            ->get();

        $query = Template::with('category')->active();

        if ($filter === 'free') {
            $query->free();
        } elseif ($filter === 'premium') {
            $query->premium();
        }

        if ($request->filled('category')) {
            $query->ofCategory($request->category);
        }

        if ($term !== '') {
            $query->search($term);
        }

        $sortMap = [
            'popular'   => ['usage_count', 'desc'],
            'newest'    => ['id', 'desc'],
            'oldest'    => ['id', 'asc'],
            'name_asc'  => ['name', 'asc'],
            'name_desc' => ['name', 'desc'],
        ];
        [$sortCol, $sortDir] = $sortMap[$sort] ?? ['usage_count', 'desc'];
        $templates = $query->orderBy($sortCol, $sortDir)->paginate(12)->withQueryString();

        $stats = [
            'total'    => Template::active()->count(),
            'free'     => Template::active()->free()->count(),
            'premium'  => Template::active()->premium()->count(),
            'total_use'=> (int) Template::active()->sum('usage_count'),
        ];

        return view('templates.index', compact(
            'templates', 'categories', 'stats', 'term', 'sort', 'filter'
        ));
    }

    public function preview(Template $template)
    {
        if (!$template->is_active) {
            abort(404);
        }

        $cv = $this->getSampleCv($template);

        return view('templates.preview', compact('template', 'cv'));
    }

    private function getSampleCv(Template $template): \App\Models\Cv
    {
        $cv = new \App\Models\Cv([
            'title'       => 'CV Mẫu – ' . $template->name,
            'theme_color' => $template->theme_color ?? '#4F46E5',
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
                    (object) ['content' => [
                        'position'   => 'Web Developer',
                        'company'    => 'Công ty TNHH XYZ',
                        'location'   => 'TP. Hồ Chí Minh',
                        'start_date' => '06/2020',
                        'end_date'   => '12/2021',
                        'description' => 'Xây dựng website bán hàng và hệ thống quản lý nội bộ.',
                    ], 'sort_order' => 1],
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
                    (object) ['content' => ['name' => 'PHP / Laravel', 'level' => 'expert'], 'sort_order' => 0],
                    (object) ['content' => ['name' => 'Vue.js / React', 'level' => 'advanced'], 'sort_order' => 1],
                    (object) ['content' => ['name' => 'MySQL / PostgreSQL', 'level' => 'advanced'], 'sort_order' => 2],
                    (object) ['content' => ['name' => 'Docker / CI/CD', 'level' => 'intermediate'], 'sort_order' => 3],
                ]),
            ],
            (object) [
                'type'       => 'certifications',
                'title'      => 'Chứng chỉ',
                'is_visible' => true,
                'items'      => collect([
                    (object) ['content' => [
                        'name'   => 'AWS Certified Developer',
                        'issuer' => 'Amazon Web Services',
                        'date'   => '2023',
                    ], 'sort_order' => 0],
                ]),
            ],
            (object) [
                'type'       => 'projects',
                'title'      => 'Dự án nổi bật',
                'is_visible' => true,
                'items'      => collect([
                    (object) ['content' => [
                        'name'        => 'Hệ thống quản lý nhân sự HRM+',
                        'tech'        => 'Laravel, Vue 3, MySQL',
                        'url'         => 'https://example.com',
                        'description' => 'Xây dựng hệ thống HRM hoàn chỉnh cho doanh nghiệp 500+ nhân viên.',
                    ], 'sort_order' => 0],
                ]),
            ],
        ]);

        $cv->setRelation('sections', $sections);

        return $cv;
    }
}
