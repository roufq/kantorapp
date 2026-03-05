<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Tugas Saya</h3>
        <p class="text-muted mb-0">Daftar tugas yang Anda buat atau kerjakan.</p>
    </div>
    <a href="<?php echo e(route('master-tasks.create.self')); ?>" class="btn btn-primary btn-sm">Create Task</a>
</div>

<!-- Search Form -->
<div class="mb-4">
    <form method="GET" action="<?php echo e(route('master-tasks.my-tasks.index')); ?>" class="d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Search by task title..." value="<?php echo e(request('search')); ?>">
        <button type="submit" class="btn btn-outline-primary">Search</button>
        <?php if(request('search')): ?>
            <a href="<?php echo e(route('master-tasks.my-tasks.index')); ?>" class="btn btn-outline-secondary ms-2">Clear</a>
        <?php endif; ?>
    </form>
</div>

<?php if($tasks->count() > 0): ?>
    <div class="row">
        <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"><a href="<?php echo e(route('master-tasks.show', $task)); ?>"><?php echo e($task->title); ?></a></h5>
                    </div>
                    <div class="card-body">
                        <p><?php echo e($task->description); ?></p>
                        <p class="mb-1"><strong>Status:</strong> <?php echo e(ucfirst($task->status)); ?></p>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small">
                                <span>Progress</span>
                                <span><?php echo e($task->progress ?? 0); ?>%</span>
                            </div>
                            <div class="progress" style="height:8px;">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo e($task->progress ?? 0); ?>%;" aria-valuenow="<?php echo e($task->progress ?? 0); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <p><strong>Due:</strong> <?php echo e($task->due_date ? $task->due_date->format('d M Y') : 'No due date'); ?></p>
                    </div>
                    <div class="card-footer">
                        <div class="mt-2">
                            <a href="<?php echo e(route('master-tasks.show', $task)); ?>#progress-form" class="btn btn-sm btn-primary">Update Progress</a>
                            <a href="<?php echo e(route('master-tasks.edit', $task)); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="<?php echo e(route('master-tasks.destroy', $task)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Pagination -->
    <?php if($tasks->hasPages()): ?>
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($tasks->appends(request()->query())->links()); ?>

        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="alert alert-info">
        No personal tasks found.
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\master-tasks\my-tasks\index.blade.php ENDPATH**/ ?>