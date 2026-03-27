<?php $__env->startSection('title', 'Việc làm - CVactive'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section with Modern Design -->
    <div class="relative bg-indigo-600 overflow-hidden">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 tracking-tight">
                    Tìm <span class="text-yellow-300">Cơ Hội</span> Nghề Nghiệp
                </h1>
                <p class="text-lg md:text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
                    Khám phá hàng trăm công việc hấp dẫn từ các công ty hàng đầu. Nơi <span class="font-semibold text-white">talent</span> gặp gỡ <span class="font-semibold text-white">opportunity</span>
                </p>
            </div>

            <!-- Search Form - Modern Card Style -->
            <div class="max-w-5xl mx-auto mt-8">
                <form method="GET" class="bg-white rounded-2xl shadow-2xl p-3">
                    <div class="flex flex-col md:flex-row gap-2">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                                placeholder="   Tìm theo tên công việc, kỹ năng, chức danh..."
                                class="w-full pl-12 pr-4 py-4 rounded-xl text-gray-900 border-0 focus:ring-2 focus:ring-indigo-500 bg-gray-50">
                        </div>
                        <div class="md:w-56 relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <input type="text" name="location" value="<?php echo e(request('location')); ?>"
                                placeholder="   Địa điểm"
                                class="w-full pl-12 pr-4 py-4 rounded-xl text-gray-900 border-0 focus:ring-2 focus:ring-indigo-500 bg-gray-50">
                        </div>
                        <button type="submit" class="px-8 py-4 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span class="hidden md:inline">Tìm kiếm</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Quick Filters - Pill Style -->
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="<?php echo e(route('jobs.index')); ?>" 
                   class="px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 <?php echo e(!request('type') ? 'bg-white text-indigo-700 shadow-md' : 'bg-white/20 text-white hover:bg-white/30'); ?>">
                    Tất cả
                </a>
                <a href="<?php echo e(route('jobs.index', ['type' => 'full-time'])); ?>" 
                   class="px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 <?php echo e(request('type') == 'full-time' ? 'bg-white text-indigo-700 shadow-md' : 'bg-white/20 text-white hover:bg-white/30'); ?>">
                    Toàn thời gian
                </a>
                <a href="<?php echo e(route('jobs.index', ['type' => 'part-time'])); ?>" 
                   class="px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 <?php echo e(request('type') == 'part-time' ? 'bg-white text-indigo-700 shadow-md' : 'bg-white/20 text-white hover:bg-white/30'); ?>">
                    Bán thời gian
                </a>
                <a href="<?php echo e(route('jobs.index', ['type' => 'intern'])); ?>" 
                   class="px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 <?php echo e(request('type') == 'intern' ? 'bg-white text-indigo-700 shadow-md' : 'bg-white/20 text-white hover:bg-white/30'); ?>">
                    Thực tập
                </a>
                <a href="<?php echo e(route('jobs.index', ['type' => 'contract'])); ?>" 
                   class="px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 <?php echo e(request('type') == 'contract' ? 'bg-white text-indigo-700 shadow-md' : 'bg-white/20 text-white hover:bg-white/30'); ?>">
                    Hợp đồng
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="bg-white border-b shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-center gap-12">
                <div class="text-center">
                    <p class="text-2xl font-bold text-indigo-600"><?php echo e($jobPosts->total()); ?></p>
                    <p class="text-sm text-gray-500">Việc làm</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-indigo-600"><?php echo e($jobPosts->count()); ?></p>
                    <p class="text-sm text-gray-500">Đang hiển thị</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Jobs List Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Section Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    <?php if(request('search') || request('location') || request('type')): ?>
                        <span class="text-indigo-600">Kết quả tìm kiếm</span>
                    <?php else: ?>
                        <span class="text-indigo-600">Tin tuyển dụng</span> mới nhất
                    <?php endif; ?>
                </h2>
                <?php if(request('search') || request('location') || request('type')): ?>
                    <p class="text-gray-500 mt-1">Tìm thấy <?php echo e($jobPosts->total()); ?> việc làm phù hợp</p>
                <?php endif; ?>
            </div>
            
            <?php if(request('search') || request('location') || request('type')): ?>
                <a href="<?php echo e(route('jobs.index')); ?>" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-medium">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Xóa bộ lọc
                </a>
            <?php endif; ?>
        </div>

        <?php if($jobPosts->count() > 0): ?>
            <!-- Jobs Grid -->
            <div class="grid gap-5">
                <?php $__currentLoopData = $jobPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jobPost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('jobs.show', $jobPost)); ?>" 
                       class="group bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:border-indigo-200 transition-all duration-300 p-6 block">
                        <div class="flex items-start gap-5">
                            <!-- Company Logo -->
                            <div class="flex-shrink-0">
                                <?php if($jobPost->company_logo): ?>
                                    <img src="<?php echo e(asset('storage/' . $jobPost->company_logo)); ?>" 
                                         alt="<?php echo e($jobPost->company_name); ?>" 
                                         class="w-16 h-16 object-contain rounded-xl shadow-sm group-hover:scale-105 transition-transform duration-300">
                                <?php else: ?>
                                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform duration-300">
                                        <svg class="w-8 h-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Job Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors truncate">
                                            <?php echo e($jobPost->title); ?>

                                        </h3>
                                        <p class="text-gray-600 font-medium mt-1">
                                            <?php echo e($jobPost->company_name ?: 'Công ty chưa cập nhật'); ?>

                                        </p>
                                    </div>
                                    <?php if($jobPost->published_at): ?>
                                        <span class="text-sm text-gray-400 whitespace-nowrap">
                                            <?php echo e($jobPost->published_at->diffForHumans()); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Tags -->
                                <div class="mt-3 flex flex-wrap items-center gap-3">
                                    <?php if($jobPost->job_type): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                                            <?php switch($jobPost->job_type):
                                                case ('full-time'): ?> Toàn thời gian <?php break; ?>
                                                <?php case ('part-time'): ?> Bán thời gian <?php break; ?>
                                                <?php case ('contract'): ?> Hợp đồng <?php break; ?>
                                                <?php case ('intern'): ?> Thực tập <?php break; ?>
                                                <?php case ('freelance'): ?> Freelance <?php break; ?>
                                                <?php default: ?> <?php echo e($jobPost->job_type); ?>

                                            <?php endswitch; ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if($jobPost->location): ?>
                                        <span class="inline-flex items-center gap-1 text-sm text-gray-500">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <?php echo e($jobPost->location); ?>

                                        </span>
                                    <?php endif; ?>

                                    <?php if($jobPost->salary_min || $jobPost->salary_max): ?>
                                        <span class="inline-flex items-center gap-1 text-sm font-semibold text-green-600">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <?php if($jobPost->salary_min && $jobPost->salary_max): ?>
                                                <?php echo e(number_format($jobPost->salary_min)); ?> - <?php echo e(number_format($jobPost->salary_max)); ?> <?php echo e($jobPost->salary_currency); ?>

                                            <?php elseif($jobPost->salary_min): ?>
                                                Từ <?php echo e(number_format($jobPost->salary_min)); ?> <?php echo e($jobPost->salary_currency); ?>

                                            <?php else: ?>
                                                Up to <?php echo e(number_format($jobPost->salary_max)); ?> <?php echo e($jobPost->salary_currency); ?>

                                            <?php endif; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Arrow Indicator -->
                            <div class="hidden md:flex items-center justify-center w-10 h-10 rounded-full bg-gray-50 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-all duration-300">
                                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Pagination -->
            <div class="mt-10 flex justify-center">
                <nav class="flex items-center gap-2">
                    <?php echo e($jobPosts->links('pagination::tailwind')); ?>

                </nav>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Không tìm thấy công việc nào</h3>
                <p class="text-gray-500 mb-6">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc để tìm kiếm công việc phù hợp</p>
                <a href="<?php echo e(route('jobs.index')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Xem tất cả việc làm
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\CLone Git\CVactive_ST5\resources\views/jobs/index.blade.php ENDPATH**/ ?>