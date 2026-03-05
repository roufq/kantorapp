<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Edit Histori Jabatan</h3>
        <p class="text-muted mb-0"><?php echo e($employee_position->employee?->nama ?? '-'); ?></p>
    </div>
    <a href="<?php echo e(route('employee-positions.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('employee-positions.update', $employee_position)); ?>" class="row g-3">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="col-md-6">
                <label class="form-label">Karyawan</label>
                <select name="employee_id" class="form-select" required>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>" <?php if(old('employee_id', $employee_position->employee_id) == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Jabatan</label>
                <input type="text" name="title" class="form-control" value="<?php echo e(old('title', $employee_position->title)); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Departemen</label>
                <input type="text" name="department" class="form-control" value="<?php echo e(old('department', $employee_position->department)); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Mulai</label>
                <input type="date" name="start_date" class="form-control" value="<?php echo e(old('start_date', optional($employee_position->start_date)->format('Y-m-d'))); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Selesai</label>
                <input type="date" name="end_date" class="form-control" value="<?php echo e(old('end_date', optional($employee_position->end_date)->format('Y-m-d'))); ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="3"><?php echo e(old('notes', $employee_position->notes)); ?></textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Simpan</button>
                <a href="<?php echo e(route('employee-positions.index')); ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\employee-positions\edit.blade.php ENDPATH**/ ?>