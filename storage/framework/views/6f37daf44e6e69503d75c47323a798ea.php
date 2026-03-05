

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h3 class="mb-1">Lokasi</h3>
                    <p class="text-muted mb-0">Kelola data lokasi beserta statusnya.</p>
                </div>
                <a href="<?php echo e(route('locations.create')); ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah Lokasi
                </a>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Locations Management</h3>
                        </div>
                        <!-- /.card-header -->

                        <!-- Search and Filter Form -->
                        <div class="card-body border-bottom">
                            <form method="GET" action="<?php echo e(route('locations.index')); ?>" class="form-inline">
                                <div class="form-group mr-3">
                                    <input type="text" name="search" class="form-control" placeholder="Search by name or code" value="<?php echo e(request('search')); ?>">
                                </div>
                                <div class="form-group mr-3">
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Active</option>
                                        <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-secondary mr-2">
                                    <i class="bi bi-search"></i> Search
                                </button>
                                <a href="<?php echo e(route('locations.index')); ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-lg"></i> Clear
                                </a>
                            </form>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover align-middle text-nowrap">
                                <thead>
                                    <tr>
                                        <th style="width:50px">No</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Address</th>
                                        <th>Timezone</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration + ($locations->currentPage()-1)*$locations->perPage()); ?></td>
                                        <td><?php echo e($location->name); ?></td>
                                        <td>
                                            <span class="badge badge-light code-badge border"><?php echo e($location->code); ?></span>
                                        </td>
                                        <td><?php echo e($location->address ?: '-'); ?></td>
                                        <td><?php echo e($location->timezone); ?></td>
                                        <td>
                                            <?php if($location->is_active): ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="table-actions">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-gear"></i> Actions
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li>
                                                        <a class="dropdown-item" href="<?php echo e(route('locations.show', $location)); ?>">
                                                            <i class="bi bi-eye mr-2"></i>View Details
                                                        </a>
                                                    </li>
                                                    <?php if(auth()->user()->hasRole('Super Admin')): ?>
                                                    <li>
                                                        <a class="dropdown-item" href="<?php echo e(route('locations.settings', $location)); ?>">
                                                            <i class="bi bi-sliders mr-2"></i>Settings
                                                        </a>
                                                    </li>
                                                    <?php endif; ?>
                                                    <li>
                                                        <a class="dropdown-item" href="<?php echo e(route('locations.edit', $location)); ?>">
                                                            <i class="bi bi-pencil-square mr-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="<?php echo e(route('location-shifts.index')); ?>">
                                                            <i class="bi bi-clock-history mr-2"></i>Manage Shifts
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="<?php echo e(route('locations.destroy', $location)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this location?')">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="bi bi-trash mr-2"></i>Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No locations found.</td>
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

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\locations\index.blade.php ENDPATH**/ ?>