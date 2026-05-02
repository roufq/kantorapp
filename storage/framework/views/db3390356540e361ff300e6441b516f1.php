<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Jobdesk Target Output</h3>
        <p class="text-muted mb-0">Set target output per jobdesk/employee per month.</p>
    </div>
    <a href="<?php echo e(route('jobdesk-targets.create')); ?>" class="btn btn-primary btn-sm">Add Target</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Jobdesk</label>
                <select name="jobdesk_id" class="form-select">
                    <option value="">All</option>
                    <?php $__currentLoopData = $jobdesks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jobdesk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($jobdesk->id); ?>" <?php if(request('jobdesk_id') == $jobdesk->id): echo 'selected'; endif; ?>><?php echo e($jobdesk->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Employees</label>
                <select name="employee_id" class="form-select">
                    <option value="">All</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>" <?php if(request('employee_id') == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Month</label>
                <input type="month" name="month" class="form-control" value="<?php echo e(request('month', $monthParam)); ?>">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">Filter</button>
                <a href="<?php echo e(route('jobdesk-targets.index')); ?>" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Target Output List</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jobdesk</th>
                    <th>Employees</th>
                    <th>Month</th>
                    <th>Unit</th>
                    <th>Target</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $targets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration + ($targets->currentPage()-1)*$targets->perPage()); ?></td>
                        <td><?php echo e($target->jobdesk?->name ?? '-'); ?></td>
                        <td><?php echo e($target->employee?->nama ?? 'All Employees'); ?></td>
                        <td><?php echo e(sprintf('%02d-%04d', $target->month, $target->year)); ?></td>
                        <td><?php echo e(ucfirst($target->unit)); ?></td>
                        <td><?php echo e($target->target_value); ?></td>
                        <td>
                            <a href="<?php echo e(route('jobdesk-targets.edit', $target)); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="<?php echo e(route('jobdesk-targets.destroy', $target)); ?>" method="POST" style="display:inline" onsubmit="return confirm('Delete this target?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center text-muted">No target outputs yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <?php echo e($targets->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\jobdesk-targets\index.blade.php ENDPATH**/ ?>