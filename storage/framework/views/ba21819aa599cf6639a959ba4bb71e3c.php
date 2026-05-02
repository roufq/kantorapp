<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;"><?php echo e(__('Add Work Hours Recap')); ?></h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;"><?php echo e(__('Enter manual recap for employees per location and month.')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="<?php echo e(route('work-recaps.index')); ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i><?php echo e(__('Back')); ?>

          </a>
        </div>
      </div>

      <div class="row">
        <div class="col-xxl-8 col-xl-10 mx-auto">
          <div class="card shadow-sm border-0 p-4 p-md-5 rounded-4 border border-light shadow-lg">
            <form action="<?php echo e(route('work-recaps.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('Month')); ?></label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-calendar-month"></i></span>
                            <input type="month" name="month" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none" value="<?php echo e(old('month', $monthParam)); ?>" required>
                        </div>
                    </div>

                    <?php if(auth()->user()->hasRole('Super Admin')): ?>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('Location')); ?></label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-map-marker-outline"></i></span>
                            <select name="location_id" class="form-select bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none" required>
                                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($loc->id); ?>" <?php if(old('location_id') == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name ?? $loc->nama ?? __('Location').' '.$loc->id); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <?php else: ?>
                        <input type="hidden" name="location_id" value="<?php echo e(auth()->user()->location_id); ?>">
                    <?php endif; ?>

                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('Employee')); ?></label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-account-outline"></i></span>
                            <select name="employee_id" class="form-select bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none" required>
                                <option value=""><?php echo e(__('Choose employee')); ?></option>
                                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($emp->id); ?>" <?php if(old('employee_id') == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->nama ?? $emp->id); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 mt-5">
                        <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('Slot Approved (minutes)')); ?></label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-checkbox-marked-circle-outline"></i></span>
                            <input type="number" min="0" name="slot_minutes_approved" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none font-monospace" value="<?php echo e(old('slot_minutes_approved', 0)); ?>" required>
                        </div>
                    </div>

                    <div class="col-md-6 mt-5">
                        <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('Attendance (minutes)')); ?></label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-clock-outline"></i></span>
                            <input type="number" min="0" name="attendance_minutes" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none font-monospace" value="<?php echo e(old('attendance_minutes', 0)); ?>" required>
                        </div>
                    </div>

                    <div class="col-12 mt-5 d-flex justify-content-end gap-3">
                        <a href="<?php echo e(route('work-recaps.index')); ?>" class="btn btn-outline-secondary rounded-pill px-5 py-2 fw-bold shadow-sm"><?php echo e(__('Cancel')); ?></a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-lg">
                            <i class="mdi mdi-check-circle-outline me-2"></i><?php echo e(__('Save')); ?>

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

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\work-recaps\create.blade.php ENDPATH**/ ?>