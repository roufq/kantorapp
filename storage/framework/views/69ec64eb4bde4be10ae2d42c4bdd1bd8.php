<?php use Illuminate\Support\Facades\Storage; ?>


<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1 text-dark fw-bold">Task Progress Approval</h3>
        <p class="text-muted mb-0">Approve/Reject tasks and slot-based progress. Legacy progress is retained for older backlogs.</p>
    </div>
    <a href="<?php echo e(route('tasks.index')); ?>" class="btn btn-outline-secondary btn-sm shadow-sm ps-3 pe-3 rounded-pill fw-bold">
        <i class="mdi mdi-arrow-left me-1"></i> Back to Tasks
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('tasks.progress.approvals')); ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Search (title / employee)</label>
                <input type="text" name="search" class="form-control" value="<?php echo e($search); ?>" placeholder="Search title or employee name">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Location</label>
                <select name="location_id" class="form-select">
                    <option value="">All</option>
                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($loc->id); ?>" <?php echo e((string)$locationId === (string)$loc->id ? 'selected' : ''); ?>><?php echo e($loc->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Employees</label>
                <select name="assignee_id" class="form-select">
                    <option value="">All</option>
                    <?php $__currentLoopData = $assignees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>" <?php echo e((string)$assigneeId === (string)$emp->id ? 'selected' : ''); ?>><?php echo e($emp->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Apply</button>
                <a href="<?php echo e(route('tasks.progress.approvals')); ?>" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">New Task Approval (Self Assign)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Title</th>
                        <th>Assignee</th>
                        <th>Created by</th>
                        <th>Location</th>
                        <th>Due</th>
                        <th>Duration (minutes)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $pendingTaskCreations ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($task->title); ?></td>
                        <td><?php echo e(optional($task->assignee)->name); ?></td>
                        <td><?php echo e(optional($task->assigner)->name); ?></td>
                        <td><?php echo e(optional($task->assignee->location)->name ?? '-'); ?></td>
                        <td><?php echo e(optional($task->due_date)->format('d M Y') ?? '-'); ?></td>
                        <td><?php echo e($task->duration_minutes ?? '-'); ?></td>
                        <td class="d-flex gap-1 flex-wrap">
                            <a href="<?php echo e(route('tasks.show', $task)); ?>" class="btn btn-sm btn-outline-secondary">Details</a>
                            <form action="<?php echo e(route('tasks.approvals.approve', $task)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#rejectCreationModal<?php echo e($task->id); ?>">Reject</button>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-muted text-center">No self-assigned tasks pending approval.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if(!empty($pendingTasks) && $pendingTasks->count() > 0): ?>
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Pending Approval (Per Task)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Title</th>
                        <th>Assignee</th>
                        <th>Location</th>
                        <th>Due</th>
                        <th>Duration (minutes)</th>
                        <th>Slot Pending</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $pendingTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $needsTaskApproval = $task->requires_approval && $task->approval_status === 'pending';
                    ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td>
                            <?php echo e($task->title); ?>

                            <?php if($needsTaskApproval): ?>
                                <span class="badge text-bg-warning ms-2">Needs task approval</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e(optional($task->assignee)->name); ?></td>
                        <td><?php echo e(optional($task->assignee->location)->name ?? '-'); ?></td>
                        <td><?php echo e(optional($task->due_date)->format('d M Y') ?? '-'); ?></td>
                        <td><?php echo e($task->duration_minutes ?? '-'); ?></td>
                        <td><?php echo e($task->slots->count()); ?></td>
                        <td class="d-flex gap-1 flex-wrap">
                            <a href="<?php echo e(route('tasks.show', $task)); ?>" class="btn btn-sm btn-outline-secondary">Details</a>
                            <form action="<?php echo e(route('tasks.approve-slots', $task)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#rejectTaskModal<?php echo e($task->id); ?>">Reject</button>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Pending Approval (Legacy Progress)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Task</th>
                        <th>Assignee</th>
                        <th>Submitted By</th>
                        <th>Progress</th>
                        <th>Submitted At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $pendingUpdates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $update): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration + ($pendingUpdates->currentPage()-1)*$pendingUpdates->perPage()); ?></td>
                        <td><?php echo e($update->task->title); ?></td>
                        <td><?php echo e(optional($update->task->assignee)->name); ?></td>
                        <td><?php echo e($update->user->name); ?></td>
                        <td><?php echo e($update->progress); ?>%</td>
                        <td><?php echo e($update->created_at->format('d M Y H:i')); ?></td>
                        <td class="d-flex gap-1 flex-wrap">
                            <a href="<?php echo e(route('tasks.show', $update->task)); ?>" class="btn btn-sm btn-outline-secondary">Details</a>
                            <form action="<?php echo e(route('tasks.progress.approve', $update)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#rejectLegacyModal<?php echo e($update->id); ?>">Reject</button>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="text-muted text-center">No progress pending approval.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($pendingUpdates->hasPages()): ?>
            <div class="p-3">
                <?php echo e($pendingUpdates->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__currentLoopData = $pendingTaskCreations ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="rejectCreationModal<?php echo e($task->id); ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Rejection Reason</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?php echo e(route('tasks.approvals.reject', $task)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="modal-body">
          <textarea name="reason" class="form-control" rows="3" required placeholder="Write the reason here"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Reject</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__currentLoopData = $pendingTasks ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="rejectTaskModal<?php echo e($task->id); ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Task Rejection Reason</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?php echo e(route('tasks.reject-slots', $task)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="modal-body">
          <textarea name="reason" class="form-control" rows="3" required placeholder="Write the reason here"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Reject</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__currentLoopData = $pendingUpdates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $update): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="rejectLegacyModal<?php echo e($update->id); ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Progress Rejection Reason</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?php echo e(route('tasks.progress.reject', $update)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="modal-body">
          <textarea name="reason" class="form-control" rows="3" required placeholder="Write the reason here"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Reject</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<style>
  /* Gunakan backdrop ringan agar konten belakang tetap terlihat */
  .modal-backdrop.show { background-color: rgba(0, 0, 0, 0.05); opacity: 1; }
  .modal-content { border-radius: 8px; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/tasks/progress-approvals.blade.php ENDPATH**/ ?>