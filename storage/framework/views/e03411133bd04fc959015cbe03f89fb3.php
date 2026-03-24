<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Dashboard')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">CV của tôi</h3>
                    <p class="text-sm text-gray-500 mt-1">Quản lý và tạo CV xin việc chuyên nghiệp</p>
                </div>
                <a href="<?php echo e(route('cv.create')); ?>" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wider hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tạo CV mới
                </a>
            </div>

            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Tổng số CV</p>
                                <p class="text-2xl font-bold text-gray-900"><?php echo e($cvs->count()); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">CV đã hoàn thành</p>
                                <p class="text-2xl font-bold text-gray-900"><?php echo e($cvs->where('is_draft', false)->count()); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">CV nháp</p>
                                <p class="text-2xl font-bold text-gray-900"><?php echo e($cvs->where('is_draft', true)->count()); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if($cvs->isEmpty()): ?>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center text-gray-500">
                        <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Chưa có CV nào</h3>
                        <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">Bắt đầu tạo CV chuyên nghiệp để gây ấn tượng với nhà tuyển dụng.</p>
                        <div class="mt-6">
                            <a href="<?php echo e(route('cv.create')); ?>" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg">
                                Tạo CV đầu tiên
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php $__currentLoopData = $cvs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                            
                            <div class="h-52 bg-gray-100 border-b relative overflow-hidden">
                                <?php if($cv->template && $cv->template->thumbnail): ?>
                                    <img src="<?php echo e($cv->template->thumbnail); ?>" alt="<?php echo e($cv->title); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
                                        <svg class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                
                                
                                <?php if($cv->is_draft): ?>
                                    <span class="absolute top-3 right-3 bg-yellow-100 text-yellow-800 text-xs px-2.5 py-1 rounded-full font-semibold">Nháp</span>
                                <?php else: ?>
                                    <span class="absolute top-3 right-3 bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-semibold">Hoàn thành</span>
                                <?php endif; ?>

                                
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center space-x-3">
                                    <a href="<?php echo e(route('cv.edit', $cv)); ?>" class="bg-white text-gray-800 p-2.5 rounded-full hover:bg-indigo-600 hover:text-white transition-colors" title="Chỉnh sửa">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <a href="<?php echo e(route('cv.pdf', $cv)); ?>" target="_blank" class="bg-white text-gray-800 p-2.5 rounded-full hover:bg-red-600 hover:text-white transition-colors" title="Tải PDF">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </a>
                                    <?php if($cv->share_url ?? null): ?>
                                    <a href="<?php echo e($cv->share_url); ?>" target="_blank" class="bg-white text-gray-800 p-2.5 rounded-full hover:bg-blue-600 hover:text-white transition-colors" title="Xem link chia sẻ">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            
                            <div class="p-5">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-lg text-gray-900 truncate" title="<?php echo e($cv->title); ?>"><?php echo e($cv->title); ?></h4>
                                        <p class="text-sm text-gray-500 mt-1">
                                            <span class="inline-flex items-center">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                                                </svg>
                                                <?php echo e($cv->template->name ?? 'Unknown'); ?>

                                            </span>
                                        </p>
                                        <p class="text-xs text-gray-400 mt-2">Cập nhật <?php echo e($cv->updated_at->diffForHumans()); ?></p>
                                    </div>
                                    
                                    
                                    <div class="flex flex-col space-y-2 ml-3">
                                        <a href="<?php echo e(route('cv.edit', $cv)); ?>" class="bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-lg text-sm font-medium text-center transition shadow-sm hover:shadow">
                                            Sửa
                                        </a>
                                        <div class="flex space-x-1">
                                            <a href="<?php echo e(route('cv.pdf', $cv)); ?>" target="_blank" class="flex-1 bg-gray-50 border border-gray-200 text-gray-600 hover:bg-gray-100 p-2 rounded-lg text-center transition" title="Tải PDF">
                                                <svg class="w-4 h-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </a>
                                            <?php if($cv->share_url ?? null): ?>
                                            <a href="<?php echo e($cv->share_url); ?>" target="_blank" class="flex-1 bg-gray-50 border border-gray-200 text-gray-600 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 p-2 rounded-lg text-center transition" title="Xem link chia sẻ">
                                                <svg class="w-4 h-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <?php endif; ?>
                                            <form action="<?php echo e(route('cv.destroy', $cv)); ?>" method="POST" class="flex-1">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="w-full bg-gray-50 border border-gray-200 text-gray-600 hover:bg-red-50 hover:border-red-200 hover:text-red-600 p-2 rounded-lg text-center transition" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa CV này?');">
                                                    <svg class="w-4 h-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php if(!($cv->share_url ?? null)): ?>
                                <p class="text-xs text-gray-400 mt-3 pt-3 border-t border-gray-100">Tạo link chia sẻ trong màn hình Sửa CV.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\CLone Git\CVactive_ST5\resources\views/dashboard.blade.php ENDPATH**/ ?>