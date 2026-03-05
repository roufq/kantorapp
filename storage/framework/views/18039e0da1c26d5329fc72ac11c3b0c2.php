<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Histori Jabatan</h3>
        <p class="text-muted mb-0">Riwayat jabatan & departemen karyawan.</p>
    </div>
    <a href="<?php echo e(route('employee-positions.create')); ?>" class="btn btn-primary btn-sm">Tambah Histori</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Karyawan</label>
                <select name="employee_id" class="form-select">
                    <option value="">Semua</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>" <?php if(request('employee_id') == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">Filter</button>
                <a href="<?php echo e(route('employee-positions.index')); ?>" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">Daftar Histori</h3></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Karyawan</th>
                    <th>Jabatan</th>
                    <th>Departemen</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration + ($histories->currentPage()-1)*$histories->perPage()); ?></td>
                        <td><?php echo e($history->employee?->nama ?? '-'); ?></td>
                        <td><?php echo e($history->title); ?></td>
                        <td><?php echo e($history->department ?? '-'); ?></td>
                        <td><?php echo e($history->start_date?->format('Y-m-d') ?? '-'); ?></td>
                        <td><?php echo e($history->end_date?->format('Y-m-d') ?? '-'); ?></td>
                        <td>
                            <a href="<?php echo e(route('employee-positions.edit', $history)); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="<?php echo e(route('employee-positions.destroy', $history)); ?>" method="POST" style="display:inline" onsubmit="return confirm('Hapus histori ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center text-muted">Belum ada histori.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer"><?php echo e($histories->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\employee-positions\index.blade.php ENDPATH**/ ?>