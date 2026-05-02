<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <div class="content pt-4">
        <div class="container-fluid">
            <!-- Header section -->
            <div class="row mb-5 align-items-center">
                <div class="col-lg-7">
                    <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        <?php echo e(__('Personal Profile')); ?>

                    </h1>
                    <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Manage your account settings and regional preferences.')); ?></p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Profile Avatar & Quick Stats -->
                <div class="col-xl-4 col-lg-5">
                    <div class="card p-4 text-center border-0 shadow-sm h-100">
                        <?php
                            $avatarPath = $user->profile_photo_path;
                            $avatarUrl = asset('assets/img/user2-160x160.jpg');
                            if ($avatarPath) {
                                $normalized = str_replace('\\','/',$avatarPath);
                                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($normalized)) {
                                    $avatarUrl = asset('storage/' . ltrim($normalized, '/'));
                                }
                            }
                        ?>
                        <div class="mb-4">
                            <div class="d-inline-block position-relative">
                                <img src="<?php echo e($avatarUrl); ?>" class="rounded-circle shadow-lg border-4 border-white" alt="User avatar" width="140" height="140" style="object-fit: cover;">
                                <div class="position-absolute bottom-0 end-0 bg-success border-4 border-white rounded-circle shadow-sm" style="width: 25px; height: 25px;"></div>
                            </div>
                        </div>
                        
                        <h4 class="fw-bold text-dark mb-1"><?php echo e($user->name); ?></h4>
                        <div class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-bold mb-4">
                            <?php echo e($roles->implode(', ')); ?>

                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-4">
                                    <div class="text-muted smaller fw-bold mb-1">MEMBERSHIP</div>
                                    <div class="text-dark fw-bold"><?php echo e($user->created_at->format('M Y')); ?></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-4">
                                    <div class="text-muted smaller fw-bold mb-1">LOCATION</div>
                                    <div class="text-dark fw-bold"><?php echo e($user->location?->name ?? '—'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 border-top border-light mt-2">
                             <?php if(session('success')): ?>
                                <div class="alert soft-card-mint border-0 text-success p-2 small mb-3"><?php echo e(session('success')); ?></div>
                             <?php endif; ?>
                             <form action="<?php echo e(route('profile.photo')); ?>" method="POST" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold text-uppercase d-block mb-3"><?php echo e(__('Change Avatar')); ?></label>
                                    <input type="file" name="photo" class="form-control rounded-pill border-light shadow-none bg-light p-2" required>
                                </div>
                                <button class="btn btn-dark rounded-pill w-100 py-3 fw-bold shadow-sm" type="submit">
                                    <i class="mdi mdi-camera-plus-outline me-2"></i> <?php echo e(__('Update Photo')); ?>

                                </button>
                             </form>
                        </div>
                    </div>
                </div>

                <!-- Detailed Account Info -->
                <div class="col-xl-8 col-lg-7">
                    <div class="card p-4 border-0 shadow-sm mb-4">
                        <h5 class="text-dark fw-bold mb-4 pb-2 border-bottom border-light">
                            <i class="mdi mdi-account-card-outline text-info me-2"></i><?php echo e(__('Personal Information')); ?>

                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="p-3 soft-card-celeste rounded-4 border-0">
                                    <label class="text-muted smaller fw-bold d-block mb-1"><?php echo e(__('Full Name')); ?></label>
                                    <div class="text-dark fw-bold fs-6"><?php echo e($user->name); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 soft-card-celeste rounded-4 border-0">
                                    <label class="text-muted smaller fw-bold d-block mb-1"><?php echo e(__('Official Email')); ?></label>
                                    <div class="text-dark fw-bold fs-6"><?php echo e($user->email); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 soft-card-celeste rounded-4 border-0">
                                    <label class="text-muted smaller fw-bold d-block mb-1"><?php echo e(__('Phone Number')); ?></label>
                                    <div class="text-dark fw-bold fs-6"><?php echo e($user->phone_number ?? 'Not provided'); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 soft-card-celeste rounded-4 border-0">
                                    <label class="text-muted smaller fw-bold d-block mb-1"><?php echo e(__('Managed Locations')); ?></label>
                                    <div class="text-dark fw-bold fs-6"><?php echo e($user->location?->name ?? 'None'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Section -->
                    <div class="card p-4 border-0 shadow-sm mb-4">
                        <h5 class="text-dark fw-bold mb-4 pb-2 border-bottom border-light">
                            <i class="mdi mdi-lock-reset text-warning me-2"></i><?php echo e(__('Account Security')); ?>

                        </h5>
                        <form action="<?php echo e(route('profile.password.update')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold text-uppercase"><?php echo e(__('Current Password')); ?></label>
                                    <input type="password" name="current_password" class="form-control rounded-pill border-light" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold text-uppercase"><?php echo e(__('New Password')); ?></label>
                                    <input type="password" name="password" class="form-control rounded-pill border-light" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold text-uppercase"><?php echo e(__('Confirm New')); ?></label>
                                    <input type="password" name="password_confirmation" class="form-control rounded-pill border-light" required>
                                </div>
                                <div class="col-12 text-end mt-4">
                                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-lg">
                                        <i class="mdi mdi-shield-check-outline me-2"></i><?php echo e(__('Change Password')); ?>

                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Active Sessions -->
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-monitor-multiple text-danger me-2"></i><?php echo e(__('Active Device Sessions')); ?></h5>
                            <span class="badge bg-light text-muted border px-3"><?php echo e(count($sessions)); ?> Devices</span>
                        </div>
                        <div class="card-body p-0">
                            <?php if(count($sessions) > 0): ?>
                                <div class="list-group list-group-flush">
                                    <?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="list-group-item p-4 d-flex justify-content-between align-items-center bg-transparent">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3 border rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                                    <i class="mdi mdi-laptop-account fs-4 text-muted"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark"><?php echo e($session->ip_address); ?></div>
                                                    <div class="text-muted smaller text-truncate" style="max-width: 300px;"><?php echo e($session->user_agent); ?></div>
                                                    <div class="text-info smallest fw-bold mt-1">
                                                        <i class="mdi mdi-clock-outline me-1"></i>Last active: <?php echo e(\Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans()); ?>

                                                        <?php if($session->id === request()->session()->getId()): ?>
                                                            <span class="badge bg-success bg-opacity-10 text-success ms-2 border-0">CURRENTLY ACTIVE</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if($session->id !== request()->session()->getId()): ?>
                                                <form action="<?php echo e(route('profile.session.logout', $session->id)); ?>" method="POST">
                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-4 fw-bold">Logout</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .smaller { font-size: 0.75rem; }
    .smallest { font-size: 0.65rem; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\profile\show.blade.php ENDPATH**/ ?>