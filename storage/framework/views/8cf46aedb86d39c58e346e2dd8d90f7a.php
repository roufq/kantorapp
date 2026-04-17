<?php $__env->startSection('content'); ?>
<div class="row mb-5 align-items-center">
    <div class="col-lg-7">
        <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo e(__('Attendance Ledger')); ?>

        </h1>
        <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Complete audit trail of verified check-ins and operational compliance.')); ?></p>
    </div>
    <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
        <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-light text-dark border bg-white rounded-pill px-4 fw-bold shadow-soft">
            <i class="mdi mdi-view-dashboard-outline me-2"></i><?php echo e(__('Dashboard')); ?>

        </a>
    </div>
</div>

<!-- Header Insight -->
<div class="card mb-4 border-light shadow-soft rounded-4 overflow-hidden bg-white">
    <div class="card-body p-4 d-flex align-items-center">
        <i class="mdi mdi-shield-check-outline text-primary me-4 fs-2"></i>
        <div>
            <div class="text-muted smaller fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Active Context')); ?></div>
            <h5 class="text-dark fw-bold mb-0">
                <?php echo e(auth()->user()->hasRole('Location Admin') ? __('Location Admin Console') : __('Personnel Ledger')); ?> 
                <span class="text-primary mx-1">|</span> 
                <?php echo e($effectiveLocation->name ?? 'Enterprise Global'); ?>

            </h5>
        </div>
    </div>
</div>

<!-- Filter Control -->
<div class="card mb-4 border-light shadow-soft rounded-4">
    <div class="card-body p-4">
        <form method="GET" action="<?php echo e(route('attendance.report')); ?>">
            <div class="row g-3 align-items-end">
                <div class="col-xl-3 col-md-6">
                    <label class="form-label text-muted smaller fw-bold text-uppercase mb-2 letter-spacing-1"><?php echo e(__('Target Personnel')); ?></label>
                    <select name="user_id" class="form-select fw-bold">
                        <option value=""><?php echo e(__('Full Directory')); ?></option>
                        <?php $__currentLoopData = \App\Models\User::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" <?php if(request('user_id') == $user->id): echo 'selected'; endif; ?>><?php echo e($user->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-xl-4 col-md-6">
                    <label class="form-label text-muted smaller fw-bold text-uppercase mb-2 letter-spacing-1"><?php echo e(__('Audit Range')); ?></label>
                    <div class="d-flex gap-2">
                        <input type="date" name="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>">
                        <input type="date" name="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>">
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold flex-grow-1 shadow-soft"><?php echo e(__('Execute Search')); ?></button>
                    <a href="<?php echo e(route('attendance.report')); ?>" class="btn btn-light border rounded-pill px-4 fw-bold text-muted"><?php echo e(__('Reset')); ?></a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="mb-4 d-flex justify-content-end">
    <a href="<?php echo e(route('attendance.export', array_merge(request()->query(), ['format' => 'xlsx']))); ?>" class="btn btn-sm btn-outline-light text-dark border bg-white rounded-pill px-4 fw-bold shadow-soft">
        <i class="mdi mdi-database-export-outline text-success me-1"></i> <?php echo e(__('Export Dataset')); ?>

    </a>
</div>

