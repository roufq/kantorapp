

<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Update Progress: <?php echo e($task->title); ?></h3>
        <p class="text-muted mb-0">Submit slot progress evidence for this task.</p>
    </div>
    <a href="<?php echo e(route('tasks.show', $task)); ?>" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Update Progress per Slot</h4>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Submit evidence per slot as a link. Task progress will be automatically calculated from approved slots.</p>
                <?php
                    $slotStatus = $task->getSlotCompositionStatus();
                    $missingPercent = max(0, round(100 - $slotStatus['total_percent'], 2));
                    $missingMinutes = $slotStatus['duration_minutes'] !== null
                        ? max(0, $slotStatus['duration_minutes'] - $slotStatus['total_minutes'])
                        : null;
                    $slotIncomplete = !$slotStatus['complete'];
                    $slotAlertMessage = 'Slot composition is incomplete. ';
                    if ($slotStatus['duration_minutes'] !== null) {
                        $slotAlertMessage .= 'Missing ' . $missingPercent . '% and ' . $missingMinutes . ' minutes. Complete slots first.';
                    } else {
                        $slotAlertMessage .= 'Missing ' . $missingPercent . '%. Complete slots first.';
                    }
                ?>
                <?php if($slotIncomplete): ?>
                    <div class="alert alert-warning">
                        <?php echo e($slotAlertMessage); ?>

                    </div>
                <?php endif; ?>
                <?php if($task->slots->isEmpty()): ?>
                    <div class="alert alert-warning mb-0">
                        No slots for this task yet. Contact Location Admin/Super Admin to add slots before submitting progress.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Slot Progress</h5>
            </div>
            <div class="card-body">
                <?php $__empty_1 = true; $__currentLoopData = $task->slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold"><?php echo e($slot->name); ?> (<?php echo e($slot->percentage); ?>% | <?php echo e($slot->minutes); ?> minutes)</div>
                                <?php
                                    $statusClass = $slot->status === 'approved'
                                        ? 'badge badge-success'
                                        : ($slot->status === 'rejected' ? 'badge badge-danger' : 'badge badge-warning');
                                ?>
                                <div class="small text-muted">Status: <span class="<?php echo e($statusClass); ?>"><?php echo e(ucfirst($slot->status)); ?></span></div>
                                <?php if($slot->rejection_reason): ?>
                                    <div class="text-danger small">Reject reason: <?php echo e($slot->rejection_reason); ?></div>
                                <?php endif; ?>
                            </div>
                            <?php
                                $slotCanSubmit = ($slot->status === 'rejected') || ($slot->status === 'pending' && $slot->attachments->where('type','link')->isEmpty());
                            ?>
                            <div class="d-flex align-items-start gap-2 flex-wrap">
                                <div class="fw-semibold small mb-1">Attachments</div>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php $linkAttachments = $slot->attachments->where('type','link'); ?>
                                    <?php $__empty_2 = true; $__currentLoopData = $linkAttachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                        <a href="<?php echo e($att->path_or_url); ?>" target="_blank" class="btn btn-sm btn-outline-primary">Link <?php echo e($loop->iteration); ?></a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                        <span class="text-muted small">No attachments yet.</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php
                            $slotCanSubmit = ($slot->status === 'rejected') || ($slot->status === 'pending' && $slot->attachments->where('type','link')->isEmpty());
                        ?>
                        <div class="mt-2">
                            <?php if($slotCanSubmit): ?>
                                <form action="<?php echo e(route('task-slots.submit', $slot)); ?>" method="POST" class="row g-2 align-items-end" <?php if($slotIncomplete): ?> onsubmit="alert('<?php echo e($slotAlertMessage); ?>'); return false;" <?php endif; ?>>
                                    <?php echo csrf_field(); ?>
                                    <div class="col-md-6">
                                        <label class="form-label">Link</label>
                                        <input type="url" name="link" class="form-control form-control-sm" placeholder="https://" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Notes</label>
                                        <textarea name="note" class="form-control form-control-sm" rows="1" placeholder="Optional"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-sm btn-primary">Submit Slot Evidence</button>
                                        <small class="text-muted ms-2">Slot evidence must be a link.</small>
                                    </div>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-light border small mb-0">
                                    <?php if($slot->status === 'approved'): ?>
                                        Slot has been approved.
                                    <?php else: ?>
                                        Evidence submitted and awaiting approval. Resubmit only if rejected.
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted mb-0">No slot progress yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Progress & Approval History (Legacy)</h5>
            </div>
            <div class="card-body">
                <?php $__empty_1 = true; $__currentLoopData = $progressUpdates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $update): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold">Progress <?php echo e($update->progress); ?>%</div>
                                <div class="small text-muted">Submitted by <?php echo e($update->user->name); ?> @ <?php echo e($update->created_at->format('d M Y H:i')); ?></div>
                                <div class="mt-1">
                                    <span class="badge text-bg-<?php echo e($update->approval_status === 'approved' ? 'success' : ($update->approval_status === 'rejected' ? 'danger' : 'warning')); ?>">
                                        <?php echo e(ucfirst($update->approval_status)); ?>

                                    </span>
                                    <?php if($update->approval_level !== 'none'): ?>
                                        <span class="badge text-bg-light text-muted">Target: <?php echo e(ucfirst(str_replace('_', ' ', $update->approval_level))); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php
                                $canInlineApprove = $update->approval_status === 'pending' && (
                                    auth()->user()->hasRole('Super Admin') ||
                                    (auth()->user()->hasRole('Location Admin') && optional($update->task->assignee)->location_id === auth()->user()->location_id) ||
                                    $update->approved_by === auth()->id()
                                );
                            ?>
                            <?php if($canInlineApprove): ?>
                                <div class="d-flex align-items-start gap-2 flex-wrap">
                                    <form action="<?php echo e(route('tasks.progress.approve', $update)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                    <form action="<?php echo e(route('tasks.progress.reject', $update)); ?>" method="POST" class="d-flex align-items-start gap-2">
                                        <?php echo csrf_field(); ?>
                                        <textarea name="reason" class="form-control form-control-sm" placeholder="Reject reason (required)" rows="2" required style="min-width: 180px;"></textarea>
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Reject</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if($update->note): ?>
                            <div class="mt-2 small"><strong>Notes:</strong> <?php echo e($update->note); ?></div>
                        <?php endif; ?>
                        <div class="mt-2 text-muted small">Legacy attachments are hidden (only links are used going forward).</div>
                        <?php if($update->approval_status === 'rejected' && $update->rejection_reason): ?>
                            <div class="mt-2 text-danger small"><strong>Rejection reason:</strong> <?php echo e($update->rejection_reason); ?></div>
                        <?php endif; ?>
                        <?php if($update->approver): ?>
                            <div class="mt-1 small text-muted">Processed by <?php echo e($update->approver->name); ?> @ <?php echo e(optional($update->approved_at)->format('d M Y H:i')); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted mb-0">No progress history yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\tasks\progress\create.blade.php ENDPATH**/ ?>