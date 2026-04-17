<?php $__env->startSection('content'); ?>
<div class="row mb-5 align-items-center">
    <div class="col-lg-6">
        <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo e(__('Operational Pipeline')); ?>

        </h1>
        <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Track active workflows, project milestones, and team deliverables.')); ?></p>
    </div>
    <div class="col-lg-6 text-lg-end mt-4 mt-lg-0">
        <div class="d-flex justify-content-lg-end flex-wrap gap-2">
            <?php if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Location Admin')): ?>
                <a href="<?php echo e(route('tasks.create')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-soft py-2">
                    <i class="mdi mdi-plus me-1"></i><?php echo e(__('Initiate Task')); ?>

                </a>
                <a href="<?php echo e(route('tasks.progress.approvals')); ?>" class="btn btn-outline-light text-dark border bg-white rounded-pill px-4 fw-bold shadow-soft py-2">
                    <i class="mdi mdi-check-decagram-outline me-1 text-warning"></i><?php echo e(__('Review Queue')); ?>

                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('tasks.create.self')); ?>" class="btn btn-outline-light text-dark border bg-white rounded-pill px-4 fw-bold shadow-soft py-2">
                <i class="mdi mdi-account-star-outline me-1 text-primary"></i><?php echo e(__('Personal Log')); ?>

            </a>
        </div>
    </div>
</div>

<!-- Search & Refined Control -->
<div class="mb-4">
    <form method="GET" action="<?php echo e(route('tasks.index')); ?>" id="task-filter-form">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-8 col-lg-9">
                <div class="card shadow-soft border-light p-2 rounded-pill d-flex flex-row align-items-center bg-white mb-0">
                    <i class="mdi mdi-magnify fs-4 ms-3 text-muted"></i>
                    <input type="text" name="search" class="form-control border-0 bg-transparent text-dark shadow-none px-3 fw-bold" style="font-size: 1rem; height: 3.2rem;" placeholder="<?php echo e(__('Search operations...')); ?>" value="<?php echo e(request('search')); ?>">
                </div>
            </div>
            <div class="col-12 col-md-4 col-lg-3">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light rounded-pill border py-3 px-4 flex-grow-1 fw-bold shadow-none" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                        <i class="mdi mdi-filter-variant me-2"></i> <?php echo e(__('Filters')); ?>

                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill py-3 px-4 fw-bold shadow-soft"><i class="mdi mdi-magnify"></i></button>
                </div>
            </div>
        </div>

        <div class="collapse mt-3" id="advancedFilters">
            <div class="card shadow-soft border-light p-4 rounded-4 bg-white">
                <div class="row g-4">
                    <div class="col-md-3">
                        <label class="form-label text-muted status-badge mb-2 d-block ms-1"><?php echo e(__('Timeline Alpha')); ?></label>
                        <input type="date" name="start_date" class="form-control rounded-pill border-light" value="<?php echo e(request('start_date')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted status-badge mb-2 d-block ms-1"><?php echo e(__('Timeline Omega')); ?></label>
                        <input type="date" name="end_date" class="form-control rounded-pill border-light" value="<?php echo e(request('end_date')); ?>">
                    </div>
                    <?php if(auth()->user()->hasRole('Super Admin')): ?>
                        <div class="col-md-3">
                            <label class="form-label text-muted status-badge mb-2 d-block ms-1"><?php echo e(__('Deployment Site')); ?></label>
                            <select name="location_id" class="form-select rounded-pill border-light">
                                <option value=""><?php echo e(__('Enterprise-wide')); ?></option>
                                <?php $__currentLoopData = \App\Models\Location::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($loc->id); ?>" <?php if((string)request('location_id')===(string)$loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Task Grid -->
