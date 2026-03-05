

<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Edit Target Jam Kerja</h3>
        <p class="text-muted mb-0">Perbarui menit kerja per lokasi (opsional per karyawan) untuk bulan tertentu.</p>
    </div>
    <a href="<?php echo e(route('work-targets.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?php echo e(route('work-targets.update', $target)); ?>" method="POST" class="row g-3">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="col-md-4">
                <label class="form-label">Bulan</label>
                <input type="month" name="month" class="form-control" value="<?php echo e(old('month', $monthParam)); ?>" required>
            </div>
            <?php if(auth()->user()->hasRole('Super Admin')): ?>
                <div class="col-md-4">
                    <label class="form-label">Lokasi</label>
                    <select name="location_id" class="form-select" required>
                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($loc->id); ?>" <?php if(old('location_id', $target->location_id) == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name ?? $loc->nama ?? 'Lokasi '.$loc->id); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="location_id" value="<?php echo e(auth()->user()->location_id); ?>">
            <?php endif; ?>
            <div class="col-md-4">
                <label class="form-label">Karyawan (opsional)</label>
                <select name="employee_id" class="form-select">
                    <option value="">Semua karyawan</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>" <?php if(old('employee_id', $target->employee_id) == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->nama ?? $emp->id); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Target (menit)</label>
                <input type="number" min="0" name="target_minutes" class="form-control" value="<?php echo e(old('target_minutes', $target->target_minutes)); ?>" required>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="<?php echo e(route('work-targets.index')); ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\work-targets\edit.blade.php ENDPATH**/ ?>