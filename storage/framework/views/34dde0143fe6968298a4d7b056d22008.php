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
            <?php echo e(__('CV Templates')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Choose a Template for Your Next CV</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Stand out from the crowd with our professionally designed CV templates. Perfect for any industry or career level.</p>
            </div>

            <!-- Categories Filter Placeholder -->
            <?php if($categories->count() > 0): ?>
            <div class="flex justify-center space-x-4 mb-8 overflow-x-auto pb-2">
                <a href="<?php echo e(route('templates.index')); ?>" class="px-4 py-2 rounded-full border font-medium whitespace-nowrap <?php echo e(request('category') ? 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' : 'border-indigo-600 bg-indigo-50 text-indigo-700'); ?>">Tất cả</a>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('templates.index', ['category' => $category->slug])); ?>" class="px-4 py-2 rounded-full border font-medium whitespace-nowrap <?php echo e(request('category') === $category->slug ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'); ?>"><?php echo e($category->name); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            <?php if($templates->isEmpty()): ?>
                <div class="bg-white rounded-lg shadow p-12 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="text-lg">No templates available yet! The admin will add some soon.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-200 flex flex-col h-full relative transform hover:-translate-y-1">
                            
                            <?php if($template->is_premium): ?>
                                <div class="absolute top-3 right-3 z-10">
                                    <span class="bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm flex items-center space-x-1">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        <span>PRO</span>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <div class="relative w-full aspect-[21/29.7] bg-gray-100 overflow-hidden border-b border-gray-100 rounded-t-xl">
                                <?php if($template->thumbnail): ?>
                                    <img src="<?php echo e($template->thumbnail); ?>" alt="<?php echo e($template->name); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50 pattern-dots">
                                        <svg class="w-16 h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Overlay on hover -->
                                <div class="absolute inset-0 bg-gray-900/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center space-y-4 p-4 z-20">
                                    <form action="<?php echo e(route('cv.store')); ?>" method="POST" class="w-full text-center">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="template_id" value="<?php echo e($template->id); ?>">
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg w-full max-w-[180px] font-bold py-3 px-4 rounded-lg transform transition active:scale-95">
                                            Use Template
                                        </button>
                                    </form>
                                    <a href="<?php echo e(route('templates.preview', $template)); ?>" target="_blank" class="bg-white/10 hover:bg-white/20 border border-white/50 text-white backdrop-blur-sm w-full max-w-[180px] font-semibold py-2 px-4 rounded-lg transform transition active:scale-95 text-center">
                                        Xem trước
                                    </a>
                                </div>
                            </div>
                            
                            <div class="p-5 bg-white">
                                <h3 class="text-xl font-bold text-gray-900 mb-1 truncate"><?php echo e($template->name); ?></h3>
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <span><?php echo e($template->category ? $template->category->name : 'General'); ?></span>
                                    <span class="flex items-center">
                                        <svg class="h-4 w-4 text-gray-400 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <?php echo e(number_format($template->usage_count)); ?>

                                    </span>
                                </div>
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
<?php /**PATH C:\CLone Git\CVactive_ST5\resources\views/templates/index.blade.php ENDPATH**/ ?>