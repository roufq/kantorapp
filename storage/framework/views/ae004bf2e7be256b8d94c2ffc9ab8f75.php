<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Tambah Task Catalog</h3>
        <p class="text-muted mb-0">Jobdesk: <?php echo e($jobdesk->name); ?></p>
    </div>
    <a href="<?php echo e(route('jobdesks.catalogs.index', $jobdesk)); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('jobdesks.catalogs.store', $jobdesk)); ?>" class="row g-3">
            <?php echo csrf_field(); ?>
            <div class="col-md-6">
                <label class="form-label">Nama Task</label>
                <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Unit</label>
                <select name="unit" class="form-select">
                    <option value="points" <?php if(old('unit') === 'points'): echo 'selected'; endif; ?>>Points</option>
                    <option value="minutes" <?php if(old('unit') === 'minutes'): echo 'selected'; endif; ?>>Minutes</option>
                    <option value="weight" <?php if(old('unit') === 'weight'): echo 'selected'; endif; ?>>Weight</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Nilai</label>
                <input type="number" min="1" name="value" class="form-control" value="<?php echo e(old('value', 1)); ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3"><?php echo e(old('description')); ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tipe</label>
                <select name="task_type" class="form-select">
                    <option value="routine" <?php if(old('task_type') === 'routine'): echo 'selected'; endif; ?>>Routine</option>
                    <option value="project" <?php if(old('task_type') === 'project'): echo 'selected'; endif; ?>>Project</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" checked>
                    <label class="form-check-label" for="isActive">Aktif</label>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?php echo e(route('jobdesks.catalogs.index', $jobdesk)); ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\task-catalogs\create.blade.php ENDPATH**/ ?>