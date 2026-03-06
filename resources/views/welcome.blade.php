<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CVactive – Tạo CV chuyên nghiệp online</title>
    <meta name="description" content="Tạo CV đẹp, chuyên nghiệp trong vài phút. Hàng chục mẫu CV miễn phí, xuất PDF, chia sẻ link ngay.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-gradient { background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 50%, #2563EB 100%); }
        .feature-card:hover { transform: translateY(-4px); }
        .feature-card { transition: all .3s ease; }
    </style>
</head>
<body class="bg-white text-gray-900">

    {{-- NAVBAR --}}
    <nav class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900">CV<span class="text-indigo-600">active</span></span>
                </a>

                <div class="hidden md:flex items-center space-x-6 text-sm font-medium">
                    <a href="{{ route('templates.index') }}" class="text-gray-600 hover:text-gray-900 transition">Mẫu CV</a>
                    <a href="{{ route('pricing') }}" class="text-gray-600 hover:text-gray-900 transition">Bảng giá</a>
                    <a href="{{ route('blog.index') }}" class="text-gray-600 hover:text-gray-900 transition">Blog</a>
                    <a href="{{ route('faq') }}" class="text-gray-600 hover:text-gray-900 transition">FAQ</a>
                </div>

                <div class="flex items-center space-x-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Dashboard</a>
                        <a href="{{ route('cv.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition shadow-sm">
                            + Tạo CV
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition shadow-sm">
                            Dùng miễn phí
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- HERO --}}
    <section class="hero-gradient text-white py-24 px-4 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-72 h-72 bg-white rounded-full filter blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-purple-300 rounded-full filter blur-3xl"></div>
        </div>
        <div class="max-w-5xl mx-auto text-center relative z-10">
            <div class="inline-flex items-center space-x-2 bg-white/10 border border-white/20 rounded-full px-4 py-1.5 text-sm mb-6">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                <span>Hơn 10,000+ CV đã được tạo</span>
            </div>
            <h1 class="text-5xl md:text-6xl font-extrabold leading-tight mb-6">
                Tạo CV <span class="text-yellow-300">chuyên nghiệp</span><br>trong vài phút
            </h1>
            <p class="text-xl text-white/80 max-w-2xl mx-auto mb-10 leading-relaxed">
                CVactive giúp bạn tạo ra CV ấn tượng với hàng chục mẫu đẹp, xuất PDF chất lượng cao và chia sẻ link ngay với nhà tuyển dụng.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}"
                    class="w-full sm:w-auto px-8 py-4 bg-white text-indigo-700 font-bold rounded-xl hover:bg-gray-50 transition shadow-lg text-lg">
                    Tạo CV miễn phí ngay →
                </a>
                <a href="{{ route('templates.index') }}"
                    class="w-full sm:w-auto px-8 py-4 bg-white/10 border border-white/30 text-white font-semibold rounded-xl hover:bg-white/20 transition text-lg">
                    Xem mẫu CV
                </a>
            </div>
            <p class="mt-4 text-sm text-white/50">Miễn phí mãi mãi · Không cần thẻ tín dụng · Xuất PDF ngay</p>
        </div>
    </section>

    {{-- STATS --}}
    <section class="bg-gray-50 py-12 border-b border-gray-100">
        <div class="max-w-5xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-3xl font-extrabold text-indigo-600">10K+</div>
                    <div class="text-sm text-gray-500 mt-1">CV đã tạo</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-indigo-600">50+</div>
                    <div class="text-sm text-gray-500 mt-1">Mẫu CV chuyên nghiệp</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-indigo-600">98%</div>
                    <div class="text-sm text-gray-500 mt-1">Hài lòng với kết quả</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-indigo-600">5 phút</div>
                    <div class="text-sm text-gray-500 mt-1">Thời gian tạo CV trung bình</div>
                </div>
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section class="py-20 px-4">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-14">
                <span class="text-indigo-600 font-semibold text-sm uppercase tracking-wide">Cách hoạt động</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-2 text-gray-900">Tạo CV trong 3 bước đơn giản</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z"/></svg>
                    </div>
                    <div class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center mx-auto -mt-2 mb-4 text-sm font-bold">1</div>
                    <h3 class="text-lg font-bold mb-2">Chọn mẫu CV</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Duyệt qua hàng chục mẫu CV đẹp, phân loại theo ngành nghề và phong cách.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                        <svg class="w-8 h-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center mx-auto -mt-2 mb-4 text-sm font-bold">2</div>
                    <h3 class="text-lg font-bold mb-2">Điền thông tin</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Nhập thông tin cá nhân, kinh nghiệm, học vấn và kỹ năng. Preview cập nhật ngay lập tức.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                        <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center mx-auto -mt-2 mb-4 text-sm font-bold">3</div>
                    <h3 class="text-lg font-bold mb-2">Tải xuống & Chia sẻ</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Xuất CV ra PDF chất lượng cao hoặc tạo link chia sẻ để gửi trực tiếp cho nhà tuyển dụng.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURES --}}
    <section class="py-20 px-4 bg-gray-50">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-14">
                <span class="text-indigo-600 font-semibold text-sm uppercase tracking-wide">Tính năng nổi bật</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-2">Mọi thứ bạn cần để tạo CV hoàn hảo</h2>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                $features = [
                    ['icon' => '⚡', 'title' => 'Live Preview', 'desc' => 'Xem CV thay đổi theo thời gian thực khi bạn nhập liệu. Không cần refresh.', 'color' => 'yellow'],
                    ['icon' => '🎨', 'title' => 'Tuỳ chỉnh thiết kế', 'desc' => 'Đổi màu chủ đề, font chữ, layout theo ý thích. Hàng triệu tổ hợp khác nhau.', 'color' => 'purple'],
                    ['icon' => '📄', 'title' => 'Xuất PDF chất lượng cao', 'desc' => 'Tải CV dưới dạng PDF sẵn sàng in, đúng chuẩn A4, độ phân giải cao.', 'color' => 'red'],
                    ['icon' => '🔗', 'title' => 'Chia sẻ link online', 'desc' => 'Tạo link chia sẻ CV ngay lập tức, gửi cho nhà tuyển dụng không cần đính kèm file.', 'color' => 'blue'],
                    ['icon' => '💾', 'title' => 'Tự động lưu', 'desc' => 'Dữ liệu được lưu tự động mỗi khi bạn gõ. Không bao giờ mất dữ liệu.', 'color' => 'green'],
                    ['icon' => '📱', 'title' => 'Responsive mobile', 'desc' => 'Chỉnh sửa CV trên điện thoại, tablet hay máy tính đều mượt mà.', 'color' => 'indigo'],
                ];
                @endphp
                @foreach($features as $f)
                <div class="feature-card bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="text-3xl mb-4">{{ $f['icon'] }}</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $f['title'] }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $f['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- TEMPLATES PREVIEW --}}
    <section class="py-20 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <span class="text-indigo-600 font-semibold text-sm uppercase tracking-wide">Mẫu CV</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-2">Hơn 50 mẫu CV chuyên nghiệp</h2>
                <p class="text-gray-500 mt-3">Được thiết kế bởi các chuyên gia HR, phù hợp mọi ngành nghề</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                $demoTemplates = [
                    ['name' => 'Classic Blue', 'cat' => 'Chuyên nghiệp', 'free' => true],
                    ['name' => 'Modern Dark', 'cat' => 'Hiện đại', 'free' => true],
                    ['name' => 'Minimal White', 'cat' => 'Đơn giản', 'free' => true],
                    ['name' => 'Creative Designer', 'cat' => 'Sáng tạo', 'free' => false],
                ];
                $colors = ['bg-indigo-500', 'bg-gray-800', 'bg-white', 'bg-gradient-to-br from-pink-500 to-purple-600'];
                @endphp
                @foreach($demoTemplates as $idx => $tpl)
                <div class="group relative bg-white rounded-xl border-2 border-gray-100 overflow-hidden hover:border-indigo-300 hover:shadow-lg transition cursor-pointer">
                    <div class="aspect-[3/4] {{ $colors[$idx] }} flex items-center justify-center">
                        <div class="w-full h-full p-3 {{ $idx === 2 ? 'bg-white' : '' }}">
                            <div class="w-full h-3 {{ $idx < 3 ? 'bg-white/30' : 'bg-white/50' }} rounded mb-2"></div>
                            <div class="w-2/3 h-2 {{ $idx < 3 ? 'bg-white/20' : 'bg-white/30' }} rounded mb-4"></div>
                            @for($i = 0; $i < 4; $i++)
                            <div class="w-full h-2 {{ $idx < 3 ? 'bg-white/15' : 'bg-white/20' }} rounded mb-1.5"></div>
                            @endfor
                        </div>
                    </div>
                    @if(!$tpl['free'])
                    <div class="absolute top-2 right-2 bg-amber-400 text-white text-xs font-bold px-2 py-0.5 rounded-full">PRO</div>
                    @endif
                    <div class="p-3">
                        <p class="font-semibold text-sm text-gray-800">{{ $tpl['name'] }}</p>
                        <p class="text-xs text-gray-400">{{ $tpl['cat'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-8">
                <a href="{{ route('templates.index') }}" class="inline-flex items-center space-x-2 px-6 py-3 border-2 border-indigo-600 text-indigo-600 font-semibold rounded-xl hover:bg-indigo-50 transition">
                    <span>Xem tất cả mẫu CV</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- PRICING TEASER --}}
    <section class="py-20 px-4 bg-gray-50">
        <div class="max-w-4xl mx-auto text-center">
            <span class="text-indigo-600 font-semibold text-sm uppercase tracking-wide">Bảng giá</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-2 mb-4">Miễn phí để bắt đầu</h2>
            <p class="text-gray-500 mb-10">Tạo CV chuyên nghiệp hoàn toàn miễn phí. Nâng cấp để mở khóa thêm tính năng.</p>
            <div class="grid md:grid-cols-2 gap-6 text-left">
                <div class="bg-white rounded-2xl p-8 border-2 border-gray-100 shadow-sm">
                    <div class="text-2xl font-bold text-gray-900 mb-1">Free</div>
                    <div class="text-4xl font-extrabold text-gray-900 mb-1">0₫ <span class="text-base font-normal text-gray-400">/tháng</span></div>
                    <p class="text-gray-500 text-sm mb-6">Mãi mãi miễn phí</p>
                    <ul class="space-y-3 text-sm text-gray-700 mb-8">
                        <li class="flex items-center space-x-2"><span class="text-green-500">✓</span><span>2 CV miễn phí</span></li>
                        <li class="flex items-center space-x-2"><span class="text-green-500">✓</span><span>Mẫu CV cơ bản</span></li>
                        <li class="flex items-center space-x-2"><span class="text-green-500">✓</span><span>Xuất PDF</span></li>
                        <li class="flex items-center space-x-2"><span class="text-green-500">✓</span><span>Chia sẻ link online</span></li>
                    </ul>
                    <a href="{{ route('register') }}" class="block text-center py-3 border-2 border-indigo-600 text-indigo-600 font-semibold rounded-xl hover:bg-indigo-50 transition">Bắt đầu miễn phí</a>
                </div>
                <div class="bg-indigo-600 rounded-2xl p-8 border-2 border-indigo-600 shadow-lg relative overflow-hidden">
                    <div class="absolute top-4 right-4 bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full">Phổ biến nhất</div>
                    <div class="text-2xl font-bold text-white mb-1">Pro</div>
                    <div class="text-4xl font-extrabold text-white mb-1">99K₫ <span class="text-base font-normal text-white/60">/tháng</span></div>
                    <p class="text-indigo-200 text-sm mb-6">Mở khóa toàn bộ tính năng</p>
                    <ul class="space-y-3 text-sm text-white mb-8">
                        <li class="flex items-center space-x-2"><span class="text-yellow-300">✓</span><span>Không giới hạn CV</span></li>
                        <li class="flex items-center space-x-2"><span class="text-yellow-300">✓</span><span>Tất cả mẫu CV Premium</span></li>
                        <li class="flex items-center space-x-2"><span class="text-yellow-300">✓</span><span>Xuất PDF & PNG chất lượng cao</span></li>
                        <li class="flex items-center space-x-2"><span class="text-yellow-300">✓</span><span>Tùy chỉnh màu sắc & font</span></li>
                        <li class="flex items-center space-x-2"><span class="text-yellow-300">✓</span><span>Ưu tiên hỗ trợ</span></li>
                    </ul>
                    <a href="{{ route('register') }}" class="block text-center py-3 bg-white text-indigo-700 font-bold rounded-xl hover:bg-gray-50 transition">Dùng thử 7 ngày miễn phí</a>
                </div>
            </div>
        </div>
    </section>

    {{-- TESTIMONIALS --}}
    <section class="py-20 px-4">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold">Người dùng nói gì về CVactive</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                @php
                $testimonials = [
                    ['name' => 'Nguyễn Minh Tuấn', 'role' => 'Software Engineer', 'text' => 'Tôi tạo được CV chuyên nghiệp trong 10 phút. Nhà tuyển dụng rất ấn tượng với thiết kế sạch sẽ và chuyên nghiệp.', 'avatar' => 'NM'],
                    ['name' => 'Trần Thị Hoa', 'role' => 'Marketing Manager', 'text' => 'CVactive giúp tôi thể hiện được cá tính qua CV. Tôi đã nhận được 3 offer sau khi dùng nền tảng này.', 'avatar' => 'TH'],
                    ['name' => 'Lê Văn Nam', 'role' => 'Fresh Graduate', 'text' => 'Là sinh viên mới ra trường, CVactive giúp tôi tạo CV đẹp không thua gì người có kinh nghiệm. Miễn phí nữa!', 'avatar' => 'LN'],
                ];
                @endphp
                @foreach($testimonials as $t)
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-sm">{{ $t['avatar'] }}</div>
                        <div>
                            <p class="font-semibold text-sm text-gray-900">{{ $t['name'] }}</p>
                            <p class="text-xs text-gray-400">{{ $t['role'] }}</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">"{{ $t['text'] }}"</p>
                    <div class="mt-3 text-yellow-400 text-sm">★★★★★</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="hero-gradient py-20 px-4">
        <div class="max-w-3xl mx-auto text-center text-white">
            <h2 class="text-3xl md:text-4xl font-extrabold mb-4">Sẵn sàng tạo CV ấn tượng?</h2>
            <p class="text-white/80 text-lg mb-8">Tham gia cùng hàng nghìn người dùng đang dùng CVactive để chinh phục nhà tuyển dụng.</p>
            <a href="{{ route('register') }}"
                class="inline-block px-10 py-4 bg-white text-indigo-700 font-bold text-lg rounded-xl hover:bg-gray-50 transition shadow-lg">
                Tạo CV miễn phí ngay →
            </a>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-gray-900 text-gray-400 py-12 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-7 h-7 bg-indigo-600 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <span class="font-bold text-white">CVactive</span>
                    </div>
                    <p class="text-sm leading-relaxed">Nền tảng tạo CV chuyên nghiệp hàng đầu Việt Nam.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-3 text-sm">Sản phẩm</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('templates.index') }}" class="hover:text-white transition">Mẫu CV</a></li>
                        <li><a href="{{ route('pricing') }}" class="hover:text-white transition">Bảng giá</a></li>
                        <li><a href="{{ route('cv.create') }}" class="hover:text-white transition">Tạo CV ngay</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-3 text-sm">Tài nguyên</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('blog.index') }}" class="hover:text-white transition">Blog</a></li>
                        <li><a href="{{ route('faq') }}" class="hover:text-white transition">FAQ</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">Liên hệ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-3 text-sm">Liên hệ</h4>
                    <ul class="space-y-2 text-sm">
                        <li>✉ support@cvactive.vn</li>
                        <li>📍 Hà Nội, Việt Nam</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 pt-6 text-sm text-center">
                © 2026 CVactive. Tất cả quyền được bảo lưu.
            </div>
        </div>
    </footer>

</body>
</html>
