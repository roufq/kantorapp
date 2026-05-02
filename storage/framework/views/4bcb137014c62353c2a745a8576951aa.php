<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo e(__('Refine Colleague Profile')); ?>

          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Update professional records and assignment details for accurate administration.')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <a href="<?php echo e(route('employees.index')); ?>" class="btn btn-light border rounded-pill px-4 fw-bold text-muted shadow-sm">
            <i class="mdi mdi-arrow-left me-2"></i><?php echo e(__('Back to Directory')); ?>

          </a>
        </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                <div class="card-header border-bottom border-light p-4 bg-transparent d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-account-edit-outline text-info me-2"></i><?php echo e(__('Record Management')); ?></h5>
                    <span class="badge badge-info"><?php echo e($employee->email); ?></span>
                </div>
                <div class="card-body p-4 p-lg-5">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-soft-rose border-0 rounded-4 mb-5">
                            <ul class="mb-0 small fw-bold">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('employees.update', $employee)); ?>" method="POST">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Legal Name')); ?></label>
                                <input type="text" name="nama" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" value="<?php echo e(old('nama', $employee->nama)); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Official Email')); ?></label>
                                <input type="email" name="email" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" value="<?php echo e(old('email', $employee->email)); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Direct Contact')); ?></label>
                                <input type="text" name="telepon" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" value="<?php echo e(old('telepon', $employee->telepon)); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Assigned Position')); ?></label>
                                <input type="text" name="jabatan" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" value="<?php echo e(old('jabatan', $employee->jabatan)); ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Primary Department')); ?></label>
                                <input type="text" name="departemen" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" value="<?php echo e(old('departemen', $employee->departemen)); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Onboarding Date')); ?></label>
                                <input type="date" name="tanggal_masuk_kerja" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" value="<?php echo e(old('tanggal_masuk_kerja', optional($employee->tanggal_masuk_kerja)->format('Y-m-d'))); ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Division Stream')); ?></label>
                                <select name="divisi_id" class="form-select rounded-pill px-4 border-light shadow-none fw-bold" required>
                                    <?php $__currentLoopData = $divisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $division): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($division->id); ?>" <?php echo e(old('divisi_id', $employee->divisi_id) == $division->id ? 'selected' : ''); ?>><?php echo e($division->nama); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Deployment Site')); ?></label>
                                <?php if(auth()->user()->hasRole('Location Admin')): ?>
                                    <input type="text" class="form-control rounded-pill px-4 bg-light border-0 fw-bold" value="<?php echo e(optional(auth()->user()->location)->name); ?>" readonly>
                                    <input type="hidden" name="location_id" value="<?php echo e(auth()->user()->location_id); ?>">
                                <?php else: ?>
                                    <select name="location_id" class="form-select rounded-pill px-4 border-light shadow-none fw-bold" required>
                                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($location->id); ?>" <?php echo e(old('location_id', $employee->location_id) == $location->id ? 'selected' : ''); ?>><?php echo e($location->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                <?php endif; ?>
                            </div>

                            <div class="col-12 mt-5 pt-3 border-top border-light text-end">
                                <a href="<?php echo e(route('employees.index')); ?>" class="btn btn-light rounded-pill px-5 fw-bold text-muted me-2 border"><?php echo e(__('Discard')); ?></a>
                                <button type="submit" class="btn btn-dark rounded-pill px-5 fw-bold shadow-sm">
                                    <?php echo e(__('Apply Modifications')); ?>

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
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\employees\edit.blade.php ENDPATH**/ ?>