<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Jobdesk</h3>
        <p class="text-muted mb-0">Kelola jobdesk dan scope tugas per perusahaan/lokasi.</p>
    </div>
    <a href="<?php echo e(route('jobdesks.create')); ?>" class="btn btn-primary btn-sm">Tambah Jobdesk</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Lokasi</label>
                <select name="location_id" class="form-select">
                    <option value="">Semua Lokasi</option>
                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($loc->id); ?>" <?php if(request('location_id') == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name ?? $loc->nama ?? 'Lokasi '.$loc->id); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Aktif</option>
                    <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="<?php echo e(route('jobdesks.index')); ?>" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Jobdesk</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th style="width:50px">No</th>
                            <th>Nama</th>
                            <th>Lokasi</th>
                            <th>Role Scope</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $jobdesks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jobdesk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($loop->iteration + ($jobdesks->currentPage()-1)*$jobdesks->perPage()); ?></td>
                                <td><?php echo e($jobdesk->name); ?></td>
                                <td><?php echo e($jobdesk->location?->name ?? $jobdesk->location?->nama ?? 'Global'); ?></td>
                                <td><?php echo e($jobdesk->role_scope ?? '-'); ?></td>
                                <td>
                                    <span class="badge <?php echo e($jobdesk->is_active ? 'badge-success' : 'badge-secondary'); ?>">
                                        <?php echo e($jobdesk->is_active ? 'Aktif' : 'Nonaktif'); ?>

                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('jobdesks.show', $jobdesk)); ?>" class="btn btn-sm btn-outline-info">View</a>
                                    <a href="<?php echo e(route('jobdesks.edit', $jobdesk)); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="<?php echo e(route('jobdesks.catalogs.index', $jobdesk)); ?>" class="btn btn-sm btn-outline-secondary">Task Catalog</a>
                                    <a href="<?php echo e(route('jobdesks.assignments.index', $jobdesk)); ?>" class="btn btn-sm btn-outline-success">Assignments</a>
                                    <form action="<?php echo e(route('jobdesks.destroy', $jobdesk)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Hapus jobdesk ini?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada jobdesk.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <?php echo e($jobdesks->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\jobdesks\index.blade.php ENDPATH**/ ?>