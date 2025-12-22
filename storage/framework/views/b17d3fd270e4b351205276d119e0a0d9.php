

<?php $__env->startSection('content'); ?>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Location Admin Details</h3>
        <div class="card-tools">
          <a href="<?php echo e(route('location-admins.index')); ?>" class="btn btn-sm btn-secondary">Back</a>
          <a href="<?php echo e(route('location-admins.edit', $admin)); ?>" class="btn btn-sm btn-primary">Edit</a>
        </div>
      </div>
      <div class="card-body">
        <p><strong>ID:</strong> <?php echo e($admin->id); ?></p>
        <p><strong>Name:</strong> <?php echo e($admin->name); ?></p>
        <p><strong>Email:</strong> <?php echo e($admin->email); ?></p>
        <p><strong>Location:</strong> <?php echo e(optional($admin->location)->name ?: '-'); ?></p>
        <p><strong>Roles:</strong>
          <?php ($roles = $admin->getRoleNames()); ?>
          <?php if($roles->isNotEmpty()): ?>
            <?php echo e($roles->implode(', ')); ?>

          <?php else: ?>
            -
          <?php endif; ?>
        </p>
        <p><strong>Created:</strong> <?php echo e($admin->created_at->format('d M Y H:i')); ?></p>
        <hr>
        <form action="<?php echo e(route('users.demote.employee', $admin)); ?>" method="POST" onsubmit="return confirm('Demote this admin to Employee?')">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn btn-warning">Demote to Employee</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/location-admins/show.blade.php ENDPATH**/ ?>