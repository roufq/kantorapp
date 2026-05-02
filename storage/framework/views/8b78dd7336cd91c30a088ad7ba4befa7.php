
<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Approval Rules</h3>
        <p class="text-muted mb-0">Set up approval workflow based on department & value.</p>
    </div>
    <a href="<?php echo e(route('approval-rules.create')); ?>" class="btn btn-primary btn-sm">Add Rule</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Scope</label>
                <input type="text" name="scope" class="form-control" value="<?php echo e(request('scope', 'task')); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Department</label>
                <input type="text" name="department" class="form-control" value="<?php echo e(request('department')); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Active</option>
                    <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">Filter</button>
                <a href="<?php echo e(route('approval-rules.index')); ?>" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Rule List</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Scope</th>
                    <th>Department</th>
                    <th>Min Value</th>
                    <th>Approval Level</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $rules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration + ($rules->currentPage()-1)*$rules->perPage()); ?></td>
                        <td><?php echo e($rule->scope); ?></td>
                        <td><?php echo e($rule->department ?? 'All'); ?></td>
                        <td><?php echo e($rule->min_value); ?></td>
                        <td><?php echo e($rule->approval_level === 'super_admin' ? 'Super Admin' : 'Location Admin'); ?></td>
                        <td>
                            <span class="badge <?php echo e($rule->is_active ? 'badge-success' : 'badge-secondary'); ?>">
                                <?php echo e($rule->is_active ? 'Active' : 'Inactive'); ?>

                            </span>
                        </td>
                        <td>
                            <a href="<?php echo e(route('approval-rules.edit', $rule)); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="<?php echo e(route('approval-rules.destroy', $rule)); ?>" method="POST" style="display:inline" onsubmit="return confirm('Delete this rule?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center text-muted">No rules yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <?php echo e($rules->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\approval-rules\index.blade.php ENDPATH**/ ?>