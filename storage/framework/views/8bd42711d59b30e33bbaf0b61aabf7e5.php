

<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
  <div>
    <h3 class="mb-1">Location Task Details</h3>
    <p class="text-muted mb-0"><?php echo e($task->title); ?></p>
  </div>
  <a href="<?php echo e(route('location-admin-tasks.index')); ?>" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Task Details (Location)</h3>
        <div class="card-tools">
          <a href="<?php echo e(route('location-admin-tasks.edit', $task)); ?>" class="btn btn-sm btn-primary">Edit</a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p><strong>Title:</strong> <?php echo e($task->title); ?></p>
            <p><strong>Description:</strong> <?php echo e($task->description ?: 'No description'); ?></p>
            <p><strong>Status:</strong> <span class="badge text-bg-secondary"><?php echo e(ucfirst($task->status)); ?></span></p>
            <div class="mb-2">
              <div class="d-flex justify-content-between small">
                <span>Progress</span>
                <span><?php echo e($task->progress ?? 0); ?>%</span>
              </div>
              <div class="progress" style="height:10px;">
                <div class="progress-bar" role="progressbar" style="width: <?php echo e($task->progress ?? 0); ?>%;" aria-valuenow="<?php echo e($task->progress ?? 0); ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
            <p><strong>Due Date:</strong> <?php echo e($task->due_date ? $task->due_date->format('d M Y') : 'No due date'); ?></p>
            <p><strong>Assigned By:</strong> <?php echo e(optional($task->assigner)->name); ?></p>
            <p><strong>Assigned To:</strong> <?php echo e(optional($task->assignee)->name); ?></p>
            <p class="text-muted mt-2">Employee progress approval process via <a href="<?php echo e(route('tasks.progress.approvals')); ?>">Approval Progress</a> page. Progress evidence is sent via link.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\location-admin-tasks\show.blade.php ENDPATH**/ ?>