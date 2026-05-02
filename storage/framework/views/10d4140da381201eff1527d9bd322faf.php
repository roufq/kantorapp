<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo e(__('Performance Benchmarks')); ?>

          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Define monthly productivity goals for each operational site.')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <a href="<?php echo e(route('work-targets.create')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
            <i class="mdi mdi-plus-circle-outline me-2"></i><?php echo e(__('Define New Target')); ?>

          </a>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="card mb-5 border-0 shadow-sm rounded-4">
          <div class="card-body p-4">
              <form method="GET">
                  <div class="row g-3 align-items-end">
                      <div class="col-xl-3 col-md-4">
                          <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Target Month')); ?></label>
                          <input type="month" name="month" value="<?php echo e($monthParam); ?>" class="form-control rounded-pill px-4 border-light shadow-none fw-bold">
                      </div>

                      <?php if(auth()->user()->hasRole('Super Admin')): ?>
                      <div class="col-xl-3 col-md-4">
                          <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Site Location')); ?></label>
                          <select name="location_id" class="form-select rounded-pill px-4 border-light shadow-none fw-bold">
                              <option value=""><?php echo e(__('All Sites')); ?></option>
                              <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($loc->id); ?>" <?php if(request('location_id') == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name ?? $loc->nama ?? __('Location').' '.$loc->id); ?></option>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </select>
                      </div>
                      <?php endif; ?>

                      <div class="col-xl-3 col-md-4">
                          <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Team Member')); ?></label>
                          <select name="employee_id" class="form-select rounded-pill px-4 border-light shadow-none fw-bold">
                              <option value=""><?php echo e(__('All Members')); ?></option>
                              <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($emp->id); ?>" <?php if(request('employee_id') == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->nama ?? $emp->id); ?></option>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </select>
                      </div>

                      <div class="col-xl-3 col-md-12 d-flex gap-2">
                          <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold flex-grow-1 shadow-sm">
                              <?php echo e(__('Apply Filter')); ?>

                          </button>
                          <a href="<?php echo e(route('work-targets.index')); ?>" class="btn btn-light border rounded-pill px-4 fw-bold text-muted">
                              <i class="mdi mdi-refresh"></i>
                          </a>
                      </div>
                  </div>
              </form>
          </div>
      </div>

      <!-- Data Table -->
      <div class="card border-0 shadow-sm overflow-hidden mb-5">
          <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-bullseye-arrow text-info me-2"></i><?php echo e(__('Quota Repository')); ?></h5>
              <span class="text-muted small fw-bold"><?php echo e($targets->total()); ?> benchmarks defined</span>
          </div>
          <div class="card-body p-0">
              <div class="table-responsive">
                  <table class="table align-middle mb-0">
                      <thead>
                          <tr>
                              <th class="ps-4">No</th>
                              <th><?php echo e(__('Site')); ?></th>
                              <th><?php echo e(__('Target Holder')); ?></th>
                              <th><?php echo e(__('Benchmark Period')); ?></th>
                              <th class="text-center"><?php echo e(__('Target (Min)')); ?></th>
                              <th class="pe-4 text-end"><?php echo e(__('Actions')); ?></th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php $__empty_1 = true; $__currentLoopData = $targets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                              <tr>
                                  <td class="ps-4 text-muted fw-bold" style="width: 60px;"><?php echo e($loop->iteration); ?></td>
                                  <td>
                                      <div class="d-flex align-items-center">
                                          <div class="avatar-sm me-3 bg-light border rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 38px; height: 38px;">
                                              <i class="mdi mdi-map-marker-outline"></i>
                                          </div>
                                          <span class="fw-bold text-dark"><?php echo e($target->location->name ?? $target->location->nama ?? __('Global')); ?></span>
                                      </div>
                                  </td>
                                  <td>
                                      <?php if($target->employee): ?>
                                          <div class="d-flex align-items-center">
                                              <div class="avatar-sm me-3 border rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                                  <?php echo e(substr($target->employee->nama, 0, 1)); ?>

                                              </div>
                                              <span class="small fw-bold text-dark"><?php echo e($target->employee->nama); ?></span>
                                          </div>
                                      <?php else: ?>
                                          <span class="text-muted smallest fw-bold text-uppercase opacity-50"><?php echo e(__('Site Global')); ?></span>
                                      <?php endif; ?>
                                  </td>
                                  <td>
                                      <span class="badge badge-info px-3 py-2">
                                          <?php echo e(date("F", mktime(0, 0, 0, $target->month, 10))); ?> <?php echo e($target->year); ?>

                                      </span>
                                  </td>
                                  <td class="text-center"><span class="fw-bold text-dark fs-5"><?php echo e(number_format($target->target_minutes)); ?></span></td>
                                  <td class="pe-4 text-end">
                                      <div class="d-flex justify-content-end gap-2">
                                          <a href="<?php echo e(route('work-targets.edit', $target)); ?>" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-primary shadow-none">
                                              <i class="mdi mdi-pencil-outline"></i>
                                          </a>
                                          <form action="<?php echo e(route('work-targets.destroy', $target)); ?>" method="POST" class="d-inline" onsubmit="return confirm('<?php echo e(__('Are you sure?')); ?>');">
                                              <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                              <button type="submit" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-danger shadow-none">
                                                  <i class="mdi mdi-delete-outline"></i>
                                              </button>
                                          </form>
                                      </div>
                                  </td>
                              </tr>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                              <tr>
                                  <td colspan="6" class="text-center py-5">
                                      <div class="py-5 opacity-25">
                                          <i class="mdi mdi-bullseye-arrow fs-1 d-block mb-3"></i>
                                          <p class="mb-0"><?php echo e(__('No benchmarks defined for this selection.')); ?></p>
                                      </div>
                                  </td>
                              </tr>
                          <?php endif; ?>
                      </tbody>
                  </table>
              </div>
          </div>
          <?php if($targets->hasPages()): ?>
              <div class="card-footer bg-transparent border-top border-light p-4 d-flex justify-content-end">
                  <?php echo e($targets->links()); ?>

              </div>
          <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\work-targets\index.blade.php ENDPATH**/ ?>