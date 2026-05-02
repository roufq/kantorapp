<?php
    $slotIncomplete = $slotIncomplete ?? false;
    $slotAlertMessage = $slotAlertMessage ?? '';
    $canApprove = $slot->status === 'pending' && (
        auth()->user()->hasRole('Super Admin') ||
        (auth()->user()->hasRole('Location Admin') && optional($task->assignee)->location_id === auth()->user()->location_id)
    );
    $canManage = auth()->user()->hasAnyRole(['Super Admin','Location Admin']);
    
    $statusColor = match($slot->status) {
        'approved' => 'success',
        'rejected' => 'danger',
        default => 'warning'
    };
?>

<div class="card shadow-sm border-0 p-4 rounded-4 border border-light mb-3 slot-card shadow-sm" data-percentage="<?php echo e($slot->percentage); ?>" data-minutes="<?php echo e($slot->minutes); ?>">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
        <div>
            <h6 class="text-dark fw-bold mb-1"><?php echo e($slot->name); ?> <span class="text-info ms-2">(<?php echo e($slot->percentage); ?>% | <?php echo e($slot->minutes); ?> <?php echo e(__('minutes')); ?>)</span></h6>
            <div class="d-flex align-items-center gap-2 small">
                <span class="text-muted"><?php echo e(__('Status:')); ?></span>
                <span class="badge rounded-pill bg-<?php echo e($statusColor); ?> bg-opacity-25 text-<?php echo e($statusColor); ?> fw-bold px-2 py-1">
                    <?php echo e(ucfirst($slot->status)); ?>

                </span>
                <?php if($slot->approved_at): ?>
                    <span class="text-muted italic ms-1"><?php echo e(__('by')); ?> <?php echo e(optional($slot->approver)->name); ?> @ <?php echo e($slot->approved_at->format('d M Y H:i')); ?></span>
                <?php endif; ?>
            </div>
            <?php if($slot->rejection_reason): ?>
                <div class="mt-2 text-danger smaller fw-bold border-start border-danger border-2 ps-2">
                    <i class="mdi mdi-alert-circle-outline me-1"></i><?php echo e(__('Rejection reason')); ?>: <?php echo e($slot->rejection_reason); ?>

                </div>
            <?php endif; ?>
        </div>
        
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <?php if($canApprove): ?>
                <form action="<?php echo e(route('task-slots.approve', $slot)); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 fw-bold">
                        <i class="mdi mdi-check-circle-outline me-1"></i><?php echo e(__('Approve')); ?>

                    </button>
                </form>
                <button class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#rejectForm-<?php echo e($slot->id); ?>">
                    <i class="mdi mdi-close-circle-outline me-1"></i><?php echo e(__('Reject')); ?>

                </button>
            <?php endif; ?>
            <?php if($canManage): ?>
                <form action="<?php echo e(route('tasks.slots.destroy', [$task, $slot])); ?>" method="POST" onsubmit="return confirm('<?php echo e(__('Delete this slot?')); ?>')" class="d-inline">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;" title="<?php echo e(__('Delete')); ?>">
                        <i class="mdi mdi-trash-can-outline"></i>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if($canApprove): ?>
        <div class="collapse mb-3" id="rejectForm-<?php echo e($slot->id); ?>">
            <div class="p-3 bg-danger bg-opacity-10 rounded-3 border border-danger border-opacity-25">
                <form action="<?php echo e(route('task-slots.reject', $slot)); ?>" method="POST" class="d-flex gap-2">
                    <?php echo csrf_field(); ?>
                    <textarea name="reason" class="form-control form-control-sm rounded-3 bg-dark bg-opacity-50 border-light text-white" placeholder="<?php echo e(__('Rejection reason')); ?>" rows="1" required></textarea>
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold"><?php echo e(__('Reject')); ?></button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="mb-3">
        <label class="text-muted smaller fw-bold mb-2 text-uppercase letter-spacing-1 d-block"><?php echo e(__('Link Attachments')); ?></label>
        <?php $linkAttachments = $slot->attachments->where('type','link'); ?>
        <div class="d-flex flex-wrap gap-2">
            <?php $__empty_1 = true; $__currentLoopData = $linkAttachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e($att->path_or_url); ?>" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-3 shadow-sm transition-all hover-lift">
                    <i class="mdi mdi-link-variant me-1"></i><?php echo e(__('Link')); ?> <?php echo e($loop->iteration); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <span class="text-muted smaller italic"><i class="mdi mdi-link-off me-1"></i><?php echo e(__('No attachments yet.')); ?></span>
            <?php endif; ?>
        </div>
    </div>

    <?php
        $slotCanSubmit = ($slot->status === 'rejected') || ($slot->status === 'pending' && $slot->attachments->where('type','link')->isEmpty());
    ?>
    
    <div class="mt-4 pt-3 border-top border-white border-opacity-5">
        <?php if($slotCanSubmit): ?>
            <form action="<?php echo e(route('task-slots.submit', $slot)); ?>" method="POST" class="row g-3 align-items-end" data-slot-submit="1" data-slot-alert="<?php echo e($slotAlertMessage); ?>">
                <?php echo csrf_field(); ?>
                <div class="col-md-6">
                    <label class="form-label text-muted smaller fw-bold"><?php echo e(__('Link')); ?></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-dark border-0 border-opacity-10 text-muted"><i class="mdi mdi-link"></i></span>
                        <input type="url" name="link" class="form-control bg-dark bg-opacity-50 border-light text-white px-3" placeholder="https://" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted smaller fw-bold"><?php echo e(__('Notes')); ?></label>
                    <input type="text" name="note" class="form-control form-control-sm bg-dark bg-opacity-50 border-light text-white px-3 rounded-pill" placeholder="<?php echo e(__('Optional')); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill fw-bold shadow-sm">
                        <i class="mdi mdi-cloud-upload-outline me-1"></i><?php echo e(__('Submit')); ?>

                    </button>
                </div>
            </form>
        <?php else: ?>
            <div class="p-2 px-3 bg-white bg-opacity-5 rounded-pill border border-light smaller text-center">
                <?php if($slot->status === 'approved'): ?>
                    <span class="text-success"><i class="mdi mdi-check-circle-outline me-1"></i><?php echo e(__('Slot already approved.')); ?></span>
                <?php else: ?>
                    <span class="text-info"><i class="mdi mdi-clock-outline me-1"></i><?php echo e(__('Evidence submitted and awaiting approval. Re-submit only after rejection.')); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH D:\www\kantorapp\resources\views\tasks\partials\slot-card.blade.php ENDPATH**/ ?>