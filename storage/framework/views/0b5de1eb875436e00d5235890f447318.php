<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Kontrak Kerja</h3>
        <p class="text-muted mb-0">Riwayat kontrak karyawan.</p>
    </div>
    <a href="<?php echo e(route('employee-contracts.create')); ?>" class="btn btn-primary btn-sm">Tambah Kontrak</a>
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
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Active</option>
                    <option value="ended" <?php if(request('status') === 'ended'): echo 'selected'; endif; ?>>Ended</option>
                    <option value="terminated" <?php if(request('status') === 'terminated'): echo 'selected'; endif; ?>>Terminated</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">Filter</button>
                <a href="<?php echo e(route('employee-contracts.index')); ?>" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">Daftar Kontrak</h3></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Karyawan</th>
                    <th>Jenis</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $contracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contract): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration + ($contracts->currentPage()-1)*$contracts->perPage()); ?></td>
                        <td><?php echo e($contract->employee?->nama ?? '-'); ?></td>
                        <td><?php echo e($contract->contract_type); ?></td>
                        <td><?php echo e($contract->start_date?->format('Y-m-d') ?? '-'); ?></td>
                        <td><?php echo e($contract->end_date?->format('Y-m-d') ?? '-'); ?></td>
                        <td><?php echo e(ucfirst($contract->status)); ?></td>
                        <td>
                            <a href="<?php echo e(route('employee-contracts.edit', $contract)); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="<?php echo e(route('employee-contracts.destroy', $contract)); ?>" method="POST" style="display:inline" onsubmit="return confirm('Hapus kontrak ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center text-muted">Belum ada kontrak.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer"><?php echo e($contracts->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/employee-contracts/index.blade.php ENDPATH**/ ?>