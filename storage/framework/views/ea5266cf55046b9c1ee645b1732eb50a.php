<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Edit Jobdesk</h3>
        <p class="text-muted mb-0"><?php echo e($jobdesk->name); ?></p>
    </div>
    <a href="<?php echo e(route('jobdesks.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('jobdesks.update', $jobdesk)); ?>" class="row g-3">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="col-md-6">
                <label class="form-label">Nama Jobdesk</label>
                <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $jobdesk->name)); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Role Scope (opsional)</label>
                <input type="text" name="role_scope" class="form-control" value="<?php echo e(old('role_scope', $jobdesk->role_scope)); ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3"><?php echo e(old('description', $jobdesk->description)); ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Lokasi</label>
                <select name="location_id" class="form-select">
                    <option value="">Global (Semua Lokasi)</option>
                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($loc->id); ?>" <?php if(old('location_id', $jobdesk->location_id) == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name ?? $loc->nama ?? 'Lokasi '.$loc->id); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Minimal Jam Hadir (menit, non-shift)</label>
                <input type="number" name="min_attendance_minutes" class="form-control" min="0" value="<?php echo e(old('min_attendance_minutes', $jobdesk->min_attendance_minutes)); ?>" placeholder="Contoh: 360 untuk 6 jam">
            </div>
            <div class="col-md-3 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" <?php if(old('is_active', $jobdesk->is_active)): echo 'checked'; endif; ?>>
                    <label class="form-check-label" for="isActive">Aktif</label>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?php echo e(route('jobdesks.index')); ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\jobdesks\edit.blade.php ENDPATH**/ ?>