

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h3 class="mb-1">Location Shifts</h3>
                    <p class="text-muted mb-0">Kelola shift yang ditetapkan ke setiap lokasi.</p>
                </div>
                <a href="<?php echo e(route('location-shifts.create')); ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Assign Shifts to Location
                </a>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Location Shifts</h3>
                        </div>
                        <!-- /.card-header -->

                        <!-- Filter Form -->
                        <div class="card-body border-bottom">
                            <form method="GET" action="<?php echo e(route('location-shifts.index')); ?>" class="form-inline">
                                <div class="form-group mr-3">
                                    <label for="location_id" class="mr-2">Filter by Location:</label>
                                    <select name="location_id" id="location_id" class="form-control form-control-sm">
                                        <option value="">All Locations</option>
                                        <?php $__currentLoopData = $allLocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($location->id); ?>" <?php echo e(request('location_id') == $location->id ? 'selected' : ''); ?>>
                                                <?php echo e($location->name); ?> (<?php echo e($location->code); ?>)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="form-group mr-3">
                                    <label for="search" class="mr-2">Search:</label>
                                    <input type="text" name="search" id="search" class="form-control form-control-sm"
                                           value="<?php echo e(request('search')); ?>" placeholder="Location name or code">
                                </div>
                                <button type="submit" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="<?php echo e(route('location-shifts.index')); ?>" class="btn btn-outline-secondary btn-sm ml-2">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </form>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th style="width:50px">No</th>
                                        <th>Location</th>
                                        <th>Code</th>
                                        <th>Assigned Shifts</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($loop->iteration + ($locations->currentPage()-1)*$locations->perPage()); ?></td>
                                            <td><?php echo e($location->name); ?></td>
                                            <td><?php echo e($location->code); ?></td>
                                            <td>
                                                <?php if($location->shifts->count() > 0): ?>
                                                    <div class="d-flex flex-wrap">
                                                        <?php $__currentLoopData = $location->shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <span class="badge badge-info mr-1 mb-1">
                                                                <?php echo e($shift->name); ?>

                                                                <a href="<?php echo e(route('location-shifts.detach-shift', [$location, $shift])); ?>"
                                                                   class="text-white ml-1"
                                                                   onclick="return confirm('Remove this shift from location?')">
                                                                    <i class="fas fa-times"></i>
                                                                </a>
                                                            </span>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">No shifts assigned</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('location-shifts.edit', $location)); ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i> Manage Shifts
                                                </a>
                                                <a href="<?php echo e(route('location-shifts.show', $location)); ?>" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No locations found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->

                        <?php if($locations->hasPages()): ?>
                            <div class="card-footer">
                                <?php echo e($locations->appends(request()->query())->links()); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/location-shifts/index.blade.php ENDPATH**/ ?>