

<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Edit Rekap Jam Kerja</h3>
        <p class="text-muted mb-0">Perbarui rekap manual untuk karyawan per lokasi dan bulan.</p>
    </div>
    <a href="<?php echo e(route('work-recaps.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?php echo e(route('work-recaps.update', $recap)); ?>" method="POST" class="row g-3">
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
                            <option value="<?php echo e($loc->id); ?>" <?php if(old('location_id', $recap->location_id) == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name ?? $loc->nama ?? 'Lokasi '.$loc->id); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="location_id" value="<?php echo e(auth()->user()->location_id); ?>">
            <?php endif; ?>
            <div class="col-md-4">
                <label class="form-label">Karyawan</label>
                <select name="employee_id" class="form-select" required>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>" <?php if(old('employee_id', $recap->employee_id) == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->nama ?? $emp->id); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Slot Approved (menit)</label>
                <input type="number" min="0" name="slot_minutes_approved" class="form-control" value="<?php echo e(old('slot_minutes_approved', $recap->slot_minutes_approved)); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Kehadiran (menit)</label>
                <input type="number" min="0" name="attendance_minutes" class="form-control" value="<?php echo e(old('attendance_minutes', $recap->attendance_minutes)); ?>" required>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="<?php echo e(route('work-recaps.index')); ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\work-recaps\edit.blade.php ENDPATH**/ ?>