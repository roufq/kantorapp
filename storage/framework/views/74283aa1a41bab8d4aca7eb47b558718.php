<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Overtime Report</h3>
        <p class="text-muted mb-0">Rekap pengajuan lembur berdasarkan filter.</p>
    </div>
    <a href="<?php echo e(route('overtime.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><?php echo e(__('Overtime Report')); ?></div>

                <div class="card-body">
                    
                    <form method="GET" action="<?php echo e(route('overtime.report')); ?>" class="mb-4 overtime-filter">
                        <div class="form-row overtime-filter-labels">
                            <div class="col-md-3 mb-2 overtime-filter-search">
                                <label for="search" class="form-label mb-1">Search</label>
                            </div>
                            <div class="col-md-2 mb-2 overtime-filter-tight overtime-filter-center overtime-filter-status">
                                <label for="status" class="form-label mb-1">Status</label>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="start_date" class="form-label mb-1">Start Date</label>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="end_date" class="form-label mb-1">End Date</label>
                            </div>
                            <div class="col-md-3 mb-2 d-none d-md-block"></div>
                        </div>
                        <div class="form-row align-items-center overtime-filter-inputs ">
                            <div class="col-md-3 mb-2 overtime-filter-search">
                                <input type="text" name="search" id="search" class="form-control" placeholder="Reason or employee name..." value="<?php echo e(request('search')); ?>">
                            </div>
                            <div class="col-md-2 mb-2 overtime-filter-tight overtime-filter-center overtime-filter-status overtime-filter-status-shift">
                                <select name="status" id="status" class="form-control w-100 overtime-filter-status-select">
                                    <option value="">All Status</option>
                                    <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                                    <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Approved</option>
                                    <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>">
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>">
                            </div>
                            <div class="col-md-3 mb-2 d-flex align-items-end justify-content-md-end">
                                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                                <a href="<?php echo e(route('overtime.export', request()->query())); ?>" class="btn btn-success">Export to Excel</a>
                            </div>
                        </div>
                    </form>

                    
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width:50px">No</th>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Duration (Minutes)</th>
                                <th>Reason</th>
                                <th class="col-status">Status</th>
                                <th>Approvals</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $overtimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $overtime): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($loop->iteration + (method_exists($overtimes, 'currentPage') ? ($overtimes->currentPage()-1)*$overtimes->perPage() : 0)); ?></td>
                                    <td><?php echo e($overtime->user->name); ?></td>
                                    <td><?php echo e($overtime->date->format('d M Y')); ?></td>
                                    <td><?php echo e($overtime->start_time_wib); ?> - <?php echo e($overtime->end_time_wib); ?> WIB</td>
                                    <td><?php echo e(number_format($overtime->duration_minutes, 0)); ?> minutes</td>
                                    <td><?php echo e(Str::limit($overtime->reason, 50)); ?></td>
                                    <td class="col-status">
                                        <span class="badge status-badge
                                            <?php if($overtime->status === 'approved'): ?> bg-success
                                            <?php elseif($overtime->status === 'rejected'): ?> bg-danger
                                            <?php else: ?> bg-warning
                                            <?php endif; ?>">
                                            <?php echo e(ucfirst($overtime->status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php $__currentLoopData = $overtime->approvals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="mb-1">
                                                <small class="badge
                                                    <?php if($approval->status === 'approved'): ?> bg-success
                                                    <?php elseif($approval->status === 'rejected'): ?> bg-danger
                                                    <?php else: ?> bg-secondary
                                                    <?php endif; ?>">
                                                    <?php echo e($approval->master->name); ?>: <?php echo e(ucfirst($approval->status)); ?>

                                                </small>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>

                    
                    <?php echo e($overtimes->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/overtime/report.blade.php ENDPATH**/ ?>