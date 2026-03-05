<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
  <div>
    <h3 class="mb-1">Admin Lokasi</h3>
    <p class="text-muted mb-0">Kelola akun admin lokasi dan penempatannya.</p>
  </div>
  <a href="<?php echo e(route('location-admins.create')); ?>" class="btn btn-primary btn-sm">Tambah Admin</a>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Admins</h3>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead>
            <tr>
              <th>NO</th>
              <th>Name</th>
              <th>Email</th>
              <th>Location</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr>
                <td><?php echo e($loop->iteration + ($admins->currentPage()-1)*$admins->perPage()); ?></td>
                <td><?php echo e($admin->name); ?></td>
                <td><?php echo e($admin->email); ?></td>
                <td><?php echo e(optional($admin->location)->name ?: '-'); ?></td>
                <td>
                  <a href="<?php echo e(route('location-admins.show', $admin)); ?>" class="btn btn-sm btn-outline-info">View</a>
                  <a href="<?php echo e(route('location-admins.edit', $admin)); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                  <form action="<?php echo e(route('users.demote.employee', $admin)); ?>" method="POST" style="display:inline" onsubmit="return confirm('Demote this admin to Employee?')">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-sm btn-outline-warning">Demote to Employee</button>
                  </form>
                  <form action="<?php echo e(route('location-admins.destroy', $admin)); ?>" method="POST" style="display:inline" onsubmit="return confirm('Are you sure?')">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="5" class="text-center">No admins found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="card-footer">
        <?php echo e($admins->links()); ?>

      </div>
    </div>
  </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\location-admins\index.blade.php ENDPATH**/ ?>