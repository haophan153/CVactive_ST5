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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Blog</h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-900">Blog CVactive</h1>
                <p class="text-gray-500 mt-2">Kiến thức & kinh nghiệm tìm việc, viết CV từ các chuyên gia</p>
            </div>

            
            <?php if($categories->count()): ?>
            <div class="flex flex-wrap gap-2 mb-8 justify-center">
                <a href="<?php echo e(route('blog.index')); ?>" class="px-4 py-1.5 rounded-full text-sm font-medium <?php echo e(!request('category') ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'); ?> transition">Tất cả</a>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('blog.index', ['category' => $cat->slug])); ?>"
                    class="px-4 py-1.5 rounded-full text-sm font-medium <?php echo e(request('category') === $cat->slug ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'); ?> transition">
                    <?php echo e($cat->name); ?> (<?php echo e($cat->posts_count); ?>)
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            <?php if($posts->isEmpty()): ?>
            <div class="text-center py-16 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                <p>Chưa có bài viết nào.</p>
            </div>
            <?php else: ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition group">
                    <?php if($post->featured_image): ?>
                    <div class="aspect-video overflow-hidden">
                        <img src="<?php echo e(asset('storage/'.$post->featured_image)); ?>" alt="<?php echo e($post->title); ?>"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <?php else: ?>
                    <div class="aspect-video bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                        <svg class="w-10 h-10 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    </div>
                    <?php endif; ?>
                    <div class="p-5">
                        <?php if($post->category): ?>
                        <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full"><?php echo e($post->category->name); ?></span>
                        <?php endif; ?>
                        <h2 class="text-base font-bold text-gray-900 mt-3 mb-2 line-clamp-2">
                            <a href="<?php echo e(route('blog.show', $post->slug)); ?>" class="hover:text-indigo-600 transition"><?php echo e($post->title); ?></a>
                        </h2>
                        <?php if($post->excerpt): ?>
                        <p class="text-sm text-gray-500 line-clamp-2"><?php echo e($post->excerpt); ?></p>
                        <?php endif; ?>
                        <div class="flex items-center justify-between mt-4 text-xs text-gray-400">
                            <span><?php echo e($post->author->name); ?></span>
                            <span><?php echo e($post->published_at?->diffForHumans()); ?></span>
                        </div>
                    </div>
                </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="mt-8"><?php echo e($posts->links()); ?></div>
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
<?php /**PATH C:\CLone Git\CVactive_ST5\resources\views/blog/index.blade.php ENDPATH**/ ?>