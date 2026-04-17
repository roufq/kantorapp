<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-white mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;"><?php echo e(__('Divisions')); ?></h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;"><?php echo e(__('Manage organizational divisions to maintain a structured employee hierarchy.')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="<?php echo e(route('divisions.create')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
            <i class="mdi mdi-plus-circle-outline me-2"></i><?php echo e(__('Add Division')); ?>

          </a>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card shadow-sm border-0" style="border-radius: 24px !important;">
            <div class="card-header border-bottom border-white border-opacity-10 p-4 bg-transparent d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0 text-white fw-bold"><i class="mdi mdi-sitemap text-info me-2 fs-4"></i><?php echo e(__('Division List')); ?></h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-white">
                        <thead class="bg-white bg-opacity-5">
                            <tr>
                                <th class="ps-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1" style="width: 80px"><?php echo e(__('No')); ?></th>
                                <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Name')); ?></th>
                                <th class="pe-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1 text-end"><?php echo e(__('Actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $divisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $division): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="border-bottom border-white border-opacity-5">
                                <td class="ps-4 fw-bold text-muted"><?php echo e($loop->iteration + ($divisions->currentPage()-1)*$divisions->perPage()); ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                            <i class="mdi mdi-account-group-outline"></i>
                                        </div>
                                        <span class="fw-bold fs-6"><?php echo e($division->nama); ?></span>
                                    </div>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="<?php echo e(route('divisions.show', $division)); ?>" class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold shadow-none" title="<?php echo e(__('View')); ?>">
                                            <i class="mdi mdi-eye-outline me-1"></i><?php echo e(__('View')); ?>

                                        </a>
                                        <a href="<?php echo e(route('divisions.edit', $division)); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-none" title="<?php echo e(__('Edit')); ?>">
                                            <i class="mdi mdi-pencil-outline me-1"></i><?php echo e(__('Edit')); ?>

                                        </a>
                                        <form action="<?php echo e(route('divisions.destroy', $division)); ?>" method="POST" onsubmit="return confirm('<?php echo e(__('Delete this division?')); ?>')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold shadow-none" title="<?php echo e(__('Delete')); ?>">
                                                <i class="mdi mdi-trash-can-outline me-1"></i><?php echo e(__('Delete')); ?>

                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <i class="mdi mdi-folder-outline fs-1 text-white opacity-25 d-block mb-2"></i>
                                    <span class="text-muted italic"><?php echo e(__('No data yet')); ?></span>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if($divisions->hasPages()): ?>
                <div class="card-footer border-top border-white border-opacity-10 p-4 bg-transparent">
                    <?php echo e($divisions->links()); ?>

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
.letter-spacing-1 { letter-spacing: 1px; }
.table-hover tbody tr:hover { background-color: rgba(255,255,255,0.02) !important; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/divisions/index.blade.php ENDPATH**/ ?>