<!-- Audit Table -->
<div class="card border-light shadow-sm overflow-hidden mb-5">
    <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-dark fw-bold"><i class="mdi mdi-table-eye me-2 text-info"></i><?php echo e(__('Verified Logs')); ?></h5>
        <span class="badge badge-info"><?php echo e($attendances->total()); ?> entries found</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">No</th>
                        <th><?php echo e(__('Personnel')); ?></th>
                        <th><?php echo e(__('Duty Context')); ?></th>
                        <th class="text-center"><?php echo e(__('Timestamp (IN/OUT)')); ?></th>
                        <th class="text-center"><?php echo e(__('Compliance')); ?></th>
                        <th class="pe-4 text-end"><?php echo e(__('Administrative Verify')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover-row">
                            <td class="ps-4 text-muted fw-bold smaller"><?php echo e($loop->iteration + ($attendances->currentPage()-1)*$attendances->perPage()); ?></td>
                            <td>
                                <div class="fw-bold text-dark smaller"><?php echo e($attendance->user->name); ?></div>
                                <div class="text-muted smallest fw-bold text-uppercase letter-spacing-1"><?php echo e($attendance->user->employee->nama ?? '—'); ?></div>
                            </td>
                            <td>
                                <span class="badge badge-indigo border-0 mb-1"><?php echo e(optional($attendance->shift)->name ?? 'Standard'); ?></span>
                                <?php
                                    $rKey = $attendance->user_id . '|' . $attendance->check_in_time->toDateString();
                                    $rosterEntries = $rosterEntries ?? collect();
                                    $rCollection = $rosterEntries[$rKey] ?? collect();
                                    $rEntry = $rCollection->firstWhere('shift_assignment_id', $attendance->shift_assignment_id) ?? $rCollection->first();
                                    $slot = $rEntry?->slot_index;
                                ?>
                                <div class="text-muted smallest fw-bold">
                                    <?php if($rEntry): ?> SLOT #<?php echo e(($slot ?? 0)+1); ?> <?php else: ?> <span class="text-muted italic opacity-50">UNROSTERED</span> <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-3">
                                    <div class="text-center">
                                        <div class="badge badge-success px-3"><?php echo e($attendance->check_in_time->format('H:i')); ?></div>
                                        <div class="smallest text-muted fw-bold mt-1"><?php echo e($attendance->check_in_time->format('d M')); ?></div>
                                    </div>
                                    <div class="text-center">
                                        <?php if($attendance->check_out_time): ?>
                                            <div class="badge badge-warning px-3"><?php echo e($attendance->check_out_time->format('H:i')); ?></div>
                                            <div class="smallest text-muted fw-bold mt-1"><?php echo e($attendance->check_out_time->format('d M')); ?></div>
                                        <?php else: ?>
                                            <div class="badge badge-secondary bg-opacity-10 text-secondary border-secondary border-opacity-20 px-3">ACTIVE</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if($attendance->is_late): ?>
                                    <span class="badge badge-danger rounded-pill px-3">LATE</span>
                                <?php else: ?>
                                    <span class="badge badge-success rounded-pill px-3">ON TIME</span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 text-end">
                                <form method="POST" action="<?php echo e(route('attendance.update.approval', $attendance->id)); ?>">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <select name="approval_status" class="form-select form-select-sm rounded-pill border-light fw-bold smaller text-center" onchange="this.form.submit()" style="min-width: 110px;">
                                        <option value="pending" <?php if($attendance->approval_status == 'pending'): echo 'selected'; endif; ?>><?php echo e(__('Pending')); ?></option>
                                        <option value="approved" <?php if($attendance->approval_status == 'approved'): echo 'selected'; endif; ?>><?php echo e(__('Verify')); ?></option>
                                        <option value="rejected" <?php if($attendance->approval_status == 'rejected'): echo 'selected'; endif; ?>><?php echo e(__('Void')); ?></option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="mdi mdi-alert-circle-outline fs-1 text-muted opacity-25 mb-2 d-block"></i>
                                <span class="text-muted smaller fw-bold"><?php echo e(__('No verified logs found for the selected period.')); ?></span>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer border-top border-light p-4 bg-white d-flex justify-content-end">
        <?php echo e($attendances->links()); ?>

    </div>
</div>

<style>
    .smallest { font-size: 0.65rem; }
    .smaller { font-size: 0.75rem; }
    .letter-spacing-1 { letter-spacing: 0.5px; }
    .shadow-soft { box-shadow: 0 10px 30px rgba(0,0,0,0.03) !important; }
    .hover-row:hover { background-color: #fcfdfe !important; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/attendances/report.blade.php ENDPATH**/ ?>