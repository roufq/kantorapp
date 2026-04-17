<?php $__env->startSection('content'); ?>
<div class="row mb-5 align-items-center">
    <div class="col-lg-7">
        <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo e(__('Personnel Intelligence')); ?>

        </h1>
        <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Strategic oversight of human capital and organizational hierarchy.')); ?></p>
    </div>
    <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
        <a href="<?php echo e(route('employees.create')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-soft py-2">
            <i class="mdi mdi-account-plus me-1"></i><?php echo e(__('Onboard Personnel')); ?>

        </a>
    </div>
</div>

<div class="card border-0 shadow-soft rounded-5 overflow-hidden bg-white">
    <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-dark fw-bold"><i class="mdi mdi-account-group-outline text-primary me-2"></i><?php echo e(__('Global Staff Directory')); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="min-width: 800px;">
                <thead class="bg-light bg-opacity-50">
                    <tr>
                        <th class="ps-4 py-3 border-0 status-badge text-muted"><?php echo e(__('ID')); ?></th>
                        <th class="py-3 border-0 status-badge text-muted"><?php echo e(__('Personnel')); ?></th>
                        <th class="py-3 border-0 status-badge text-muted"><?php echo e(__('Role & Function')); ?></th>
                        <th class="py-3 border-0 status-badge text-muted"><?php echo e(__('Division')); ?></th>
                        <th class="py-3 border-0 status-badge text-muted"><?php echo e(__('Deployment')); ?></th>
                        <th class="pe-4 text-end py-3 border-0 status-badge text-muted"><?php echo e(__('Operations')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="transition-base">
                            <td class="ps-4 fw-bold text-muted smaller"><?php echo e($loop->iteration + ($employees->currentPage()-1)*$employees->perPage()); ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-800 smallest me-3 shadow-none text-primary" style="width: 42px; height: 42px; background-color: var(--soft-indigo);">
                                        <?php echo e(strtoupper(substr($employee->nama, 0, 1))); ?><?php echo e(strtoupper(substr(strrchr($employee->nama, ' '), 1, 1)) ?: ''); ?>

                                    </div>
                                    <div>
                                        <div class="fw-800 text-dark smaller"><?php echo e($employee->nama); ?></div>
                                        <div class="text-muted smallest"><?php echo e($employee->email); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark smaller"><?php echo e($employee->jabatan); ?></div>
                                <div class="text-muted smallest"><?php echo e(__('Tenure')); ?>: <?php echo e($employee->tanggal_masuk_kerja ? $employee->tanggal_masuk_kerja->format('M Y') : '-'); ?></div>
                            </td>
                            <td>
                                <span class="badge badge-mint border-0 px-3 fw-bold status-badge">
                                    <?php echo e($employee->division->nama ?? 'N/A'); ?>

                                </span>
                            </td>
                            <td>
                                <?php if($employee->location): ?>
                                    <span class="badge badge-indigo border-0 px-3 fw-bold status-badge">
                                        <?php echo e($employee->location->name); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-muted smallest font-italic"><?php echo e(__('Undeployed')); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="<?php echo e(route('employees.show', $employee)); ?>" class="btn btn-sm btn-outline-light text-dark border bg-white rounded-pill px-3 fw-bold shadow-soft smallest" title="<?php echo e(__('Overview')); ?>">
                                        <i class="mdi mdi-eye-outline text-primary me-1"></i><?php echo e(__('View')); ?>

                                    </a>
                                    <a href="<?php echo e(route('employees.edit', $employee)); ?>" class="btn btn-sm btn-outline-light text-dark border bg-white rounded-pill px-3 fw-bold shadow-soft smallest" title="<?php echo e(__('Modify')); ?>">
                                        <i class="mdi mdi-pencil-outline text-warning me-1"></i><?php echo e(__('Edit')); ?>

                                    </a>
                                    <form action="<?php echo e(route('employees.destroy', $employee)); ?>" method="POST" class="d-inline" onsubmit="return confirm('<?php echo e(__('Are you sure?')); ?>')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-light text-dark border bg-white rounded-pill px-3 fw-bold shadow-soft smallest" title="<?php echo e(__('Terminate')); ?>">
                                            <i class="mdi mdi-trash-can-outline text-danger me-1"></i><?php echo e(__('Delete')); ?>

                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted py-5">
                                    <i class="mdi mdi-account-off-outline fs-1 d-block mb-3 opacity-25"></i>
                                    <h6 class="fw-bold"><?php echo e(__('Personnel Database Empty')); ?></h6>
                                    <p class="smallest"><?php echo e(__('No employees have been registered in the system yet.')); ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($employees->hasPages()): ?>
        <div class="card-footer border-top border-light p-4 bg-white d-flex justify-content-end">
            <?php echo e($employees->links()); ?>

        </div>
    <?php endif; ?>
</div>

<style>
    .status-badge { font-size: 0.65rem; font-weight: 800; letter-spacing: 0.05rem; text-transform: uppercase; }
    .smaller { font-size: 0.85rem; }
    .smallest { font-size: 0.75rem; }
    .fw-800 { font-weight: 800; }
    .transition-base { transition: all 0.2s ease; }
    tr.transition-base:hover { background-color: rgba(248, 250, 252, 0.8); }
    .shadow-soft { box-shadow: 0 10px 40px rgba(0,0,0,0.04) !important; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/employees/index.blade.php ENDPATH**/ ?>