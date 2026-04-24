

<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Location Change Requests</h3>
        <p class="text-muted mb-0">Manage employee location change requests.</p>
    </div>
    <?php if(auth()->user()->hasRole('Employee') || auth()->user()->hasRole('Location Admin') || auth()->user()->hasRole('Super Admin')): ?>
    <a href="<?php echo e(route('location-change-requests.create')); ?>" class="btn btn-outline-secondary btn-sm">
        <i class="mdi mdi-plus-circle-outline mr-1"></i> Create New Request
    </a>
    <?php endif; ?>
</div>
<div class="row">
    <div class="col-12">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
                    <div>
                        <h4 class="header-title mb-0">Requests Overview</h4>
                        <small class="text-muted">Filter and manage employee location changes</small>
                    </div>
                    
                </div>

                <form method="GET" action="<?php echo e(route('location-change-requests.index')); ?>" class="row align-items-end mb-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="mb-1 text-muted">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All</option>
                            <option value="pending" <?php echo e(request('status')=='pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="approved" <?php echo e(request('status')=='approved' ? 'selected' : ''); ?>>Approved</option>
                            <option value="rejected" <?php echo e(request('status')=='rejected' ? 'selected' : ''); ?>>Rejected</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="mb-1 text-muted">Request Date</label>
                        <input type="date" name="request_date" class="form-control" value="<?php echo e(request('request_date')); ?>">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="mb-1 text-muted">Original Location</label>
                        <select name="original_location_id" class="form-control">
                            <option value="">All</option>
                            <?php if(isset($locations)): ?>
                                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($loc->id); ?>" <?php echo e((string)request('original_location_id')===(string)$loc->id ? 'selected' : ''); ?>><?php echo e($loc->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="mb-1 text-muted">Target Location</label>
                        <select name="target_location_id" class="form-control">
                            <option value="">All</option>
                            <?php if(isset($locations)): ?>
                                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($loc->id); ?>" <?php echo e((string)request('target_location_id')===(string)$loc->id ? 'selected' : ''); ?>><?php echo e($loc->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-12 d-flex gap-2 flex-wrap mt-2">
                        <button type="submit" class="btn btn-outline-secondary waves-effect waves-light">
                            <i class="mdi mdi-filter-outline mr-1"></i> Apply
                        </button>
                        <a href="<?php echo e(route('location-change-requests.index')); ?>" class="btn btn-outline-secondary waves-effect">
                            <i class="mdi mdi-close-circle-outline mr-1"></i> Clear
                        </a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover m-0">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-uppercase small" style="width:50px">No</th>
                                <th class="text-uppercase small">Date</th>
                                <?php if(auth()->user()->hasRole('Location Admin') || auth()->user()->hasRole('Super Admin')): ?>
                                    <th class="text-uppercase small">Employee</th>
                                <?php endif; ?>
                                <th class="text-uppercase small">Original Location</th>
                                <th class="text-uppercase small">Target Location</th>
                                <th class="text-uppercase small">Reason</th>
                                <th class="text-uppercase small">Status</th>
                                <th class="text-uppercase small">Type</th>
                                <th class="text-uppercase small text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($loop->iteration + ($requests->currentPage()-1)*$requests->perPage()); ?></td>
                                <td><?php echo e(\Illuminate\Support\Carbon::parse($request->request_date)->format('Y-m-d')); ?></td>
                                <?php if(auth()->user()->hasRole('Location Admin') || auth()->user()->hasRole('Super Admin')): ?>
                                    <td><?php echo e(optional($request->user)->name ?? '-'); ?></td>
                                <?php endif; ?>
                                <td><?php echo e(optional($request->originalLocation)->name ?? '-'); ?></td>
                                <td><?php echo e(optional($request->targetLocation)->name ?? '-'); ?></td>
                                <td class="text-truncate" style="max-width: 220px;" title="<?php echo e($request->reason); ?>">
                                    <?php echo e($request->reason ?: '-'); ?>

                                </td>
                                <td>
                                    <?php ($status = $request->status); ?>
                                    <span class="badge text-bg-secondary"><?php echo e(ucfirst($status)); ?></span>
                                </td>
                                <td>
                                    <?php if($request->is_permanent): ?>
                                        <span class="badge text-bg-secondary">Permanent</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary">Temporary</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if((auth()->user()->hasRole('Location Admin') || auth()->user()->hasRole('Super Admin')) && $request->status === 'pending'): ?>
                                        <div class="d-inline-flex gap-1 flex-wrap justify-content-center">
                                            <form action="<?php echo e(route('location-change-requests.updateStatus', $request)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-outline-secondary btn-sm">Approve</button>
                                            </form>
                                            <form action="<?php echo e(route('location-change-requests.updateStatus', $request)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-outline-secondary btn-sm">Reject</button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">&mdash;</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="<?php echo e(auth()->user()->hasRole('Location Admin') || auth()->user()->hasRole('Super Admin') ? 8 : 7); ?>" class="text-center py-4 text-muted">
                                    No location change requests found.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(method_exists($requests, 'links') && $requests->hasPages()): ?>
                <div class="mt-3 d-flex justify-content-center">
                    <?php echo e($requests->links()); ?>

                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/location_change_requests/index.blade.php ENDPATH**/ ?>