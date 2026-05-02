

<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
  <div>
    <h3 class="mb-1">Select Active Location</h3>
    <p class="text-muted mb-0">Super Admin can select a location to scope the dashboard and data.</p>
  </div>
  <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-secondary btn-sm">Back</a>
 </div>

<div class="card">
  <div class="card-body">
    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <form method="POST" action="<?php echo e(route('location-selection.store')); ?>">
      <?php echo csrf_field(); ?>
      <div class="mb-3">
        <label class="form-label">Location</label>
        <select name="location_id" class="form-control" required>
          <option value="">Select location</option>
          <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($loc->id); ?>" <?php if($current==$loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name); ?> (<?php echo e($loc->code); ?>)</option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['location_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
          <div class="text-danger small"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <button class="btn btn-primary">Set Location</button>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\locations\select.blade.php ENDPATH**/ ?>