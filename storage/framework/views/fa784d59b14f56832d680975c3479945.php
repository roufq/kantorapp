<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h4 mb-1">Target Jam Kerja per Lokasi</h1>
            <p class="text-muted mb-0">Tetapkan target menit kerja per lokasi dan per karyawan (opsional) per bulan.</p>
        </div>
        <div>
            <a href="<?php echo e(route('work-targets.create')); ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Tambah Target</a>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Bulan</label>
                <input type="month" name="month" value="<?php echo e($monthParam); ?>" class="form-control">
            </div>
            <?php if(auth()->user()->hasRole('Super Admin')): ?>
                <div class="col-md-3">
                    <label class="form-label">Lokasi</label>
                    <select name="location_id" class="form-select">
                        <option value="">Semua</option>
                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($loc->id); ?>" <?php if(request('location_id') == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name ?? $loc->nama ?? 'Lokasi '.$loc->id); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="col-md-3">
                <label class="form-label">Karyawan</label>
                <select name="employee_id" class="form-select">
                    <option value="">Semua</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>" <?php if(request('employee_id') == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->nama ?? $emp->id); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Terapkan</button>
                <a href="<?php echo e(route('work-targets.index')); ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Daftar Target</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Lokasi</th>
                        <th>Karyawan (opsional)</th>
                        <th>Bulan</th>
                        <th>Target (menit)</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $targets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($target->location->name ?? $target->location->nama ?? 'Lokasi #'.$target->location_id); ?></td>
                            <td><?php echo e($target->employee?->nama ?? '-'); ?></td>
                            <td><?php echo e(sprintf('%02d', $target->month)); ?>-<?php echo e($target->year); ?></td>
                            <td><?php echo e($target->target_minutes); ?></td>
                            <td class="text-muted small"><?php echo e(optional($target->created_at)->format('d M Y H:i')); ?></td>
                            <td class="d-flex gap-2">
                                <a href="<?php echo e(route('work-targets.edit', $target)); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="<?php echo e(route('work-targets.destroy', $target)); ?>" method="POST" onsubmit="return confirm('Hapus target ini?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada target untuk filter ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($targets->hasPages()): ?>
        <div class="card-footer d-flex justify-content-center">
            <?php echo e($targets->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/work-targets/index.blade.php ENDPATH**/ ?>