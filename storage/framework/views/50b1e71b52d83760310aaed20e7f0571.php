<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;"><?php echo e(__('Edit Work Hours Target')); ?></h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;"><?php echo e(__('Update working minutes target for this specific entry.')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="<?php echo e(route('work-targets.index')); ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i><?php echo e(__('Back')); ?>

          </a>
        </div>
      </div>

      <div class="row">
        <div class="col-xxl-8 col-xl-10 mx-auto">
          <div class="card shadow-sm border-0 p-4 p-md-5 rounded-4 border border-light shadow-lg">
            <form action="<?php echo e(route('work-targets.update', $target)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('Month')); ?></label>
                        <div class="input-group text-white">
                            <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-calendar"></i></span>
                            <input type="month" name="month" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none" value="<?php echo e(old('month', $monthParam)); ?>" required>
                        </div>
                    </div>

                    <?php if(auth()->user()->hasRole('Super Admin')): ?>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('Location')); ?></label>
                        <div class="input-group text-white">
                            <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-map-marker-radius-outline"></i></span>
                            <select name="location_id" class="form-select bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none" required>
                                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($loc->id); ?>" <?php if(old('location_id', $target->location_id) == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name ?? $loc->nama ?? __('Location').' '.$loc->id); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <?php else: ?>
                        <input type="hidden" name="location_id" value="<?php echo e(auth()->user()->location_id); ?>">
                    <?php endif; ?>

                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('Employee (optional)')); ?></label>
                        <div class="input-group text-white">
                            <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-account-cog-outline"></i></span>
                            <select name="employee_id" class="form-select bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none">
                                <option value=""><?php echo e(__('All employees')); ?></option>
                                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($emp->id); ?>" <?php if(old('employee_id', $target->employee_id) == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->nama ?? $emp->id); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 mt-5">
                        <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('Target (minutes)')); ?></label>
                        <div class="input-group text-white">
                            <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-timer-outline"></i></span>
                            <input type="number" min="0" name="target_minutes" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none font-monospace" value="<?php echo e(old('target_minutes', $target->target_minutes)); ?>" required>
                        </div>
                    </div>

                    <div class="col-12 mt-5 d-flex justify-content-end gap-3">
                        <a href="<?php echo e(route('work-targets.index')); ?>" class="btn btn-outline-secondary rounded-pill px-5 py-2 fw-bold shadow-sm"><?php echo e(__('Cancel')); ?></a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-lg">
                            <i class="mdi mdi-content-save-outline me-2"></i><?php echo e(__('Update')); ?>

                        </button>
                    </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.letter-spacing-1 { letter-spacing: 1px; }
.form-select option { background-color: #1a1d21; color: white; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\work-targets\edit.blade.php ENDPATH**/ ?>