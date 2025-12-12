<?php $__env->startSection('title'); ?>
<div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">User</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">User</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Employees</h3>
                <div class="card-tools">
                    <a href="<?php echo e(route('users.create')); ?>" class="btn btn-sm btn-primary">Add Employee</a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
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
                                <td><?php echo e($loop->iteration); ?></td>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/users/index.blade.php ENDPATH**/ ?>