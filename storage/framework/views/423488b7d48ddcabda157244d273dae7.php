<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Tambah Target Output</h3>
        <p class="text-muted mb-0">Tentukan target output per jobdesk/karyawan per bulan.</p>
    </div>
    <a href="<?php echo e(route('jobdesk-targets.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('jobdesk-targets.store')); ?>" class="row g-3">
            <?php echo csrf_field(); ?>
            <div class="col-md-6">
                <label class="form-label">Jobdesk</label>
                <select name="jobdesk_id" class="form-select" required>
                    <option value="" disabled selected>-- Pilih Jobdesk --</option>
                    <?php $__currentLoopData = $jobdesks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jobdesk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($jobdesk->id); ?>" <?php if(old('jobdesk_id') == $jobdesk->id): echo 'selected'; endif; ?>><?php echo e($jobdesk->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Karyawan (opsional)</label>
                <select name="employee_id" class="form-select">
                    <option value="">Semua Karyawan</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>" <?php if(old('employee_id') == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Bulan</label>
                <input type="month" name="month" class="form-control" value="<?php echo e(old('month', $monthParam)); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Unit</label>
                <select name="unit" class="form-select" required>
                    <option value="points" <?php if(old('unit') === 'points'): echo 'selected'; endif; ?>>Points</option>
                    <option value="minutes" <?php if(old('unit') === 'minutes'): echo 'selected'; endif; ?>>Minutes</option>
                    <option value="weight" <?php if(old('unit') === 'weight'): echo 'selected'; endif; ?>>Weight</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Target</label>
                <input type="number" name="target_value" class="form-control" min="0" value="<?php echo e(old('target_value', 0)); ?>" required>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Simpan</button>
                <a href="<?php echo e(route('jobdesk-targets.index')); ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\jobdesk-targets\create.blade.php ENDPATH**/ ?>