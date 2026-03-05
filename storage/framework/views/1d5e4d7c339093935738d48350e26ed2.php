
<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Overtime Requests</h3>
        <p class="text-muted mb-0">Pantau permintaan lembur dan status persetujuan.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <?php if(auth()->user()->hasRole('Super Admin')): ?>
            <a href="<?php echo e(route('overtime.report')); ?>" class="btn btn-outline-secondary btn-sm">View Report</a>
        <?php endif; ?>
        <?php if(auth()->user()->hasRole('Karyawan')): ?>
            <a href="<?php echo e(route('overtime.create')); ?>" class="btn btn-primary btn-sm">Request Overtime</a>
        <?php endif; ?>
    </div>
</div>

<!-- Search Form -->
<div class="mb-4">
    <form method="GET" action="<?php echo e(route('overtime.index')); ?>" class="d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Search by reason or employee name..." value="<?php echo e(request('search')); ?>">
        <button type="submit" class="btn btn-outline-primary">Search</button>
        <?php if(request('search')): ?>
            <a href="<?php echo e(route('overtime.index')); ?>" class="btn btn-outline-secondary ms-2">Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="row">
    <?php $__currentLoopData = $overtimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $overtime): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <a href="<?php echo e(route('overtime.show', $overtime)); ?>">
                            Overtime Request - <?php echo e($overtime->date->format('d M Y')); ?>

                        </a>
                    </h5>
                    <span class="badge
                        <?php if($overtime->status === 'approved'): ?> bg-success
                        <?php elseif($overtime->status === 'rejected'): ?> bg-danger
                        <?php else: ?> bg-warning
                        <?php endif; ?>">
                        <?php echo e(ucfirst($overtime->status)); ?>

                    </span>
                </div>
                <div class="card-body">
                    <p><strong>Employee:</strong> <?php echo e($overtime->user->name); ?></p>
                    <p><strong>Time:</strong> <?php echo e($overtime->start_time_wib); ?> - <?php echo e($overtime->end_time_wib); ?> WIB (<?php echo e(number_format($overtime->duration_minutes, 0)); ?> minutes)</p>
                    <p><strong>Reason:</strong> <?php echo e(Str::limit($overtime->reason, 100)); ?></p>

                    <?php if(auth()->user()->hasAnyRole(['Super Admin', 'Admin Lokasi'])): ?>
                        <div class="mt-3">
                            <strong>Approvals:</strong>
                            <?php $__currentLoopData = $overtime->approvals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="mb-1">
                                    <span class="badge
                                        <?php if($approval->status === 'approved'): ?> bg-success
                                        <?php elseif($approval->status === 'rejected'): ?> bg-danger
                                        <?php else: ?> bg-secondary
                                        <?php endif; ?>">
                                        <?php echo e($approval->master->name); ?>: <?php echo e(ucfirst($approval->status)); ?>

                                    </span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer d-flex flex-wrap gap-2">
                    <a href="<?php echo e(route('overtime.show', $overtime)); ?>" class="btn btn-sm btn-outline-primary">View Details</a>

                    <?php if(auth()->user()->hasAnyRole(['Super Admin', 'Admin Lokasi']) && in_array(auth()->id(), $overtime->selected_masters)): ?>
                        <?php
                            $userApproval = $overtime->approvals->where('master_id', auth()->id())->first();
                        ?>
                        <?php if($userApproval && $userApproval->status === 'pending'): ?>
                            <button class="btn btn-sm btn-success ms-2" data-bs-toggle="modal" data-bs-target="#approveModal<?php echo e($overtime->id); ?>">Approve/Reject</button>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if(auth()->user()->hasRole('Karyawan') && $overtime->user_id === auth()->id() && $overtime->status === 'pending'): ?>
                        <a href="<?php echo e(route('overtime.edit', $overtime)); ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="<?php echo e(route('overtime.destroy', $overtime)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Cancel this overtime request?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Approval Modal -->
        <?php if(auth()->user()->hasAnyRole(['Super Admin', 'Admin Lokasi']) && in_array(auth()->id(), $overtime->selected_masters)): ?>
            <?php
                $userApproval = $overtime->approvals->where('master_id', auth()->id())->first();
            ?>
            <?php if($userApproval && $userApproval->status === 'pending'): ?>
                <div class="modal fade" id="approveModal<?php echo e($overtime->id); ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Approve Overtime Request</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="<?php echo e(route('overtime.approve', $overtime)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Decision</label>
                                        <select name="status" class="form-select" required>
                                            <option value="approved">Approve</option>
                                            <option value="rejected">Reject</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Notes (Optional)</label>
                                        <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Submit Decision</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<!-- Pagination -->
<?php if($overtimes->hasPages()): ?>
    <div class="d-flex justify-content-center mt-4">
        <?php echo e($overtimes->appends(request()->query())->links()); ?>

    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\overtime\index.blade.php ENDPATH**/ ?>