<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Pengguna</h3>
        <p class="text-muted mb-0">Kelola akun pengguna, peran, dan keterkaitan karyawan.</p>
    </div>
    <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary btn-sm">Tambah User</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Employees</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th style="width:50px">No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Employee</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($loop->iteration + ($employees->currentPage()-1)*$employees->perPage()); ?></td>
                                <td><?php echo e($employee->name); ?></td>
                                <td><?php echo e($employee->email); ?></td>
                                <td>
                                  <?php ($roles = $employee->getRoleNames()); ?>
                                  <?php if($roles->isNotEmpty()): ?>
                                    <span class="badge text-bg-secondary"><?php echo e($roles->implode(', ')); ?></span>
                                  <?php else: ?>
                                    <span class="badge text-bg-light">-</span>
                                  <?php endif; ?>
                                </td>
                                <td><?php echo e($employee->employee ? $employee->employee->nama : '-'); ?></td>
                                <td><?php echo e($employee->location ? $employee->location->name : '-'); ?></td>
                                <td>
                                    <a href="<?php echo e(route('users.show', $employee)); ?>" class="btn btn-sm btn-outline-info">View</a>
                                    <a href="<?php echo e(route('users.edit', $employee)); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="<?php echo e(route('users.destroy', $employee)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\users\index.blade.php ENDPATH**/ ?>