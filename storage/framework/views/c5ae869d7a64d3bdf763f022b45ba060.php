<?php
    $slotIncomplete = $slotIncomplete ?? false;
    $slotAlertMessage = $slotAlertMessage ?? '';
    $canApprove = $slot->status === 'pending' && (
        auth()->user()->hasRole('Super Admin') ||
        (auth()->user()->hasRole('Admin Lokasi') && optional($task->assignee)->location_id === auth()->user()->location_id)
    );
    $canManage = auth()->user()->hasAnyRole(['Super Admin','Admin Lokasi']);
?>
<?php
    $statusClass = $slot->status === 'approved'
        ? 'badge badge-success'
        : ($slot->status === 'rejected' ? 'badge badge-danger' : 'badge badge-warning');
?>
<div class="border rounded p-2 mb-3 slot-card" data-percentage="<?php echo e($slot->percentage); ?>" data-minutes="<?php echo e($slot->minutes); ?>">
    <div class="d-flex justify-content-between flex-wrap gap-2">
        <div>
            <div class="fw-semibold"><?php echo e($slot->name); ?> (<?php echo e($slot->percentage); ?>% | <?php echo e($slot->minutes); ?> menit)</div>
            <div class="small text-muted">Status:
                <span class="<?php echo e($statusClass); ?>"><?php echo e(ucfirst($slot->status)); ?></span>
                <?php if($slot->approved_at): ?>
                    <span class="text-muted">oleh <?php echo e(optional($slot->approver)->name); ?> @ <?php echo e($slot->approved_at->format('d M Y H:i')); ?></span>
                <?php endif; ?>
            </div>
            <?php if($slot->rejection_reason): ?>
                <div class="text-danger small">Alasan reject: <?php echo e($slot->rejection_reason); ?></div>
            <?php endif; ?>
        </div>
        <div class="d-flex align-items-start gap-2 flex-wrap">
            <?php if($canApprove): ?>
                <form action="<?php echo e(route('task-slots.approve', $slot)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                </form>
                <form action="<?php echo e(route('task-slots.reject', $slot)); ?>" method="POST" class="d-flex align-items-start gap-2">
                    <?php echo csrf_field(); ?>
                    <textarea name="reason" class="form-control form-control-sm" placeholder="Alasan reject" rows="1" required style="min-width: 180px;"></textarea>
                    <button type="submit" class="btn btn-outline-danger btn-sm">Reject</button>
                </form>
            <?php endif; ?>
            <?php if($canManage): ?>
                <form action="<?php echo e(route('tasks.slots.destroy', [$task, $slot])); ?>" method="POST" onsubmit="return confirm('Hapus slot ini?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Hapus</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-2">
        <div class="fw-semibold small mb-1">Lampiran Link</div>
        <?php $linkAttachments = $slot->attachments->where('type','link'); ?>
        <div class="d-flex flex-wrap gap-2">
            <?php $__empty_1 = true; $__currentLoopData = $linkAttachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e($att->path_or_url); ?>" target="_blank" class="btn btn-sm btn-outline-primary">Link <?php echo e($loop->iteration); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <span class="text-muted small">Belum ada lampiran.</span>
            <?php endif; ?>
        </div>
    </div>

    <?php
        $slotCanSubmit = ($slot->status === 'rejected') || ($slot->status === 'pending' && $slot->attachments->where('type','link')->isEmpty());
    ?>
    <div class="mt-2">
        <?php if($slotCanSubmit): ?>
            <form action="<?php echo e(route('task-slots.submit', $slot)); ?>" method="POST" class="row g-2 align-items-end" data-slot-submit="1" data-slot-alert="<?php echo e($slotAlertMessage); ?>">
                <?php echo csrf_field(); ?>
                <div class="col-12">
                    <label class="form-label">Link</label>
                    <input type="url" name="link" class="form-control form-control-sm" placeholder="https://" required>
                </div>
                <div class="col-12 mb-2">
                    <label class="form-label">Catatan</label>
                    <textarea name="note" class="form-control form-control-sm" rows="1" placeholder="Opsional"></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-sm btn-primary w-100">Submit Bukti Slot (Link)</button>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-light border small mb-0">
                <?php if($slot->status === 'approved'): ?>
                    Slot sudah disetujui.
                <?php else: ?>
                    Bukti slot sudah dikirim dan menunggu approval. Ajukan ulang hanya setelah status di-reject.
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH D:\www\kantorapp\resources\views\tasks\partials\slot-card.blade.php ENDPATH**/ ?>