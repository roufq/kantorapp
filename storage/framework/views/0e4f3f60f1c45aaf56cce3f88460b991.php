<?php use Carbon\Carbon; ?>


<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h4 mb-1">Rekap Jam Kerja Bulanan</h1>
            <p class="text-muted mb-0">Total menit dari slot tugas yang disetujui + kehadiran (jika ada).</p>
        </div>
        <div>
            <a href="<?php echo e(route('tasks.index')); ?>" class="text-decoration-none">Kembali ke Tasks</a>
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
                <a href="<?php echo e(route('work-recaps.index')); ?>" class="btn btn-outline-secondary">Reset</a>
                <a href="<?php echo e(route('work-recaps.create')); ?>" class="btn btn-outline-primary ms-auto">Tambah Rekap</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Hasil Rekap</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Karyawan</th>
                        <th>Lokasi</th>
                        <th>Bulan</th>
                        <th>Slot Approved (menit)</th>
                        <th>Kehadiran (menit)</th>
                        <th>Total (menit)</th>
                        <th>Update</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recaps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($recap->employee->nama ?? 'Emp #'.$recap->employee_id); ?></td>
                            <td><?php echo e($recap->location->name ?? $recap->location->nama ?? 'Lokasi #'.$recap->location_id); ?></td>
                            <td><?php echo e(sprintf('%02d', $recap->month)); ?>-<?php echo e($recap->year); ?></td>
                            <td><?php echo e($recap->slot_minutes_approved); ?></td>
                            <td><?php echo e($recap->attendance_minutes); ?></td>
                            <td><strong><?php echo e($recap->total_minutes); ?></strong></td>
                            <td class="text-muted small"><?php echo e(optional($recap->updated_at)->format('d M Y H:i')); ?></td>
                            <td class="d-flex gap-2">
                                <a href="<?php echo e(route('work-recaps.edit', $recap)); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="<?php echo e(route('work-recaps.destroy', $recap)); ?>" method="POST" onsubmit="return confirm('Hapus rekap ini?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada data untuk filter ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($recaps->hasPages()): ?>
        <div class="card-footer d-flex justify-content-center">
            <?php echo e($recaps->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/work-recaps/index.blade.php ENDPATH**/ ?>