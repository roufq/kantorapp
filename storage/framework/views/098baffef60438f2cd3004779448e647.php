

<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Detail Divisi</h3>
        <p class="text-muted mb-0"><?php echo e($division->nama); ?></p>
    </div>
    <a href="<?php echo e(route('divisions.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Division Details</h3>
                <div class="card-tools">
                    <a href="<?php echo e(route('divisions.edit', $division)); ?>" class="btn btn-sm btn-primary">Edit</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> <?php echo e($division->id); ?></p>
                        <p><strong>Nama:</strong> <?php echo e($division->nama); ?></p>
                        <p><strong>Created:</strong> <?php echo e($division->created_at->format('d M Y H:i')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\divisions\show.blade.php ENDPATH**/ ?>