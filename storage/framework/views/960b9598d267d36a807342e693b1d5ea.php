
<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Tasks</h3>
        <p class="text-muted mb-0">Pantau daftar tugas dan status progres.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <?php if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')): ?>
            <a href="<?php echo e(route('tasks.create')); ?>" class="btn btn-primary btn-sm me-2">Assign Task</a>
            <a href="<?php echo e(route('tasks.progress.approvals')); ?>" class="btn btn-warning btn-sm me-2">Approval Progress</a>
        <?php endif; ?>
        <a href="<?php echo e(route('tasks.create.self')); ?>" class="btn btn-outline-primary btn-sm">Create Task for Myself</a>
    </div>
</div>

<!-- Search & Filters -->
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="<?php echo e(route('tasks.index')); ?>">
      <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <div class="flex-grow-1">
          <label class="form-label fw-semibold d-block">Pencarian Task</label>
          <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari task berdasarkan judul, deskripsi, atau nama assignee" value="<?php echo e(request('search')); ?>">
            <div class="input-group-append d-flex gap-2">
              <button type="submit" class="btn btn-primary waves-effect waves-light">Apply</button>
              <button type="button" class="btn btn-outline-secondary waves-effect collapsed" data-toggle="collapse" data-target="#taskFiltersCollapse" aria-expanded="false" aria-controls="taskFiltersCollapse">
                <i class="mdi mdi-tune"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="collapse" id="taskFiltersCollapse">
        <div class="row g-3">
          <div class="col-md-4 col-lg-3">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-control">
              <option value="">Semua</option>
              <option value="pending" <?php echo e(request('status')=='pending' ? 'selected' : ''); ?>>Pending</option>
              <option value="in_progress" <?php echo e(request('status')=='in_progress' ? 'selected' : ''); ?>>In Progress</option>
              <option value="completed" <?php echo e(request('status')=='completed' ? 'selected' : ''); ?>>Completed</option>
            </select>
          </div>
          <div class="col-md-4 col-lg-3">
            <label class="form-label fw-semibold">Tanggal Dibuat</label>
            <input type="date" name="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>">
          </div>
          <div class="col-md-4 col-lg-3">
            <label class="form-label fw-semibold">Due Date</label>
            <input type="date" name="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>">
          </div>
          <?php if(auth()->user()->hasRole('Super Admin')): ?>
          <div class="col-md-4 col-lg-3">
            <label class="form-label fw-semibold">Role</label>
            <select name="assignee_role" class="form-control">
              <option value="">Semua</option>
              <option value="Karyawan" <?php echo e(request('assignee_role')=='Karyawan' ? 'selected' : ''); ?>>Karyawan</option>
              <option value="Admin Lokasi" <?php echo e(request('assignee_role')=='Admin Lokasi' ? 'selected' : ''); ?>>Admin Lokasi</option>
            </select>
          </div>
          <div class="col-md-4 col-lg-3">
            <label class="form-label fw-semibold">Location</label>
            <select name="location_id" class="form-control">
              <option value="">Semua</option>
              <?php $__currentLoopData = \App\Models\Location::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($loc->id); ?>" <?php echo e((string)request('location_id')===(string)$loc->id ? 'selected' : ''); ?>><?php echo e($loc->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <?php endif; ?>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3">
          <button type="submit" class="btn btn-primary waves-effect waves-light">Apply</button>
          <a href="<?php echo e(route('tasks.index')); ?>" class="btn btn-outline-secondary waves-effect">Clear</a>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="row">
    <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <h5 class="card-title mb-1">
                                <a href="<?php echo e(route('tasks.show', $task)); ?>" class="text-dark"><?php echo e($task->title); ?></a>
                            </h5>
                            <small class="text-muted d-block">
                                Dibuat: <?php echo e(optional($task->created_at)->format('d M Y')); ?> ·
                                Due: <?php echo e($task->due_date ? $task->due_date->format('d M Y') : 'No due date'); ?>

                            </small>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-1">
                            <span class="badge badge-light border text-capitalize"><?php echo e($task->status); ?></span>
                            <?php if($needsApproval): ?>
                                <span class="badge badge-warning">Butuh approval (<?php echo e($task->pending_slots_count); ?>)</span>
                            <?php endif; ?>
                            <?php if($approvalLabel): ?>
                                <span class="badge badge-<?php echo e($task->approval_status === 'rejected' ? 'danger' : 'warning'); ?>"><?php echo e($approvalLabel); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="text-muted mb-2" style="min-height: 48px;"><?php echo e(\Illuminate\Support\Str::limit($task->description, 120)); ?></p>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Progress</span>
                            <span><?php echo e($task->progress ?? 0); ?>%</span>
                        </div>
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo e($task->progress ?? 0); ?>%;" aria-valuenow="<?php echo e($task->progress ?? 0); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="mt-auto pt-2 small text-muted">
                        <div><strong>Assigned by:</strong> <?php echo e($task->assigner->name); ?></div>
                        <div><strong>Assigned to:</strong> <?php echo e(optional($task->assignee)->name ?? '-'); ?></div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <?php
                        $isOwnRejected = $task->assigned_to === $me->id && $task->approval_status === 'rejected';
                    ?>
                    <?php if($canUpdateProgress): ?>
                        <div class="d-flex flex-wrap">
                            <a href="<?php echo e(route('tasks.progress.create', $task)); ?>" class="btn btn-sm btn-primary mr-2 mb-2">Update Progress</a>
                            <a href="<?php echo e(route('tasks.edit', $task)); ?>" class="btn btn-sm btn-outline-primary mr-2 mb-2">Edit</a>
                            <form action="<?php echo e(route('tasks.destroy', $task)); ?>" method="POST" onsubmit="return confirm('Are you sure?')" class="mb-2">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    <?php elseif($requiresCreationApproval): ?>
                        <?php if($task->approval_status === 'rejected'): ?>
                            <div class="text-danger small">Tugas ditolak: <?php echo e($task->approval_note ?? 'Alasan tidak tersedia.'); ?></div>
                            <?php if($isOwnRejected): ?>
                                <div class="mt-2 d-flex flex-wrap gap-2">
                                    <a href="<?php echo e(route('tasks.edit', $task)); ?>" class="btn btn-sm btn-outline-primary">Perbaiki &amp; ajukan ulang</a>
                                    <a href="<?php echo e(route('tasks.show', $task)); ?>" class="btn btn-sm btn-outline-secondary">Lihat detail</a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-muted small">Menunggu persetujuan sebelum progres bisa diupdate.</div>
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

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\tasks\index.blade.php ENDPATH**/ ?>