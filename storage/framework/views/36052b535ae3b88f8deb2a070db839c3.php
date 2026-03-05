

<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Edit Admin Lokasi</h3>
        <p class="text-muted mb-0">Perbarui data admin lokasi agar akses dan tanggung jawab tetap sesuai.</p>
    </div>
    <a href="<?php echo e(route('location-admins.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Edit Location Admin</h3></div>
      <div class="card-body">
        <form action="<?php echo e(route('location-admins.update', $admin)); ?>" method="POST">
          <?php echo csrf_field(); ?>
          <?php echo method_field('PATCH'); ?>
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $admin->name)); ?>" required>
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $admin->email)); ?>" required>
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div class="mb-3">
            <label class="form-label">Password (leave blank to keep)</label>
            <input type="password" name="password" class="form-control">
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Location</label>
            <select name="location_id" class="form-select" required>
              <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($loc->id); ?>" <?php echo e($admin->location_id == $loc->id ? 'selected' : ''); ?>><?php echo e($loc->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['location_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <button type="submit" class="btn btn-primary">Update</button>
          <a href="<?php echo e(route('location-admins.index')); ?>" class="btn btn-secondary">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\location-admins\edit.blade.php ENDPATH**/ ?>