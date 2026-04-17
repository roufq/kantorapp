<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo e(__('Employees')); ?>

          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Manage your workforce and location assignments.')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <a href="<?php echo e(route('employees.create')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
            <i class="mdi mdi-account-plus-outline me-2"></i><?php echo e(__('Add Employee')); ?>

          </a>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card shadow-sm border-0">
            <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-account-group-outline text-info me-2 fs-4"></i><?php echo e(__('Global Staff Directory')); ?></h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4"><?php echo e(__('NO')); ?></th>
                                <th><?php echo e(__('Employee')); ?></th>
                                <th><?php echo e(__('Position')); ?></th>
                                <th><?php echo e(__('Division')); ?></th>
                                <th><?php echo e(__('Join Date')); ?></th>
                                <th><?php echo e(__('Location')); ?></th>
                                <th class="pe-4 text-end"><?php echo e(__('Actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted" style="width: 60px;"><?php echo e($loop->iteration + ($employees->currentPage()-1)*$employees->perPage()); ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 42px; height: 42px; background-color: var(--soft-celeste);">
                                                <?php echo e(strtoupper(substr($employee->nama, 0, 1))); ?><?php echo e(strtoupper(substr(strrchr($employee->nama, ' '), 1, 1)) ?: ''); ?>

                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-bold fs-6 text-dark"><?php echo e($employee->nama); ?></div>
                                            <div class="text-muted small"><?php echo e($employee->email); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="text-dark fw-semibold small"><?php echo e($employee->jabatan); ?></span></td>
                                <td>
                                    <span class="badge badge-warning">
                                        <?php echo e($employee->division->nama ?? 'N/A'); ?>

                                    </span>
                                </td>
                                <td><span class="text-muted small fw-medium"><?php echo e($employee->tanggal_masuk_kerja ? $employee->tanggal_masuk_kerja->format('d M Y') : '-'); ?></span></td>
                                <td>
                                    <?php if($employee->location): ?>
                                        <span class="badge badge-info">
                                            <?php echo e($employee->location->name); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted smaller font-italic">No Location</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="<?php echo e(route('employees.show', $employee)); ?>" class="btn btn-sm btn-light rounded-pill px-3 fw-bold border shadow-none" title="<?php echo e(__('View')); ?>">
                                            <i class="mdi mdi-eye-outline text-info"></i>
                                        </a>
                                        <a href="<?php echo e(route('employees.edit', $employee)); ?>" class="btn btn-sm btn-light rounded-pill px-3 fw-bold border shadow-none" title="<?php echo e(__('Edit')); ?>">
                                            <i class="mdi mdi-pencil-outline text-primary"></i>
                                        </a>
                                        <form action="<?php echo e(route('employees.destroy', $employee)); ?>" method="POST" class="d-inline" onsubmit="return confirm('<?php echo e(__('Delete this employee?')); ?>')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-light rounded-pill px-3 fw-bold border shadow-none" title="<?php echo e(__('Delete')); ?>">
                                                <i class="mdi mdi-trash-can-outline text-danger"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted py-4">
                                        <i class="mdi mdi-account-off-outline fs-1 d-block mb-2 opacity-25"></i>
                                        <p class="mb-0"><?php echo e(__('No employees found.')); ?></p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if($employees->hasPages()): ?>
                <div class="card-footer border-top border-light p-4 bg-transparent d-flex justify-content-end">
                    <?php echo e($employees->links()); ?>

                </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/employees/index.blade.php ENDPATH**/ ?>