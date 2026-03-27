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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Câu hỏi thường gặp</h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-900">Câu hỏi thường gặp</h1>
                <p class="text-gray-500 mt-2">Không tìm thấy câu trả lời? <a href="<?php echo e(route('contact')); ?>" class="text-indigo-600 hover:underline">Liên hệ chúng tôi</a></p>
            </div>

            <div class="space-y-4" x-data="{open: null}">
                <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <button @click="open === <?php echo e($faq->id); ?> ? open = null : open = <?php echo e($faq->id); ?>"
                        class="w-full flex items-center justify-between px-6 py-4 text-left">
                        <span class="font-semibold text-gray-900"><?php echo e($faq->question); ?></span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform" :class="open === <?php echo e($faq->id); ?> ? 'rotate-180' : ''"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === <?php echo e($faq->id); ?>" x-collapse class="px-6 pb-4">
                        <p class="text-gray-600 text-sm leading-relaxed"><?php echo e($faq->answer); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
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
<?php /**PATH C:\CLone Git\CVactive_ST5\resources\views/faq.blade.php ENDPATH**/ ?>