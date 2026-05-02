<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Task Catalog</h3>
        <p class="text-muted mb-0">Jobdesk: <?php echo e($jobdesk->name); ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('jobdesks.index')); ?>" class="btn btn-outline-secondary btn-sm">Back</a>
        <a href="<?php echo e(route('jobdesks.catalogs.create', $jobdesk)); ?>" class="btn btn-primary btn-sm">Add Task</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Task Catalog List</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>Name</th>
                    <th>Unit</th>
                    <th>Value</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $catalogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catalog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration + ($catalogs->currentPage()-1)*$catalogs->perPage()); ?></td>
                        <td><?php echo e($catalog->name); ?></td>
                        <td><?php echo e(ucfirst($catalog->unit)); ?></td>
                        <td><?php echo e($catalog->value); ?></td>
                        <td><?php echo e(ucfirst($catalog->task_type)); ?></td>
                        <td>
                            <span class="badge <?php echo e($catalog->is_active ? 'badge-success' : 'badge-secondary'); ?>">
                                <?php echo e($catalog->is_active ? 'Active' : 'Inactive'); ?>

                            </span>
                        </td>
                        <td>
                            <a href="<?php echo e(route('jobdesks.catalogs.edit', [$jobdesk, $catalog])); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="<?php echo e(route('jobdesks.catalogs.destroy', [$jobdesk, $catalog])); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Delete this task catalog?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No task catalogs yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <?php echo e($catalogs->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\task-catalogs\index.blade.php ENDPATH**/ ?>