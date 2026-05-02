

<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Location Details</h3>
        <p class="text-muted mb-0"><?php echo e($location->name); ?></p>
    </div>
    <a href="<?php echo e(route('locations.index')); ?>" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Location Information</h3>
                            <div class="card-tools">
                                <?php if(auth()->user()->hasRole('Super Admin')): ?>
                                <a href="<?php echo e(route('locations.settings', $location)); ?>" class="btn btn-outline-secondary btn-sm me-1">
                                    <i class="bi bi-gear"></i> Settings
                                </a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('locations.edit', $location)); ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-3">ID</dt>
                                <dd class="col-sm-9"><?php echo e($location->id); ?></dd>

                                <dt class="col-sm-3">Name</dt>
                                <dd class="col-sm-9"><?php echo e($location->name); ?></dd>

                                <dt class="col-sm-3">Code</dt>
                                <dd class="col-sm-9">
                                    <span class="badge badge-light code-badge border"><?php echo e($location->code); ?></span>
                                </dd>

                                <dt class="col-sm-3">Address</dt>
                                <dd class="col-sm-9"><?php echo e($location->address ?: '-'); ?></dd>

                                <dt class="col-sm-3">Timezone</dt>
                                <dd class="col-sm-9"><?php echo e($location->timezone); ?></dd>

                                <dt class="col-sm-3">Latitude / Longitude</dt>
                                <dd class="col-sm-9"><?php echo e($location->latitude ?? '-'); ?>, <?php echo e($location->longitude ?? '-'); ?></dd>

                                <dt class="col-sm-3">Radius</dt>
                                <dd class="col-sm-9"><?php echo e($location->radius ? ($location->radius . ' m') : '-'); ?></dd>

                                <dt class="col-sm-3">Default Location</dt>
                                <dd class="col-sm-9">
                                    <?php if($location->is_default): ?>
                                        <span class="badge badge-primary">Default</span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </dd>

                                <dt class="col-sm-3">Shift Enabled</dt>
                                <dd class="col-sm-9">
                                    <?php if($location->shift_enabled): ?>
                                        <span class="badge badge-success">Yes</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">No</span>
                                    <?php endif; ?>
                                </dd>

                                <dt class="col-sm-3">Schedule Type</dt>
                                <dd class="col-sm-9">
                                    <?php if($location->schedule_type): ?>
                                        <span class="badge badge-info"><?php echo e(ucfirst($location->schedule_type)); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </dd>

                                <?php if($location->daily_schedule): ?>
                                <dt class="col-sm-3">Daily Schedule</dt>
                                <dd class="col-sm-9">
                                    <pre class="bg-light p-2 rounded"><?php echo e(json_encode($location->daily_schedule, JSON_PRETTY_PRINT)); ?></pre>
                                </dd>
                                <?php endif; ?>

                                <dt class="col-sm-3">Status</dt>
                                <dd class="col-sm-9">
                                    <?php if($location->is_active): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Inactive</span>
                                    <?php endif; ?>
                                </dd>

                                <dt class="col-sm-3">Created At</dt>
                                <dd class="col-sm-9"><?php echo e($location->created_at->format('d M Y H:i')); ?></dd>

                                <dt class="col-sm-3">Updated At</dt>
                                <dd class="col-sm-9"><?php echo e($location->updated_at->format('d M Y H:i')); ?></dd>
                            </dl>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>

                <div class="col-md-4">
                    <!-- Users at this location -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Users at this Location</h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if($location->users->count() > 0): ?>
                                <ul class="list-group list-group-flush">
                                    <?php $__currentLoopData = $location->users->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><?php echo e($user->name); ?></span>
                                            <small class="text-muted"><?php echo e($user->role); ?></small>
                                        </div>
                                    </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($location->users->count() > 5): ?>
                                    <li class="list-group-item text-center">
                                        <small class="text-muted">And <?php echo e($location->users->count() - 5); ?> more...</small>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            <?php else: ?>
                                <div class="card-body">
                                    <p class="text-muted text-center">No users assigned to this location</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent Attendances -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Attendances</h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if($location->attendances->count() > 0): ?>
                                <ul class="list-group list-group-flush">
                                    <?php $__currentLoopData = $location->attendances->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?php echo e($attendance->user->name); ?></strong><br>
                                                <small class="text-muted"><?php echo e(method_exists($attendance, 'getAttribute') && $attendance->check_in_time ? $attendance->check_in_time->format('d M Y H:i') : ''); ?></small>
                                                <?php if(!empty($attendance->location)): ?>
                                                    <?php $p = explode(',', $attendance->location); ?>
                                                    <?php if(count($p) === 2): ?>
                                                        <?php $plat = trim($p[0]); $plng = trim($p[1]); ?>
                                                        <br><small><a href="https://www.google.com/maps?q=<?php echo e($plat); ?>,<?php echo e($plng); ?>" target="_blank" rel="noopener">View on Map</a></small>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            <?php if($attendance->status === 'present'): ?>
                                                <span class="badge badge-success">Present</span>
                                            <?php elseif($attendance->status === 'late'): ?>
                                                <span class="badge badge-warning">Late</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary"><?php echo e(ucfirst($attendance->status)); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($location->attendances->count() > 5): ?>
                                    <li class="list-group-item text-center">
                                        <small class="text-muted">And <?php echo e($location->attendances->count() - 5); ?> more...</small>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            <?php else: ?>
                                <div class="card-body">
                                    <p class="text-muted text-center">No attendances recorded</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <a href="<?php echo e(route('locations.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Locations
                    </a>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\locations\show.blade.php ENDPATH**/ ?>