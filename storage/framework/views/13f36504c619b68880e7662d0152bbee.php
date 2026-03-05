<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Location Shift Details</h3>
        <p class="text-muted mb-0">Detail shift yang terpasang di lokasi.</p>
    </div>
    <a href="<?php echo e(route('location-shifts.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo e($location->name); ?> Shift Configuration</h3>
                            <div class="card-tools">
                                <a href="<?php echo e(route('location-shifts.edit', $location)); ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Manage Shifts
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Location Details</strong>
                                    <dl class="row mt-2">
                                        <dt class="col-sm-4">Name:</dt>
                                        <dd class="col-sm-8"><?php echo e($location->name); ?></dd>

                                        <dt class="col-sm-4">Code:</dt>
                                        <dd class="col-sm-8"><?php echo e($location->code); ?></dd>

                                        <dt class="col-sm-4">Address:</dt>
                                        <dd class="col-sm-8"><?php echo e($location->address); ?></dd>

                                        <dt class="col-sm-4">Timezone:</dt>
                                        <dd class="col-sm-8"><?php echo e($location->timezone); ?></dd>

                                        <dt class="col-sm-4">Status:</dt>
                                        <dd class="col-sm-8">
                                            <?php if($location->is_active): ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Inactive</span>
                                            <?php endif; ?>
                                        </dd>
                                    </dl>
                                </div>

                                <div class="col-md-6">
                                    <strong>Shift Information</strong>
                                    <div class="mt-2">
                                        <p><strong>Total Assigned Shifts:</strong> <?php echo e($location->shifts->count()); ?></p>

                                        <?php if($location->shifts->count() > 0): ?>
                                            <strong>Assigned Shifts:</strong>
                                            <div class="mt-2">
                                                <?php $__currentLoopData = $location->shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="card card-outline card-info mb-2">
                                                        <div class="card-header p-2">
                                                            <h6 class="card-title mb-0">
                                                                <?php echo e($shift->name); ?> (<?php echo e($shift->code); ?>)
                                                            </h6>
                                                        </div>
                                                        <div class="card-body p-2">
                                                            <small>
                                                                <?php
                                                                    $slots = $shift->pivot->time_slots ?? [];
                                                                    if (isset($slots['start'], $slots['end'])) { $slots = [ $slots ]; }
                                                                    $dayNames = [
                                                                        0 => 'Minggu',
                                                                        1 => 'Senin',
                                                                        2 => 'Selasa',
                                                                        3 => 'Rabu',
                                                                        4 => 'Kamis',
                                                                        5 => 'Jumat',
                                                                        6 => 'Sabtu',
                                                                    ];
                                                                ?>
                                                                <strong>Schedule:</strong> <?php echo e($shift->getFormattedSchedule()); ?><br>
                                                                <?php if(!empty($slots)): ?>
                                                                    <strong>Detail per Hari:</strong>
                                                                    <ul class="mb-1 ps-3">
                                                                        <?php $__currentLoopData = $slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <?php
                                                                    $days = isset($slot['days']) && is_array($slot['days']) && count($slot['days']) > 0
                                                                                    ? collect($slot['days'])->map(function($d) use ($dayNames) {
                                                                                        // support numeric index (0-6) atau string nama hari
                                                                                        if (is_numeric($d)) {
                                                                                            return $dayNames[(int)$d] ?? '';
                                                                                        }
                                                                                        return ucfirst($d);
                                                                                    })->filter()->implode(', ')
                                                                                    : 'Semua hari';
                                                                                $start = $slot['start'] ?? '?';
                                                                                $end = $slot['end'] ?? '?';
                                                                            ?>
                                                                            <li><?php echo e($days); ?>: <?php echo e($start); ?> - <?php echo e($end); ?></li>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </ul>
                                                                <?php endif; ?>
                                                                <?php if($shift->day): ?>
                                                                    <strong>Day:</strong> <?php echo e($shift->getDayName()); ?><br>
                                                                <?php endif; ?>
                                                                <strong>Type:</strong> <?php echo e(ucfirst($shift->shift_type)); ?><br>
                                                                <strong>Category:</strong> <?php echo e(strtoupper(str_replace('_',' ', $shift->pivot->category ?? $shift->category))); ?><br>
                                                                <strong>Status:</strong>
                                                                <?php if($shift->is_active): ?>
                                                                    <span class="badge badge-success">Active</span>
                                                                <?php else: ?>
                                                                    <span class="badge badge-danger">Inactive</span>
                                                                <?php endif; ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning mt-2">
                                                <i class="icon fas fa-exclamation-triangle"></i>
                                                No shifts are currently assigned to this location.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <a href="<?php echo e(route('location-shifts.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <a href="<?php echo e(route('location-shifts.edit', $location)); ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Manage Shifts
                            </a>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/location-shifts/show.blade.php ENDPATH**/ ?>