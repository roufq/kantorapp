<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Assign Jobdesk</h3>
        <p class="text-muted mb-0">Jobdesk: <?php echo e($jobdesk->name); ?></p>
    </div>
    <a href="<?php echo e(route('jobdesks.index')); ?>" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('jobdesks.assignments.store', $jobdesk)); ?>" class="row g-3">
            <?php echo csrf_field(); ?>
            <div class="col-md-6">
                <label class="form-label">Select Employee</label>
                <select name="employee_ids[]" class="form-select" multiple required>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->nama); ?> <?php if($emp->location): ?> - <?php echo e($emp->location->name ?? $emp->location->nama); ?> <?php endif; ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <small class="text-muted">Use Ctrl/Command to select multiple.</small>
            </div>
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="<?php echo e(old('start_date')); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="<?php echo e(old('end_date')); ?>">
            </div>
            <div class="col-md-3 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="is_primary" value="1" id="isPrimary" checked>
                    <label class="form-check-label" for="isPrimary">Primary Jobdesk</label>
                </div>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Save Assignment</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Active Assignments</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Employees</th>
                    <th>Location</th>
                    <th>Primary</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration + ($assignments->currentPage()-1)*$assignments->perPage()); ?></td>
                        <td><?php echo e($assignment->employee?->nama ?? '-'); ?></td>
                        <td><?php echo e($assignment->employee?->location?->name ?? $assignment->employee?->location?->nama ?? '-'); ?></td>
                        <td><?php echo e($assignment->is_primary ? 'Yes' : 'No'); ?></td>
                        <td><?php echo e($assignment->start_date?->format('Y-m-d') ?? '-'); ?></td>
                        <td><?php echo e($assignment->end_date?->format('Y-m-d') ?? '-'); ?></td>
                        <td>
                            <form action="<?php echo e(route('jobdesks.assignments.destroy', [$jobdesk, $assignment])); ?>" method="POST" onsubmit="return confirm('Delete this assignment?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No assignments yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <?php echo e($assignments->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\jobdesks\assignments.blade.php ENDPATH**/ ?>