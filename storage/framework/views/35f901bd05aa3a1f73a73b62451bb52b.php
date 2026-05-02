

<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">User Details</h3>
        <p class="text-muted mb-0"><?php echo e($user->name); ?></p>
    </div>
    <a href="<?php echo e(route('users.index')); ?>" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Employee Details</h3>
                <div class="card-tools">
                    <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-sm btn-primary">Edit</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> <?php echo e($user->id); ?></p>
                        <p><strong>Name:</strong> <?php echo e($user->name); ?></p>
                        <p><strong>Email:</strong> <?php echo e($user->email); ?></p>
                        <p><strong>Role:</strong>
                          <?php ($roles = $user->getRoleNames()); ?>
                          <?php if($roles->isNotEmpty()): ?>
                            <span class="badge text-bg-secondary"><?php echo e($roles->implode(', ')); ?></span>
                          <?php else: ?>
                            <span class="badge text-bg-light">-</span>
                          <?php endif; ?>
                        </p>
                        <p><strong>Location:</strong> <?php echo e($user->location ? $user->location->name : '-'); ?></p>
                        <p><strong>Created:</strong> <?php echo e($user->created_at->format('d M Y H:i')); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update-user-location', $user)): ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Transfer User to Another Location</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('users.transfer', $user)); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">New Location</label>
                            <select name="location_id" class="form-select" required>
                                <option value="" disabled selected>-- Select Location --</option>
                                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($location->id); ?>" <?php if($user->location_id == $location->id): echo 'selected'; endif; ?>><?php echo e($location->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Transfer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <?php if(auth()->user()->hasRole('Super Admin')): ?>
        <div class="card">
            <div class="card-header"><h3 class="card-title">Role Management</h3></div>
            <div class="card-body">
                <?php ($roles = $user->getRoleNames()); ?>
                <?php if($roles->contains('Location Admin')): ?>
                    <form action="<?php echo e(route('users.demote.employee', $user)); ?>" method="POST" onsubmit="return confirm('Demote this user to Employee?')">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-warning">Demote to Employee</button>
                    </form>
                <?php else: ?>
                    <form action="<?php echo e(route('users.promote.location-admin', $user)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label">Assign Location</label>
                                <select name="location_id" class="form-select" required>
                                    <option value="" disabled selected>-- Select Location --</option>
                                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($location->id); ?>" <?php if($user->location_id == $location->id): echo 'selected'; endif; ?>><?php echo e($location->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Promote to Location Admin</button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>

                <hr class="my-3" />

                <?php if($roles->contains('HR')): ?>
                    <form action="<?php echo e(route('users.demote.employee', $user)); ?>" method="POST" onsubmit="return confirm('Demote this user to Employee?')">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-warning">Demote HR to Employee</button>
                    </form>
                <?php else: ?>
                    <form action="<?php echo e(route('users.promote.hr', $user)); ?>" method="POST" onsubmit="return confirm('Promote this user to HR? This will remove Location Admin / Employee roles.')">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-outline-primary">Promote to HR</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\users\show.blade.php ENDPATH**/ ?>