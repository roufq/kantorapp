<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo e(__('Divisions')); ?>

          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Organize your company departments and team structures.')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <a href="<?php echo e(route('divisions.create')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
            <i class="mdi mdi-plus-circle-outline me-2"></i><?php echo e(__('Add Division')); ?>

          </a>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-sitemap-outline text-info me-2"></i><?php echo e(__('Departmental Organization')); ?></h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No</th>
                                <th><?php echo e(__('Division Name')); ?></th>
                                <th class="pe-4 text-end"><?php echo e(__('Actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $divisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $division): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted" style="width: 60px;"><?php echo e($loop->iteration + ($divisions->currentPage()-1)*$divisions->perPage()); ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-light border rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px;">
                                            <i class="mdi mdi-account-group-outline"></i>
                                        </div>
                                        <span class="fw-bold text-dark fs-6"><?php echo e($division->nama); ?></span>
                                    </div>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="<?php echo e(route('divisions.show', $division)); ?>" class="btn btn-sm btn-light border rounded-pill px-3 shadow-none fw-bold text-info"><i class="mdi mdi-eye-outline me-1"></i>View</a>
                                        <a href="<?php echo e(route('divisions.edit', $division)); ?>" class="btn btn-sm btn-light border rounded-pill px-3 shadow-none fw-bold text-primary"><i class="mdi mdi-pencil-outline me-1"></i>Edit</a>
                                        <form action="<?php echo e(route('divisions.destroy', $division)); ?>" method="POST" class="d-inline" onsubmit="return confirm('<?php echo e(__('Are you sure?')); ?>')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-light border rounded-pill px-3 shadow-none fw-bold text-danger"><i class="mdi mdi-trash-can-outline me-1"></i>Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <div class="py-5 opacity-25">
                                        <i class="mdi mdi-folder-outline fs-1 d-block mb-3"></i>
                                        <p class="mb-0"><?php echo e(__('No entries yet.')); ?></p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if($divisions->hasPages()): ?>
                <div class="card-footer border-top border-light p-4 bg-transparent d-flex justify-content-end">
                    <?php echo e($divisions->links()); ?>

                </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\divisions\index.blade.php ENDPATH**/ ?>