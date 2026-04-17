

<?php $__env->startSection('content'); ?>
      <div class="row mb-4 align-items-center">
        <div class="col-lg-6">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;"><?php echo e(__('Create New Report')); ?></h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;"><?php echo e(__('Employees report to location admin, then proceed to Super Admin.')); ?></p>
        </div>
        <div class="col-lg-6 text-lg-end mt-3 mt-lg-0">
          <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i><?php echo e(__('Back')); ?>

          </a>
        </div>
      </div>
  <div class="content">
    <div class="container-fluid">
      <?php if($errors->any()): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="card">
        <div class="card-body">
          <form action="<?php echo e(route('reports.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php if(auth()->user()->hasRole('Super Admin')): ?>
              <div class="mb-3">
                <label class="form-label fw-bold text-dark small"><?php echo e(__('Location')); ?></label>
                <select name="location_id" class="form-select rounded-pill px-4" required>
                  <option value=""><?php echo e(__('Pick location')); ?></option>
                  <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($loc->id); ?>" <?php if(old('location_id')==$loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            <?php else: ?>
              <div class="mb-3">
                <label class="form-label fw-bold text-dark small"><?php echo e(__('Location')); ?></label>
                <input type="text" class="form-control rounded-pill px-4" value="<?php echo e(auth()->user()->location?->name ?? '-'); ?>" disabled>
              </div>
            <?php endif; ?>

            <div class="mb-3">
              <label class="form-label fw-bold text-dark small"><?php echo e(__('Title')); ?></label>
              <input type="text" name="title" value="<?php echo e(old('title')); ?>" class="form-control rounded-pill px-4" required placeholder="e.g. Printer Issue">
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold text-dark small"><?php echo e(__('Description')); ?></label>
              <textarea name="description" rows="4" class="form-control rounded-3 px-3 py-2" required style="border-radius: 15px !important;" placeholder="Detailed description of the issue..."></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold text-dark small"><?php echo e(__('Attachments (optional, max 10 MB/file)')); ?></label>
              <input type="file" name="attachments[]" class="form-control rounded-pill px-4" multiple>
            </div>

            <div class="d-flex gap-3 mt-4">
              <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">
                <i class="mdi mdi-send me-2 fs-5 align-middle"></i><?php echo e(__('Submit Report')); ?>

              </button>
              <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                <?php echo e(__('Cancel')); ?>

              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/reports/create.blade.php ENDPATH**/ ?>