<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;"><?php echo e(__('Location Admins')); ?></h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;"><?php echo e(__('Manage location admin accounts and their assignments.')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="<?php echo e(route('location-admins.create')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
            <i class="mdi mdi-account-plus-outline me-2"></i><?php echo e(__('Add Admin')); ?>

          </a>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card shadow-sm border-0" style="border-radius: 24px !important;">
            <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-shield-account-outline text-info me-2 fs-4"></i><?php echo e(__('Admin List')); ?></h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-white">
                        <thead class="bg-white bg-opacity-5">
                            <tr>
                                <th class="ps-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1" style="width: 80px"><?php echo e(__('No')); ?></th>
                                <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Name')); ?></th>
                                <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Email')); ?></th>
                                <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Location')); ?></th>
                                <th class="pe-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1 text-end"><?php echo e(__('Actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="border-bottom border-white border-opacity-5">
                                    <td class="ps-4 fw-bold text-muted"><?php echo e($loop->iteration + ($admins->currentPage()-1)*$admins->perPage()); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3 rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                <i class="mdi mdi-account-star-outline"></i>
                                            </div>
                                            <span class="fw-bold"><?php echo e($admin->name); ?></span>
                                        </div>
                                    </td>
                                    <td><span class="text-info small fw-medium"><?php echo e($admin->email); ?></span></td>
                                    <td>
                                        <?php if($admin->location): ?>
                                            <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-1 smaller">
                                                <?php echo e($admin->location->name); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small italic text-opacity-50">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="<?php echo e(route('location-admins.show', $admin)); ?>" class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold shadow-none" title="<?php echo e(__('View')); ?>">
                                                <i class="mdi mdi-eye-outline me-1"></i><?php echo e(__('View')); ?>

                                            </a>
                                            <a href="<?php echo e(route('location-admins.edit', $admin)); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-none" title="<?php echo e(__('Edit')); ?>">
                                                <i class="mdi mdi-pencil-outline me-1"></i><?php echo e(__('Edit')); ?>

                                            </a>
                                            
                                            <form action="<?php echo e(route('users.demote.employee', $admin)); ?>" method="POST" class="d-inline" onsubmit="return confirm('<?php echo e(__('Demote this admin to Employee?')); ?>')">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold shadow-none" title="<?php echo e(__('Demote')); ?>">
                                                    <i class="mdi mdi-account-arrow-down-outline me-1"></i><?php echo e(__('Demote')); ?>

                                                </button>
                                            </form>
    
                                            <form action="<?php echo e(route('location-admins.destroy', $admin)); ?>" method="POST" class="d-inline" onsubmit="return confirm('<?php echo e(__('Are you sure?')); ?>')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold shadow-none" title="<?php echo e(__('Delete')); ?>">
                                                    <i class="mdi mdi-trash-can-outline me-1"></i><?php echo e(__('Delete')); ?>

                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="mdi mdi-account-off-outline fs-1 text-white opacity-25 d-block mb-2"></i>
                                        <span class="text-muted italic"><?php echo e(__('No admins found.')); ?></span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <?php if($admins->hasPages()): ?>
                <div class="card-footer border-top border-light p-4 bg-transparent">
                    <?php echo e($admins->links()); ?>

                </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.italic { font-style: italic; }
.smaller { font-size: 0.75rem; }
.letter-spacing-1 { letter-spacing: 1px; }
.table-hover tbody tr:hover { background-color: rgba(255,255,255,0.02) !important; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/location-admins/index.blade.php ENDPATH**/ ?>