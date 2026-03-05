<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Tambah Mutasi</h3>
        <p class="text-muted mb-0">Catat perpindahan lokasi karyawan.</p>
    </div>
    <a href="<?php echo e(route('employee-transfers.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('employee-transfers.store')); ?>" class="row g-3">
            <?php echo csrf_field(); ?>
            <div class="col-md-6">
                <label class="form-label">Karyawan</label>
                <select name="employee_id" class="form-select" required>
                    <option value="" disabled selected>-- Pilih Karyawan --</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>" <?php if(old('employee_id') == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Dari Lokasi</label>
                <select name="from_location_id" class="form-select">
                    <option value="">-</option>
                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($loc->id); ?>" <?php if(old('from_location_id') == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name ?? $loc->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Ke Lokasi</label>
                <select name="to_location_id" class="form-select">
                    <option value="">-</option>
                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($loc->id); ?>" <?php if(old('to_location_id') == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name ?? $loc->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Efektif</label>
                <input type="date" name="effective_date" class="form-control" value="<?php echo e(old('effective_date')); ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Alasan</label>
                <textarea name="reason" class="form-control" rows="3"><?php echo e(old('reason')); ?></textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Simpan</button>
                <a href="<?php echo e(route('employee-transfers.index')); ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\employee-transfers\create.blade.php ENDPATH**/ ?>