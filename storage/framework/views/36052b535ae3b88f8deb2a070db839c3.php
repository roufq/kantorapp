<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;"><?php echo e(__('Edit Location Admin')); ?></h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;"><?php echo e(__('Update admin account details to ensure accurate access and responsibility.')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="<?php echo e(route('location-admins.index')); ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i><?php echo e(__('Back')); ?>

          </a>
        </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-xl-8">
          <div class="card shadow-sm border-0 p-4 p-md-5 rounded-4 border border-light shadow-lg">
            <form action="<?php echo e(route('location-admins.update', $admin)); ?>" method="POST">
              <?php echo csrf_field(); ?>
              <?php echo method_field('PATCH'); ?>
              <div class="row g-4">
                <div class="col-md-12">
                  <h5 class="text-dark fw-bold mb-3 d-flex align-items-center">
                    <i class="mdi mdi-account-edit-outline text-info me-2 fs-4"></i><?php echo e(__('Admin Information')); ?>

                  </h5>
                </div>

                <div class="col-md-6">
                  <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('Name')); ?></label>
                  <input type="text" name="name" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-pill px-4 shadow-none <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                         value="<?php echo e(old('name', $admin->name)); ?>" required>
                  <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback ms-3"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-6">
                  <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('Email')); ?></label>
                  <input type="email" name="email" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-pill px-4 shadow-none <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                         value="<?php echo e(old('email', $admin->email)); ?>" required>
                  <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback ms-3"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-6">
                  <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('Password (leave blank to keep)')); ?></label>
                  <div class="input-group">
                    <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-lock-outline"></i></span>
                    <input type="password" name="password" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                  </div>
                  <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback ms-3 d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-6">
                  <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('Confirm Password')); ?></label>
                  <div class="input-group">
                    <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-lock-check-outline"></i></span>
                    <input type="password" name="password_confirmation" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none">
                  </div>
                </div>

                <div class="col-md-12">
                  <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('Location')); ?></label>
                  <select name="location_id" class="form-select bg-dark bg-opacity-50 border-light text-white rounded-pill px-4 shadow-none <?php $__errorArgs = ['location_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($loc->id); ?>" <?php echo e($admin->location_id == $loc->id ? 'selected' : ''); ?>><?php echo e($loc->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                  <?php $__errorArgs = ['location_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback ms-3 d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-12 mt-5 text-end">
                  <a href="<?php echo e(route('location-admins.index')); ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-bold me-2"><?php echo e(__('Cancel')); ?></a>
                  <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-lg">
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

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\location-admins\edit.blade.php ENDPATH**/ ?>