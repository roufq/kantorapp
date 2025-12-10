<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['queries' => []]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['queries' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $queries = array_slice($queries ?? [], 0, 100);
?>

<div <?php echo e($attributes->merge(['class' => 'flex flex-col gap-2.5 bg-neutral-50 dark:bg-white/1 border border-neutral-200 dark:border-neutral-800 rounded-xl p-2.5 shadow-xs'])); ?>>
    <div class="flex items-center justify-between p-2">
        <div class="flex items-center gap-2.5">
            <h3 class="text-base font-semibold">Queries</h3>
        </div>
        <div class="text-sm text-neutral-500 dark:text-neutral-400">
            <?php echo e(count($queries)); ?> shown (max 100)
        </div>
    </div>

    <div class="flex flex-col gap-1">
        <?php $__empty_1 = true; $__currentLoopData = $queries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $query): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="border border-neutral-200 dark:border-none bg-white dark:bg-white/[3%] rounded-md p-2 text-xs font-mono shadow-xs">
            <div class="flex justify-between gap-2">
                <div class="min-w-0">
                    <span class="text-neutral-500 dark:text-neutral-400"><?php echo e($query['connectionName'] ?? ''); ?></span>
                    <span class="text-neutral-700 dark:text-neutral-200 break-all"><?php echo $query['sql'] ?? ''; ?></span>
                </div>
                <div class="text-neutral-500 dark:text-neutral-200 flex-shrink-0"><?php echo e($query['time'] ?? ''); ?> ms</div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-muted">No queries executed</div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH D:\www\kantorapp\vendor\laravel\framework\src\Illuminate\Foundation\Providers/../resources/exceptions/renderer/components/query.blade.php ENDPATH**/ ?>