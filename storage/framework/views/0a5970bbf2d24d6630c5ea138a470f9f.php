<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Detail Jobdesk</h3>
        <p class="text-muted mb-0"><?php echo e($jobdesk->name); ?></p>
    </div>
    <a href="<?php echo e(route('jobdesks.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <p><strong>Nama:</strong> <?php echo e($jobdesk->name); ?></p>
        <p><strong>Deskripsi:</strong> <?php echo e($jobdesk->description ?? '-'); ?></p>
        <p><strong>Role Scope:</strong> <?php echo e($jobdesk->role_scope ?? '-'); ?></p>
        <p><strong>Lokasi:</strong> <?php echo e($jobdesk->location?->name ?? $jobdesk->location?->nama ?? 'Global'); ?></p>
        <p><strong>Status:</strong> <?php echo e($jobdesk->is_active ? 'Aktif' : 'Nonaktif'); ?></p>
        <p><strong>Minimal Hadir (menit):</strong> <?php echo e($jobdesk->min_attendance_minutes ?? '-'); ?></p>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Task Catalog</h3>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('jobdesks.assignments.index', $jobdesk)); ?>" class="btn btn-sm btn-outline-success">Assignments</a>
            <a href="<?php echo e(route('jobdesks.catalogs.index', $jobdesk)); ?>" class="btn btn-sm btn-outline-primary">Kelola Catalog</a>
        </div>
    </div>
    <div class="card-body">
        <?php if($jobdesk->taskCatalogs->isEmpty()): ?>
            <div class="text-muted">Belum ada task catalog.</div>
        <?php else: ?>
            <ul class="mb-0">
                <?php $__currentLoopData = $jobdesk->taskCatalogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catalog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($catalog->name); ?> (<?php echo e($catalog->unit); ?> <?php echo e($catalog->value); ?>)</li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\jobdesks\show.blade.php ENDPATH**/ ?>