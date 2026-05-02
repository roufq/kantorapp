<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;"><?php echo e(__('Location Admin Details')); ?></h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;"><?php echo e($admin->name); ?></p>
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
            <div class="d-flex justify-content-between align-items-start mb-5">
              <div class="d-flex align-items-center">
                <div class="avatar-box me-3 rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info border border-info border-opacity-20" style="width: 64px; height: 64px;">
                  <i class="mdi mdi-shield-account-outline fs-1"></i>
                </div>
                <div>
                  <h4 class="text-dark fw-bold mb-0"><?php echo e($admin->name); ?></h4>
                  <span class="badge rounded-pill bg-dark border border-light px-3 smaller"><?php echo e(__('Location Admin')); ?></span>
                </div>
              </div>
              <a href="<?php echo e(route('location-admins.edit', $admin)); ?>" class="btn btn-primary rounded-pill px-4 fw-bold">
                <i class="mdi mdi-pencil-outline me-1"></i><?php echo e(__('Edit')); ?>

              </a>
            </div>

            <div class="row g-4 mb-5">
              <div class="col-md-6">
                <div class="p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-5">
                    <label class="text-muted small fw-bold text-uppercase letter-spacing-1 d-block mb-1"><?php echo e(__('ID:')); ?></label>
                    <div class="text-dark fw-bold">#<?php echo e($admin->id); ?></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-5">
                    <label class="text-muted small fw-bold text-uppercase letter-spacing-1 d-block mb-1"><?php echo e(__('Email')); ?></label>
                    <div class="text-dark fw-bold text-truncate"><?php echo e($admin->email); ?></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-5">
                    <label class="text-muted small fw-bold text-uppercase letter-spacing-1 d-block mb-1"><?php echo e(__('Location')); ?></label>
                    <div class="text-dark fw-bold">
                      <?php if($admin->location): ?>
                        <i class="mdi mdi-map-marker text-info me-1"></i><?php echo e($admin->location->name); ?>

                      <?php else: ?>
                        <span class="text-muted text-opacity-50 italic">-</span>
                      <?php endif; ?>
                    </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-5">
                    <label class="text-muted small fw-bold text-uppercase letter-spacing-1 d-block mb-1"><?php echo e(__('Roles:')); ?></label>
                    <div class="text-dark fw-bold">
                      <?php ($roles = $admin->getRoleNames()); ?>
                      <?php if($roles->isNotEmpty()): ?>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 smaller"><?php echo e($role); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php else: ?>
                        <span class="text-muted text-opacity-50 italic">-</span>
                      <?php endif; ?>
                    </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-5">
                    <label class="text-muted small fw-bold text-uppercase letter-spacing-1 d-block mb-1"><?php echo e(__('Created:')); ?></label>
                    <div class="text-muted smaller"><i class="mdi mdi-clock-outline me-1"></i><?php echo e($admin->created_at->format('d M Y, H:i')); ?></div>
                </div>
              </div>
            </div>

            <div class="pt-4 border-top border-light d-flex justify-content-between align-items-center">
              <p class="text-muted smaller mb-0 italic">
                <i class="mdi mdi-information-outline me-1"></i><?php echo e(__('Demoting an admin will remove their administrative privileges.')); ?>

              </p>
              <form action="<?php echo e(route('users.demote.employee', $admin)); ?>" method="POST" onsubmit="return confirm('<?php echo e(__('Demote this admin to Employee?')); ?>')">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-outline-warning rounded-pill px-4 fw-bold shadow-sm">
                  <i class="mdi mdi-account-arrow-down-outline me-2"></i><?php echo e(__('Demote')); ?>

                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.smaller { font-size: 0.85rem; }
.italic { font-style: italic; }
.letter-spacing-1 { letter-spacing: 1px; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\location-admins\show.blade.php ENDPATH**/ ?>