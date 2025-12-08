<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Location Change Requests</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Location Change Requests</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div>
                                <h3 class="card-title mb-0">Requests Overview</h3>
                                <p class="text-muted small mb-0">Filter and manage employee location changes</p>
                            </div>
                            <?php if(auth()->user()->hasRole('Karyawan') || auth()->user()->hasRole('Admin Lokasi') || auth()->user()->hasRole('Super Admin')): ?>
                            <a href="<?php echo e(route('location-change-requests.create')); ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Create New Request
                            </a>
                            <?php endif; ?>
                        </div>

                        <!-- Filters -->
                        <div class="card-body border-bottom">
                            <form method="GET" action="<?php echo e(route('location-change-requests.index')); ?>" class="row g-3 align-items-end">
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label mb-1">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All</option>
                                        <option value="pending" <?php echo e(request('status')=='pending' ? 'selected' : ''); ?>>Pending</option>
                                        <option value="approved" <?php echo e(request('status')=='approved' ? 'selected' : ''); ?>>Approved</option>
                                        <option value="rejected" <?php echo e(request('status')=='rejected' ? 'selected' : ''); ?>>Rejected</option>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label mb-1">Request Date</label>
                                    <input type="date" name="request_date" class="form-control" value="<?php echo e(request('request_date')); ?>">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label mb-1">Original Location</label>
                                    <select name="original_location_id" class="form-select">
                                        <option value="">All</option>
                                        <?php if(isset($locations)): ?>
                                            <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($loc->id); ?>" <?php echo e((string)request('original_location_id')===(string)$loc->id ? 'selected' : ''); ?>><?php echo e($loc->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label mb-1">Target Location</label>
                                    <select name="target_location_id" class="form-select">
                                        <option value="">All</option>
                                        <?php if(isset($locations)): ?>
                                            <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($loc->id); ?>" <?php echo e((string)request('target_location_id')===(string)$loc->id ? 'selected' : ''); ?>><?php echo e($loc->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-12 d-flex gap-2 flex-wrap">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-funnel me-1"></i> Apply
                                    </button>
                                    <a href="<?php echo e(route('location-change-requests.index')); ?>" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i> Clear
                                    </a>
                                </div>
                            </form>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover align-middle text-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-uppercase small">Date</th>
                                        <?php if(auth()->user()->hasRole('Admin Lokasi') || auth()->user()->hasRole('Super Admin')): ?>
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
                                        <td><?php echo e(\Illuminate\Support\Carbon::parse($request->request_date)->format('Y-m-d')); ?></td>
                                        <?php if(auth()->user()->hasRole('Admin Lokasi') || auth()->user()->hasRole('Super Admin')): ?>
                                            <td><?php echo e(optional($request->user)->name ?? '-'); ?></td>
                                        <?php endif; ?>
                                        <td><?php echo e(optional($request->originalLocation)->name ?? '-'); ?></td>
                                        <td><?php echo e(optional($request->targetLocation)->name ?? '-'); ?></td>
                                        <td class="text-truncate" style="max-width: 220px;" title="<?php echo e($request->reason); ?>">
                                            <?php echo e($request->reason ?: '-'); ?>

                                        </td>
                                        <td>
                                            <?php ($status = $request->status); ?>
                                            <span class="badge text-bg-<?php echo e($status==='approved' ? 'success' : ($status==='rejected' ? 'danger' : 'warning')); ?>"><?php echo e(ucfirst($status)); ?></span>
                                        </td>
                                        <td>
                                            <?php if($request->is_permanent): ?>
                                                <span class="badge text-bg-primary">Permanent</span>
                                            <?php else: ?>
                                                <span class="badge text-bg-info">Temporary</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if((auth()->user()->hasRole('Admin Lokasi') || auth()->user()->hasRole('Super Admin')) && $request->status === 'pending'): ?>
                                                <div class="d-inline-flex gap-1 flex-wrap justify-content-center">
                                                    <form action="<?php echo e(route('location-change-requests.updateStatus', $request)); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PATCH'); ?>
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                                    </form>
                                                    <form action="<?php echo e(route('location-change-requests.updateStatus', $request)); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PATCH'); ?>
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                                    </form>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">&mdash;</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="<?php echo e(auth()->user()->hasRole('Admin Lokasi') || auth()->user()->hasRole('Super Admin') ? 8 : 7); ?>" class="text-center py-4 text-muted">
                                            No location change requests found.
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if(method_exists($requests, 'links') && $requests->hasPages()): ?>
                        <div class="card-footer d-flex justify-content-center">
                            <?php echo e($requests->links()); ?>

                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/location_change_requests/index.blade.php ENDPATH**/ ?>