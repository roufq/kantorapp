<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Detail Lembur</h3>
        <p class="text-muted mb-0">Pengajuan lembur tanggal <?php echo e($overtime->date->format('d M Y')); ?></p>
    </div>
    <a href="<?php echo e(route('overtime.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Overtime Request Details</h3>
                <div class="card-tools d-flex gap-2">
                    <?php if(auth()->user()->hasRole('Karyawan') && $overtime->user_id === auth()->id() && $overtime->status === 'pending'): ?>
                        <a href="<?php echo e(route('overtime.edit', $overtime)); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Employee:</strong> <?php echo e($overtime->user->name); ?></p>
                        <p><strong>Date:</strong> <?php echo e($overtime->date->format('d M Y')); ?></p>
                        <p><strong>Time:</strong> <?php echo e($overtime->start_time_wib); ?> - <?php echo e($overtime->end_time_wib); ?> WIB</p>
                        <p><strong>Duration:</strong> <?php echo e(number_format($overtime->duration_minutes, 0)); ?> minutes</p>
                        <p><strong>Reason:</strong> <?php echo e($overtime->reason); ?></p>
                        <p><strong>Status:</strong>
                            <span class="badge
                                <?php if($overtime->status === 'approved'): ?> bg-success
                                <?php elseif($overtime->status === 'rejected'): ?> bg-danger
                                <?php else: ?> bg-warning
                                <?php endif; ?>">
                                <?php echo e(ucfirst($overtime->status)); ?>

                            </span>
                        </p>
                        <p><strong>Created:</strong> <?php echo e($overtime->created_at->format('d M Y H:i')); ?></p>
                        <p><strong>Updated:</strong> <?php echo e($overtime->updated_at->format('d M Y H:i')); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Approval Status</h5>
                        <?php $__currentLoopData = $overtime->selected_masters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $masterId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $master = \App\Models\User::find($masterId);
                                $approval = $overtime->approvals->where('master_id', $masterId)->first();
                            ?>
                            <div class="mb-3 p-3 border rounded">
                                <strong><?php echo e($master->name); ?></strong><br>
                                <span class="badge
                                    <?php if($approval && $approval->status === 'approved'): ?> bg-success
                                    <?php elseif($approval && $approval->status === 'rejected'): ?> bg-danger
                                    <?php else: ?> bg-secondary
                                    <?php endif; ?>">
                                    <?php echo e($approval ? ucfirst($approval->status) : 'Pending'); ?>

                                </span>
                                <?php if($approval && $approval->approved_at): ?>
                                    <br><small class="text-muted">Approved at: <?php echo e($approval->approved_at->format('d M Y H:i')); ?></small>
                                <?php endif; ?>
                                <?php if($approval && $approval->notes): ?>
                                    <br><small><strong>Notes:</strong> <?php echo e($approval->notes); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(auth()->user()->hasAnyRole(['Super Admin', 'Admin Lokasi']) && in_array(auth()->id(), $overtime->selected_masters)): ?>
    <?php
        $userApproval = $overtime->approvals->where('master_id', auth()->id())->first();
    ?>
    <?php if($userApproval && $userApproval->status === 'pending'): ?>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Make Decision</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('overtime.approve', $overtime)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <div class="mb-3">
                                <label class="form-label">Decision</label>
                                <select name="status" class="form-select" required>
                                    <option value="approved">Approve</option>
                                    <option value="rejected">Reject</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes (Optional)</label>
                                <textarea name="notes" class="form-control" rows="3" name="notes" placeholder="Add any notes..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Decision</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\overtime\show.blade.php ENDPATH**/ ?>