<div class="row g-4 mb-5">
    <?php $__empty_1 = true; $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $me = auth()->user();
            $creationApproved = !($task->requires_approval ?? false) || $task->approval_status === 'approved';
            $statusBadge = match($task->status) {
                'completed' => 'badge-mint',
                'in_progress' => 'badge-indigo',
                default => 'badge-honey'
            };
            $canUpdateProgress = $creationApproved && (
                $me->hasRole('Super Admin') || $task->assigned_to === $me->id ||
                ($me->hasRole('Location Admin') && optional($task->assignee)->location_id === $me->location_id)
            );
        ?>
        <div class="col-xl-4 col-md-6">
            <div class="card shadow-soft-hover border-light h-100 rounded-5 transition-base overflow-hidden bg-white">
                <div class="card-body p-4 d-flex flex-column" style="min-height: 300px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge <?php echo e($statusBadge); ?> border-0 px-3 py-1 fw-bold status-badge">
                            <?php echo e(str_replace('_', ' ', $task->status)); ?>

                        </span>
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-0 shadow-none" type="button" data-bs-toggle="dropdown">
                                <i class="mdi mdi-dots-horizontal fs-4"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2">
                                <li><a class="dropdown-item fw-bold rounded-3 py-2 small" href="<?php echo e(route('tasks.show', $task)); ?>"><i class="mdi mdi-file-document-edit-outline me-2 text-primary"></i> Review Dataset</a></li>
                                <?php if($canUpdateProgress): ?>
                                    <li><a class="dropdown-item fw-bold rounded-3 py-2 small" href="<?php echo e(route('tasks.edit', $task)); ?>"><i class="mdi mdi-pencil-outline me-2 text-warning"></i> Modify Framework</a></li>
                                    <li><hr class="dropdown-divider opacity-10"></li>
                                    <li>
                                        <form action="<?php echo e(route('tasks.destroy', $task)); ?>" method="POST" onsubmit="return confirm('<?php echo e(__('Are you sure?')); ?>')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="dropdown-item fw-bold text-danger rounded-3 py-2 small"><i class="mdi mdi-trash-can-outline me-2"></i> Terminate Task</button>
                                        </form>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-2 text-dark text-truncate-2" style="line-height: 1.5;">
                        <a href="<?php echo e(route('tasks.show', $task)); ?>" class="text-dark text-decoration-none hover-primary"><?php echo e($task->title); ?></a>
                    </h5>
                    
                    <div class="d-flex align-items-center gap-3 mb-4 text-muted status-badge">
                        <span><i class="mdi mdi-clock-start me-1"></i><?php echo e(optional($task->created_at)->format('d M')); ?></span>
                        <?php if($task->due_date): ?>
                            <span class="<?php echo e($task->due_date->isPast() ? 'text-danger fw-bold' : ''); ?>">
                                <i class="mdi mdi-calendar-clock me-1"></i><?php echo e($task->due_date->format('d M')); ?>

                            </span>
                        <?php endif; ?>
                    </div>

                    <p class="text-muted smaller mb-4 flex-grow-1" style="opacity: 0.8; line-height: 1.6;"><?php echo e(\Illuminate\Support\Str::limit($task->description, 100)); ?></p>

                    <div class="mt-auto">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between status-badge mb-2">
                                <span class="text-muted"><?php echo e(__('DEPLOYMENT VELOCITY')); ?></span>
                                <span class="text-primary fw-800"><?php echo e($task->progress ?? 0); ?>%</span>
                            </div>
                            <div class="progress rounded-pill bg-light" style="height:6px;">
                                <div class="progress-bar rounded-pill shadow-none" role="progressbar" style="width: <?php echo e($task->progress ?? 0); ?>%; background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);"></div>
                            </div>
                        </div>

                        <div class="pt-3 border-top border-light d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold smallest text-primary" style="width: 32px; height: 32px; background-color: var(--soft-indigo);">
                                    <?php echo e(substr(optional($task->assignee)->name ?? '?', 0, 1)); ?>

                                </div>
                                <span class="fw-bold text-dark smaller"><?php echo e(optional($task->assignee)->name ?? 'Unassigned'); ?></span>
                            </div>
                            <?php if($canUpdateProgress): ?>
                                <a href="<?php echo e(route('tasks.progress.create', $task)); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-none smaller transition-base">
                                    Update
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12 text-center py-5">
            <div class="p-5">
                <i class="mdi mdi-clipboard-text-outline fs-1 text-muted opacity-25 mb-3 d-block"></i>
                <h5 class="text-muted fw-bold"><?php echo e(__('No operational tasks defined in this scope.')); ?></h5>
                <p class="text-muted smaller"><?php echo e(__('Use the "Initiate Task" button to start tracking deliverables.')); ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if($tasks->hasPages()): ?>
    <div class="d-flex justify-content-end mt-4">
        <?php echo e($tasks->appends(request()->query())->links()); ?>

    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .status-badge { font-size: 0.65rem; font-weight: 800; letter-spacing: 0.05rem; text-transform: uppercase; }
    .smaller { font-size: 0.85rem; }
    .smallest { font-size: 0.7rem; }
    .shadow-soft-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.06) !important; }
    .transition-base { transition: all 0.3s ease; }
    .hover-primary:hover { color: #0ea5e9 !important; }
    .fw-800 { font-weight: 800; }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/tasks/index.blade.php ENDPATH**/ ?>