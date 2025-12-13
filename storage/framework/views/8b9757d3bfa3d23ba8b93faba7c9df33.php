<?php $__env->startSection('title'); ?>
<div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Tasks</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Tasks</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Tasks</h1>
    <div>
        <?php if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')): ?>
            <a href="<?php echo e(route('tasks.create')); ?>" class="btn btn-primary me-2">Assign Task</a>
            <a href="<?php echo e(route('tasks.progress.approvals')); ?>" class="btn btn-warning me-2">Approval Progress</a>
        <?php endif; ?>
        <a href="<?php echo e(route('tasks.create.self')); ?>" class="btn btn-outline-primary">Create Task for Myself</a>
    </div>
</div>

<!-- Search Form -->
<div class="mb-4 card">
  <div class="card-body">
    <form method="GET" action="<?php echo e(route('tasks.index')); ?>">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Search</label>
          <input type="text" name="search" class="form-control" placeholder="Title or assignee name" value="<?php echo e(request('search')); ?>">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Status</label>
          <select name="status" class="form-select">
            <option value="">All</option>
            <option value="pending" <?php echo e(request('status')=='pending' ? 'selected' : ''); ?>>Pending</option>
            <option value="in_progress" <?php echo e(request('status')=='in_progress' ? 'selected' : ''); ?>>In Progress</option>
            <option value="completed" <?php echo e(request('status')=='completed' ? 'selected' : ''); ?>>Completed</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Start Date (Due)</label>
          <input type="date" name="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">End Date (Due)</label>
          <input type="date" name="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>">
        </div>
        <?php if(auth()->user()->hasRole('Super Admin')): ?>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Role</label>
          <select name="assignee_role" class="form-select">
            <option value="">All</option>
            <option value="Karyawan" <?php echo e(request('assignee_role')=='Karyawan' ? 'selected' : ''); ?>>Karyawan</option>
            <option value="Admin Lokasi" <?php echo e(request('assignee_role')=='Admin Lokasi' ? 'selected' : ''); ?>>Admin Lokasi</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Location</label>
          <select name="location_id" class="form-select">
            <option value="">All</option>
            <?php $__currentLoopData = \App\Models\Location::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($loc->id); ?>" <?php echo e((string)request('location_id')===(string)$loc->id ? 'selected' : ''); ?>><?php echo e($loc->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <?php endif; ?>
      </div>
      <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-primary">Apply</button>
        <a href="<?php echo e(route('tasks.index')); ?>" class="btn btn-outline-secondary">Clear</a>
      </div>
    </form>
  </div>
</div>
<div class="row g-4">
    <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-4">
            <div class="card h-100">
                <?php
                    $me = auth()->user();
                    $needsApproval = ($me->hasAnyRole(['Super Admin','Admin Lokasi']) && ($task->pending_slots_count ?? 0) > 0);
                    $requiresCreationApproval = $task->requires_approval ?? false;
                    $creationApproved = !$requiresCreationApproval || $task->approval_status === 'approved';
                    $approvalLabel = null;
                    if ($requiresCreationApproval) {
                        if ($task->approval_status === 'pending') {
                            $approvalLabel = 'Menunggu persetujuan ' . ($task->approval_level === 'location_admin' ? 'Admin Lokasi' : 'Super Admin');
                        } elseif ($task->approval_status === 'rejected') {
                            $approvalLabel = 'Ditolak' . ($task->approval_note ? ': ' . $task->approval_note : '');
                        }
                    }
                    $canUpdateProgress = $creationApproved && (
                        $me->hasRole('Super Admin') ||
                        $task->assigned_to === $me->id ||
                        ($me->hasRole('Admin Lokasi') && optional($task->assignee)->location_id === $me->location_id)
                    );
                ?>
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <h5 class="card-title mb-0"><a href="<?php echo e(route('tasks.show', $task)); ?>"><?php echo e($task->title); ?></a></h5>
                        <div class="d-flex flex-column align-items-end gap-1">
                            <?php if($needsApproval): ?>
                                <span class="badge text-bg-warning">Butuh approval (<?php echo e($task->pending_slots_count); ?>)</span>
                            <?php endif; ?>
                            <?php if($approvalLabel): ?>
                                <span class="badge text-bg-<?php echo e($task->approval_status === 'rejected' ? 'danger' : 'warning'); ?>"><?php echo e($approvalLabel); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
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
                    <p><strong>Assigned by:</strong> <?php echo e($task->assigner->name); ?></p>
                    <p><strong>Assigned to:</strong> <?php echo e(optional($task->assignee)->name); ?></p>
                </div>
                <div class="card-footer">
                    <?php
                        $me = auth()->user();
                    ?>
                    <?php if($canUpdateProgress): ?>
                        <div class="mt-2">
                            <a href="<?php echo e(route('tasks.progress.create', $task)); ?>" class="btn btn-sm btn-primary me-1">Update Progress</a>
                            <a href="<?php echo e(route('tasks.edit', $task)); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="<?php echo e(route('tasks.destroy', $task)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    <?php elseif($requiresCreationApproval): ?>
                        <?php if($task->approval_status === 'rejected'): ?>
                            <div class="mt-2 text-danger small">Tugas Anda ditolak: <?php echo e($task->approval_note ?? 'Alasan tidak tersedia.'); ?></div>
                        <?php else: ?>
                            <div class="mt-2 text-muted small">Menunggu persetujuan sebelum progres bisa diupdate.</div>
                        <?php endif; ?>
                    <?php endif; ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/tasks/index.blade.php ENDPATH**/ ?>