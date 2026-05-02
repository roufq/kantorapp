<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo e(__('Career Paths')); ?>

          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Historical record of employee roles and department changes.')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <a href="<?php echo e(route('employee-positions.create')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
            <i class="mdi mdi-plus-circle-outline me-2"></i><?php echo e(__('Add New Record')); ?>

          </a>
        </div>
      </div>

      <div class="card mb-4 border-0 shadow-sm rounded-4">
          <div class="card-body p-4">
              <form method="GET" class="row g-3 align-items-end">
                  <div class="col-md-6">
                      <label class="form-label text-muted small fw-bold text-uppercase"><?php echo e(__('Search Employee')); ?></label>
                      <select name="employee_id" class="form-select rounded-pill px-4 border-light shadow-none fw-bold">
                          <option value=""><?php echo e(__('All Staff')); ?></option>
                          <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <option value="<?php echo e($emp->id); ?>" <?php if(request('employee_id') == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->nama); ?></option>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>
                  </div>
                  <div class="col-md-4 d-flex gap-2">
                      <button class="btn btn-dark rounded-pill px-4 fw-bold flex-grow-1 shadow-sm"><?php echo e(__('Apply Filter')); ?></button>
                      <a href="<?php echo e(route('employee-positions.index')); ?>" class="btn btn-light border rounded-pill px-4 fw-bold text-muted"><?php echo e(__('Reset')); ?></a>
                  </div>
              </form>
          </div>
      </div>

      <div class="card border-0 shadow-sm overflow-hidden">
          <div class="card-header border-bottom border-light p-4 bg-transparent">
              <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-layers-outline text-info me-2"></i><?php echo e(__('Promotion & Transfer History')); ?></h5>
          </div>
          <div class="card-body p-0">
              <div class="table-responsive">
                  <table class="table align-middle mb-0">
                      <thead>
                          <tr>
                              <th class="ps-4">No</th>
                              <th><?php echo e(__('Staff Name')); ?></th>
                              <th><?php echo e(__('Position')); ?></th>
                              <th><?php echo e(__('Department')); ?></th>
                              <th><?php echo e(__('Period')); ?></th>
                              <th class="pe-4 text-end"><?php echo e(__('Actions')); ?></th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php $__empty_1 = true; $__currentLoopData = $histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                              <tr>
                                  <td class="ps-4 fw-bold text-muted" style="width: 60px;"><?php echo e($loop->iteration + ($histories->currentPage()-1)*$histories->perPage()); ?></td>
                                  <td>
                                      <div class="fw-bold text-dark"><?php echo e($history->employee?->nama ?? '-'); ?></div>
                                  </td>
                                  <td><span class="badge badge-info"><?php echo e($history->title); ?></span></td>
                                  <td><span class="text-muted small fw-semibold"><?php echo e($history->department ?? '-'); ?></span></td>
                                  <td>
                                      <div class="small fw-bold text-dark"><?php echo e($history->start_date?->format('d M Y') ?? '-'); ?></div>
                                      <div class="smallest text-muted"><?php echo e($history->end_date ? 'Until ' . $history->end_date->format('d M Y') : 'Present'); ?></div>
                                  </td>
                                  <td class="pe-4 text-end">
                                      <div class="d-flex gap-2 justify-content-end">
                                          <a href="<?php echo e(route('employee-positions.edit', $history)); ?>" class="btn btn-sm btn-light border rounded-pill px-3 shadow-none text-primary"><i class="mdi mdi-pencil"></i></a>
                                          <form action="<?php echo e(route('employee-positions.destroy', $history)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this history?')">
                                              <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                              <button class="btn btn-sm btn-light border rounded-pill px-3 shadow-none text-danger"><i class="mdi mdi-trash-can-outline"></i></button>
                                          </form>
                                      </div>
                                  </td>
                              </tr>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                              <tr>
                                  <td colspan="6" class="text-center py-5">
                                      <div class="py-5 opacity-25">
                                          <i class="mdi mdi-buffer fs-1 d-block mb-3"></i>
                                          <p class="mb-0"><?php echo e(__('No records found.')); ?></p>
                                      </div>
                                  </td>
                              </tr>
                          <?php endif; ?>
                      </tbody>
                  </table>
              </div>
          </div>
          <?php if($histories->hasPages()): ?>
            <div class="card-footer bg-transparent border-top border-light p-4 d-flex justify-content-end">
                <?php echo e($histories->links()); ?>

            </div>
          <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\employee-positions\index.blade.php ENDPATH**/ ?>