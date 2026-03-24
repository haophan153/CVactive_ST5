<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CVactive – Tạo CV chuyên nghiệp online</title>
    <meta name="description" content="Tạo CV đẹp, chuyên nghiệp trong vài phút. Hàng chục mẫu CV miễn phí, xuất PDF, chia sẻ link ngay.">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-bg {
            background: #0F172A;
        }
        .hero-accent {
            background: linear-gradient(135deg, #6366F1 0%, #818CF8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .section-label {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #6366F1;
            margin-bottom: 0.5rem;
        }
        .card-hover {
            transition: all .25s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }
        .divider {
            height: 1px;
            background: #E2E8F0;
        }
        .price-free {
            border: 1.5px solid #E2E8F0;
        }
        .price-pro {
            border: 1.5px solid #6366F1;
            background: #0F172A;
        }
    </style>
</head>
<body class="bg-white text-slate-900 antialiased">

    
    <nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2.5 shrink-0">
                    <img src="<?php echo e(asset('storage/avatars/logo/logo.png')); ?>" style="height:60px" alt="CVactive" class="h-9 w-auto object-contain">
                    <span class="text-xl font-bold text-slate-900">CV<span class="text-indigo-500">active</span></span>
                </a>

                <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                    <a href="<?php echo e(route('templates.index')); ?>" class="hover:text-slate-900 transition">Mẫu CV</a>
                    <a href="<?php echo e(route('jobs.index')); ?>" class="hover:text-slate-900 transition">Việc làm</a>
                    <a href="<?php echo e(route('pricing')); ?>" class="hover:text-slate-900 transition">Bảng giá</a>
                    <a href="<?php echo e(route('blog.index')); ?>" class="hover:text-slate-900 transition">Blog</a>
                    <a href="<?php echo e(route('faq')); ?>" class="hover:text-slate-900 transition">FAQ</a>
                </div>

                <div class="flex items-center gap-3">
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('dashboard')); ?>" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition">Dashboard</a>
                        <a href="<?php echo e(route('cv.create')); ?>"
                            class="px-4 py-2 bg-indigo-500 text-white text-sm font-semibold rounded-lg hover:bg-indigo-600 transition shadow-sm shadow-indigo-200">
                            + Tạo CV
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition">Đăng nhập</a>
                        <a href="<?php echo e(route('register')); ?>"
                            class="px-4 py-2 bg-indigo-500 text-white text-sm font-semibold rounded-lg hover:bg-indigo-600 transition shadow-sm shadow-indigo-200">
                            Dùng miễn phí
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    
    <section class="hero-bg text-white pt-24 pb-32 px-6 relative overflow-hidden">
        
        <div class="absolute inset-0 opacity-[0.03]"
            style="background-image: linear-gradient(#ffffff 1px, transparent 1px), linear-gradient(90deg, #ffffff 1px, transparent 1px); background-size: 60px 60px;">
        </div>
        <div class="max-w-4xl mx-auto text-center relative z-10 ">
            <div class="inline-flex items-center gap-2 bg-white/5 border border-white/10 rounded-full px-4 py-1.5 text-sm mb-8" style="margin-top: 22px;">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-slate-300">Hơn 10,000 CV đã được tạo</span>
            </div>
            <h1 class="text-5xl md:text-6xl font-extrabold leading-[1.1] tracking-tight mb-6">
                Tạo CV<br>
                <span class="hero-accent">chuyên nghiệp</span><br>
                trong vài phút
            </h1>
            <p class="text-lg text-slate-400 max-w-xl mx-auto mb-12 leading-relaxed" style="background-color: #0F172A;">
                CVactive giúp bạn xây dựng CV ấn tượng — hàng chục mẫu đẹp, xuất PDF chất lượng cao, chia sẻ link tức thì.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="<?php echo e(route('register')); ?>"
                    class="w-full sm:w-auto px-8 py-4 bg-indigo-500 text-white font-semibold rounded-xl hover:bg-indigo-400 transition shadow-xl shadow-indigo-900/40 text-base">
                    Tạo CV miễn phí
                </a>
                <a href="<?php echo e(route('templates.index')); ?>"
                    class="w-full sm:w-auto px-8 py-4 bg-white/5 border border-white/20 text-white font-medium rounded-xl hover:bg-white/10 transition text-base">
                    Xem mẫu CV
                </a>
            </div>
            <p class="mt-6 text-sm text-slate-500">Miễn phí mãi mãi &nbsp;&middot;&nbsp; Không cần thẻ tín dụng &nbsp;&middot;&nbsp; Xuất PDF ngay</p>
        </div>
    </section>

    
    <section class="bg-white border-b border-slate-100 py-14 px-6">
        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-slate-100 rounded-2xl overflow-hidden">
                <?php
                $stats = [
                    ['value' => '10,000+', 'label' => 'CV đã tạo'],
                    ['value' => '50+', 'label' => 'Mẫu CV'],
                    ['value' => '98%', 'label' => 'Hài lòng'],
                    ['value' => '5 phút', 'label' => 'Thời gian tạo'],
                ];
                ?>
                <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white px-8 py-6 text-center">
                    <div class="text-2xl font-extrabold text-slate-900 tracking-tight"><?php echo e($s['value']); ?></div>
                    <div class="text-sm text-slate-500 mt-1"><?php echo e($s['label']); ?></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    
    <section class="py-24 px-6">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <span class="section-label">Quy trình</span>
                <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 mt-1">Tạo CV trong 3 bước</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-px bg-slate-100 rounded-2xl overflow-hidden">
                <div class="bg-white px-8 py-10">
                    <div class="w-7 h-7 rounded-full bg-indigo-500 text-white flex items-center justify-center text-sm font-bold mb-6">1</div>
                    <h3 class="text-base font-semibold text-slate-900 mb-2">Chọn mẫu CV</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Duyệt hàng chục mẫu CV chuyên nghiệp, phân loại theo ngành nghề và phong cách riêng.</p>
                </div>
                <div class="bg-white px-8 py-10">
                    <div class="w-7 h-7 rounded-full bg-indigo-500 text-white flex items-center justify-center text-sm font-bold mb-6">2</div>
                    <h3 class="text-base font-semibold text-slate-900 mb-2">Điền thông tin</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Nhập thông tin cá nhân, kinh nghiệm, học vấn và kỹ năng. Preview cập nhật theo thời gian thực.</p>
                </div>
                <div class="bg-white px-8 py-10">
                    <div class="w-7 h-7 rounded-full bg-indigo-500 text-white flex items-center justify-center text-sm font-bold mb-6">3</div>
                    <h3 class="text-base font-semibold text-slate-900 mb-2">Tải xuống &amp; Chia sẻ</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Xuất PDF chất lượng cao hoặc tạo link chia sẻ gửi trực tiếp cho nhà tuyển dụng.</p>
                </div>
            </div>
        </div>
    </section>

    
    <section class="py-24 px-6 bg-slate-50">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-16">
                <span class="section-label">Tính năng</span>
                <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 mt-1">Mọi thứ bạn cần</h2>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <?php
                $features = [
                    ['title' => 'Live Preview', 'desc' => 'Xem CV thay đổi ngay khi bạn nhập liệu. Không cần refresh trang.'],
                    ['title' => 'Tuỳ chỉnh thiết kế', 'desc' => 'Đổi màu chủ đề, font chữ, layout. Hàng triệu tổ hợp khác nhau.'],
                    ['title' => 'Xuất PDF chất lượng cao', 'desc' => 'Tải CV dưới dạng PDF sẵn sàng in, đúng chuẩn A4, độ phân giải cao.'],
                    ['title' => 'Chia sẻ link online', 'desc' => 'Tạo link chia sẻ CV tức thì, gửi cho nhà tuyển dụng không cần đính kèm file.'],
                    ['title' => 'Tự động lưu', 'desc' => 'Dữ liệu được lưu tự động mỗi khi bạn gõ. Không bao giờ mất dữ liệu.'],
                    ['title' => 'Responsive mobile', 'desc' => 'Chỉnh sửa CV trên điện thoại, tablet hay máy tính đều mượt mà.'],
                ];
                ?>
                <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card-hover bg-white rounded-xl p-6 border border-slate-100">
                    <h3 class="text-base font-semibold text-slate-900 mb-1.5"><?php echo e($f['title']); ?></h3>
                    <p class="text-sm text-slate-500 leading-relaxed"><?php echo e($f['desc']); ?></p>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    
    <section class="py-24 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-14">
                <span class="section-label">Mẫu CV</span>
                <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 mt-1">Hơn 50 mẫu chuyên nghiệp</h2>
                <p class="text-slate-500 mt-3">Thiết kế bởi chuyên gia HR, phù hợp mọi ngành nghề</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                <?php
                $demoTemplates = [
                    ['name' => 'Classic Blue', 'cat' => 'Chuyên nghiệp'],
                    ['name' => 'Modern Dark', 'cat' => 'Hiện đại'],
                    ['name' => 'Minimal White', 'cat' => 'Đơn giản'],
                    ['name' => 'Creative Pro', 'cat' => 'Sáng tạo'],
                ];
                $tplBgs = ['bg-indigo-600', 'bg-slate-800', 'bg-white border border-slate-200', 'bg-gradient-to-br from-slate-800 to-indigo-700'];
                $tplLines = ['bg-white/30', 'bg-white/20', 'bg-slate-200', 'bg-white/20'];
                ?>
                <?php $__currentLoopData = $demoTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('templates.index')); ?>"
                    class="card-hover group block bg-white rounded-xl border border-slate-100 overflow-hidden">
                    <div class="aspect-[3/4] <?php echo e($tplBgs[$idx]); ?> p-4 flex flex-col gap-1.5">
                        <div class="w-3/4 h-2.5 <?php echo e($tplLines[$idx]); ?> rounded"></div>
                        <div class="w-1/2 h-1.5 <?php echo e($tplLines[$idx]); ?> rounded"></div>
                        <div class="w-full h-1 <?php echo e($tplLines[$idx]); ?> rounded mt-2"></div>
                        <div class="w-11/12 h-1 <?php echo e($tplLines[$idx]); ?> rounded"></div>
                        <div class="w-full h-1 <?php echo e($tplLines[$idx]); ?> rounded"></div>
                        <div class="w-9/12 h-1 <?php echo e($tplLines[$idx]); ?> rounded"></div>
                        <div class="w-full h-1 <?php echo e($tplLines[$idx]); ?> rounded mt-2"></div>
                        <div class="w-10/12 h-1 <?php echo e($tplLines[$idx]); ?> rounded"></div>
                        <div class="w-full h-1 <?php echo e($tplLines[$idx]); ?> rounded"></div>
                    </div>
                    <div class="p-3">
                        <p class="font-semibold text-sm text-slate-800"><?php echo e($tpl['name']); ?></p>
                        <p class="text-xs text-slate-400 mt-0.5"><?php echo e($tpl['cat']); ?></p>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="text-center mt-10">
                <a href="<?php echo e(route('templates.index')); ?>"
                    class="inline-flex items-center gap-2 px-6 py-3 border border-slate-300 text-slate-700 font-medium rounded-xl hover:bg-slate-50 transition text-sm">
                    Xem tất cả mẫu CV
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    
    <section class="py-24 px-6 bg-slate-50">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-14">
                <span class="section-label">Bảng giá</span>
                <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 mt-1">Miễn phí để bắt đầu</h2>
                <p class="text-slate-500 mt-3">Tạo CV chuyên nghiệp hoàn toàn miễn phí. Nâng cấp để mở khóa thêm tính năng.</p>
            </div>
            <div class="grid md:grid-cols-2 gap-5">
                
                <div class="price-free bg-white rounded-2xl p-8">
                    <div class="text-sm font-semibold text-slate-500 mb-1">Free</div>
                    <div class="text-4xl font-extrabold text-slate-900 tracking-tight mb-1">
                        0&nbsp;<span class="text-lg font-medium text-slate-400">/ tháng</span>
                    </div>
                    <p class="text-sm text-slate-500 mb-7">Mãi mãi miễn phí</p>
                    <ul class="space-y-3 mb-8">
                        <?php $__currentLoopData = ['2 CV miễn phí', 'Mẫu CV cơ bản', 'Xuất PDF', 'Chia sẻ link online']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-center gap-2.5 text-sm text-slate-700">
                            <span class="w-4 h-4 rounded-full bg-emerald-50 flex items-center justify-center shrink-0">
                                <svg class="w-2.5 h-2.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <?php echo e($item); ?>

                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <a href="<?php echo e(route('register')); ?>"
                        class="block text-center py-3 border border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 transition text-sm">
                        Bắt đầu miễn phí
                    </a>
                </div>
                
                <div class="price-pro rounded-2xl p-8 relative">
                    <div class="absolute top-5 right-5 bg-indigo-400 text-white text-xs font-bold px-2.5 py-1 rounded-full">Phổ biến</div>
                    <div class="text-sm font-semibold text-indigo-300 mb-1">Pro</div>
                    <div class="text-4xl font-extrabold text-white tracking-tight mb-1">
                        99K&nbsp;<span class="text-lg font-medium text-white/40">/ tháng</span>
                    </div>
                    <p class="text-sm text-slate-400 mb-7">Mở khóa toàn bộ tính năng</p>
                    <ul class="space-y-3 mb-8">
                        <?php $__currentLoopData = ['Không giới hạn CV', 'Tất cả mẫu CV Premium', 'Xuất PDF & PNG chất lượng cao', 'Tuỳ chỉnh màu sắc & font', 'Ưu tiên hỗ trợ']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-center gap-2.5 text-sm text-white/80">
                            <span class="w-4 h-4 rounded-full bg-indigo-500/40 flex items-center justify-center shrink-0">
                                <svg class="w-2.5 h-2.5 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <?php echo e($item); ?>

                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <a href="<?php echo e(route('register')); ?>"
                        class="block text-center py-3 bg-white text-slate-900 font-semibold rounded-xl hover:bg-slate-100 transition text-sm">
                        Dùng thử 7 ngày
                    </a>
                </div>
            </div>
        </div>
    </section>

    
    <section class="py-24 px-6">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-14">
                <span class="section-label">Đánh giá</span>
                <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 mt-1">Người dùng nói gì</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-5">
                <?php
                $testimonials = [
                    [
                        'name' => 'Nguyễn Minh Tuấn',
                        'role' => 'Software Engineer',
                        'text' => 'Tôi tạo được CV chuyên nghiệp trong 10 phút. Nhà tuyển dụng rất ấn tượng với thiết kế sạch sẽ và chuyên nghiệp.',
                        'initials' => 'NM',
                    ],
                    [
                        'name' => 'Trần Thị Hoa',
                        'role' => 'Marketing Manager',
                        'text' => 'CVactive giúp tôi thể hiện được cá tính qua CV. Tôi đã nhận được 3 offer sau khi dùng nền tảng này.',
                        'initials' => 'TH',
                    ],
                    [
                        'name' => 'Lê Văn Nam',
                        'role' => 'Fresh Graduate',
                        'text' => 'Là sinh viên mới ra trường, CVactive giúp tôi tạo CV đẹp không thua gì người có kinh nghiệm. Miễn phí nữa.',
                        'initials' => 'LN',
                    ],
                ];
                ?>
                <?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white border border-slate-100 rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold text-sm shrink-0">
                            <?php echo e($t['initials']); ?>

                        </div>
                        <div>
                            <p class="font-semibold text-sm text-slate-900"><?php echo e($t['name']); ?></p>
                            <p class="text-xs text-slate-400"><?php echo e($t['role']); ?></p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed"><?php echo e($t['text']); ?></p>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    
    <section class="hero-bg py-24 px-6">
        <div class="max-w-2xl mx-auto text-center text-white">
            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-4">Sẵn sàng tạo CV ấn tượng?</h2>
            <p class="text-slate-400 text-lg mb-10">Tham gia cùng hàng nghìn người dùng đang dùng CVactive để chinh phục nhà tuyển dụng.</p>
            <a href="<?php echo e(route('register')); ?>"
                class="inline-block px-10 py-4 bg-indigo-500 text-white font-semibold text-base rounded-xl hover:bg-indigo-400 transition shadow-xl shadow-indigo-900/40">
                Tạo CV miễn phí ngay
            </a>
        </div>
    </section>

    
    <footer class="bg-slate-900 text-slate-400 py-14 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-10">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <img src="<?php echo e(asset('storage/avatars/logo/logo.png')); ?>" style="height:28px" alt="CVactive" class="w-auto object-contain opacity-80">
                        <span class="font-bold text-white">CVactive</span>
                    </div>
                    <p class="text-sm leading-relaxed">Nền tảng tạo CV chuyên nghiệp hàng đầu Việt Nam.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-white text-sm mb-3">Sản phẩm</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?php echo e(route('templates.index')); ?>" class="hover:text-white transition">Mẫu CV</a></li>
                        <li><a href="<?php echo e(route('pricing')); ?>" class="hover:text-white transition">Bảng giá</a></li>
                        <li><a href="<?php echo e(route('cv.create')); ?>" class="hover:text-white transition">Tạo CV ngay</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white text-sm mb-3">Tài nguyên</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?php echo e(route('blog.index')); ?>" class="hover:text-white transition">Blog</a></li>
                        <li><a href="<?php echo e(route('faq')); ?>" class="hover:text-white transition">FAQ</a></li>
                        <li><a href="<?php echo e(route('contact')); ?>" class="hover:text-white transition">Liên hệ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white text-sm mb-3">Liên hệ</h4>
                    <ul class="space-y-2 text-sm">
                        <li>support@cvactive.vn</li>
                        <li>Ha Noi, Viet Nam</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-700 pt-6 text-sm text-center">
                &copy; 2026 CVactive. Tat ca quyen duoc bao luu.
            </div>
        </div>
    </footer>

</body>
</html>
<?php /**PATH C:\CLone Git\CVactive_ST5\resources\views/welcome.blade.php ENDPATH**/ ?>