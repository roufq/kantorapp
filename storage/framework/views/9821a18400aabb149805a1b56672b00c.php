<?php $__env->startSection('content'); ?>
<!-- Header Area -->
<div class="row mb-5 align-items-center">
    <div class="col-lg-7">
        <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo e(__('Attendance Terminal')); ?>

        </h1>
        <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Secure, multi-vector personnel verification protocol.')); ?></p>
    </div>
    <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
        <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-light text-dark border bg-white rounded-pill px-4 fw-bold shadow-soft">
            <i class="mdi mdi-view-dashboard-outline me-2"></i><?php echo e(__('Dashboard')); ?>

        </a>
    </div>
</div>

<!-- Deployment Insight -->
<div class="card mb-4 border-light shadow-soft rounded-4 overflow-hidden bg-white">
    <div class="card-body p-4 d-flex align-items-center">
        <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle me-4 shadow-sm">
            <i class="mdi mdi-map-marker-radius-outline fs-2"></i>
        </div>
        <div>
            <div class="text-muted status-badge mb-1"><?php echo e(__('Active Base Location')); ?></div>
            <h5 class="text-dark fw-bold mb-0">
                <?php echo e($effectiveLocation->name ?? 'Enterprise Global HQ'); ?>

                <?php if(!empty($effectiveTemporary)): ?>
                    <span class="ms-2 badge badge-warning status-badge"><i class="mdi mdi-clock-alert-outline me-1"></i><?php echo e(__('Temporary Context')); ?></span>
                <?php endif; ?>
            </h5>
        </div>
        <div class="ms-auto d-none d-md-block">
            <div class="text-end">
                <div class="text-muted status-badge mb-1"><?php echo e(__('Auth Identity')); ?></div>
                <div class="fw-bold text-dark"><?php echo e(auth()->user()->name); ?> <span class="text-primary mx-1">/</span> <span class="text-muted smaller fw-bold"><?php echo e(auth()->user()->getRoleNames()->first()); ?></span></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- Verification Main Console -->
    <div class="col-xl-5 col-lg-6">
        <div class="card border-0 shadow-soft rounded-5 h-100 bg-white">
            <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark fw-bold"><i class="mdi mdi-shield-check-outline text-success me-2"></i><?php echo e(__('Verification Unit')); ?></h5>
                <span class="badge badge-success bg-opacity-10 text-success fw-bold status-badge shadow-none"><?php echo e(__('Active')); ?></span>
            </div>
            
            <div class="card-body p-4">
                <?php if(session('success')): ?>
                    <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-4 d-flex align-items-center">
                        <div class="bg-white rounded-circle p-2 me-3 text-success shadow-sm">
                            <i class="mdi mdi-check-circle fs-4"></i>
                        </div>
                        <div class="fw-bold smaller"><?php echo e(session('success')); ?></div>
                    </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-4 d-flex align-items-center">
                        <div class="bg-white rounded-circle p-2 me-3 text-danger shadow-sm">
                            <i class="mdi mdi-alert-circle fs-4"></i>
                        </div>
                        <div class="fw-bold smaller"><?php echo e(session('error')); ?></div>
                    </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-4">
                        <ul class="mb-0 smaller fw-bold">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div id="geolocation-error" class="alert alert-danger border-0 shadow-sm rounded-4 p-3 mb-4 d-none smaller fw-bold"></div>
                <div id="geolocation-info" class="alert alert-info border-0 shadow-sm rounded-4 p-3 mb-4 d-none smaller fw-bold"></div>

                <?php if($todayAttendance): ?>
                    <div class="p-4 bg-light bg-opacity-50 rounded-5 mb-4 text-center border-light shadow-soft pulse">
                        <div class="text-muted status-badge mb-2"><?php echo e(__('Current Session Active')); ?></div>
                        <div class="text-primary h2 fw-800 mb-0 font-monospace"><?php echo e($todayAttendance->check_in_time->format('H:i:s')); ?></div>
                        <?php if($todayAttendance->check_out_time): ?>
                            <div class="mt-3 pt-3 border-top border-light">
                                <div class="text-muted status-badge mb-1"><?php echo e(__('Completion Timestamp')); ?></div>
                                <div class="text-success h2 fw-800 mb-0 font-monospace"><?php echo e($todayAttendance->check_out_time->format('H:i:s')); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if(!$todayAttendance || ($todayAttendance && !$todayAttendance->check_out_time)): ?>
                    <form id="<?php echo e(!$todayAttendance ? 'checkinForm' : 'checkoutForm'); ?>" method="POST" action="<?php echo e(!$todayAttendance ? route('attendance.checkin.post') : route('attendance.checkout')); ?>" enctype="multipart/form-data" class="pt-2">
                        <?php echo csrf_field(); ?>
                        <?php if($todayAttendance): ?> <?php echo method_field('POST'); ?> <?php endif; ?>
                        <input type="hidden" id="<?php echo e(!$todayAttendance ? 'latitude' : 'checkout_latitude'); ?>" name="latitude">
                        <input type="hidden" id="<?php echo e(!$todayAttendance ? 'longitude' : 'checkout_longitude'); ?>" name="longitude">
                        <input type="hidden" id="<?php echo e(!$todayAttendance ? 'accuracy' : 'checkout_accuracy'); ?>" name="accuracy">
                        <input type="hidden" id="<?php echo e(!$todayAttendance ? 'device_id' : 'checkout_device_id'); ?>" name="device_id">
                        <input type="hidden" name="<?php echo e(!$todayAttendance ? 'check_in_selfie_data' : 'check_out_selfie_data'); ?>" id="<?php echo e(!$todayAttendance ? 'check_in_selfie_data' : 'check_out_selfie_data'); ?>">

                        <!-- Location Sec -->
                        <div class="mb-4">
                            <label class="form-label text-muted status-badge mb-2 ms-1"><?php echo e(__('Geo-Spatial Precision')); ?></label>
                            <div class="input-group bg-light rounded-pill p-1 border border-light shadow-none overflow-hidden">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="mdi mdi-map-marker-radius"></i></span>
                                <input type="text" id="<?php echo e(!$todayAttendance ? 'locationDisplay' : 'checkoutLocationDisplay'); ?>" class="form-control bg-transparent border-0 shadow-none smaller fw-bold" readonly placeholder="<?php echo e(__('Scan for location...')); ?>">
                                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-soft" onclick="getLocation()">VERIFY</button>
                            </div>
                        </div>

                        <!-- Biometric Sec -->
                        <div class="mb-5">
                            <label class="form-label text-muted status-badge mb-2 ms-1"><?php echo e(__('Identity Verification Feed')); ?></label>
                            <div class="scanner-container shadow-sm">
                                <video id="<?php echo e(!$todayAttendance ? 'checkinVideo' : 'checkoutVideo'); ?>" class="w-100 h-100 object-fit-cover" autoplay playsinline muted></video>
                                <canvas id="<?php echo e(!$todayAttendance ? 'checkinCanvas' : 'checkoutCanvas'); ?>" class="d-none"></canvas>
                                <img id="<?php echo e(!$todayAttendance ? 'checkinPreview' : 'checkoutPreview'); ?>" class="w-100 h-100 object-fit-cover d-none shadow-soft rounded-4" alt="">
                                
                                <div id="<?php echo e(!$todayAttendance ? 'video-overlay' : 'checkout-overlay'); ?>" class="scanner-overlay">
                                    <div class="p-4 bg-white bg-opacity-90 rounded-circle mb-3 shadow-soft">
                                        <i class="mdi mdi-face-recognition fs-1 text-primary"></i>
                                    </div>
                                    <div class="text-dark fw-bold smallest letter-spacing-1"><?php echo e(__('READY FOR BIOMETRIC SCAN')); ?></div>
                                </div>
                                <div class="face-guide"></div>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="button" class="btn btn-outline-light text-dark border bg-white flex-grow-1 rounded-pill fw-bold smaller py-2 shadow-soft" id="<?php echo e(!$todayAttendance ? 'checkinStartCamera' : 'checkoutStartCamera'); ?>">
                                    <i class="mdi mdi-video-plus-outline me-1 text-primary"></i><?php echo e(__('ACTIVATE')); ?>

                                </button>
                                <button type="button" class="btn btn-primary flex-grow-1 rounded-pill fw-bold shadow-soft smaller py-2" id="<?php echo e(!$todayAttendance ? 'checkinCapture' : 'checkoutCapture'); ?>" disabled>
                                    <i class="mdi mdi-camera-outline me-1"></i><?php echo e(__('CAPTURE IDENTITY')); ?>

                                </button>
                                <button type="button" class="btn btn-outline-danger d-none rounded-circle p-0 shadow-soft" style="width: 42px; height: 42px;" id="<?php echo e(!$todayAttendance ? 'checkinRetake' : 'checkoutRetake'); ?>">
                                    <i class="mdi mdi-refresh"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn <?php echo e(!$todayAttendance ? 'btn-success' : 'btn-warning'); ?> rounded-pill w-100 py-3 fw-bold shadow-soft pulse-on-hover" id="<?php echo e(!$todayAttendance ? 'checkinBtn' : 'checkoutBtn'); ?>" disabled>
                            <i class="mdi <?php echo e(!$todayAttendance ? 'mdi-login-variant' : 'mdi-logout-variant'); ?> me-2"></i><?php echo e(!$todayAttendance ? __('EXECUTE CHECK-IN') : __('EXECUTE CHECK-OUT')); ?>

                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Secondary Context & Policy -->
    <div class="col-xl-7 col-lg-6">
        <div class="row g-4 h-100">
            <div class="col-12 h-100">
                <div class="card p-5 border-0 shadow-soft rounded-5 h-100 bg-white d-flex flex-column">
                    <h5 class="text-dark fw-bold mb-4 pb-2 border-bottom border-light"><i class="mdi mdi-shield-account-outline me-2 text-primary"></i><?php echo e(__('Compliance Intelligence')); ?></h5>
                    
                    <div class="row g-4 mb-auto">
                        <div class="col-md-6">
                            <div class="bg-light bg-opacity-50 p-4 rounded-4 h-100 border-light border">
                                <div class="p-2 bg-indigo bg-opacity-10 text-indigo rounded-3 d-inline-block mb-3">
                                    <i class="mdi mdi-crosshairs-gps fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark"><?php echo e(__('Geo-Perimeter')); ?></h6>
                                <p class="text-muted smaller mb-0"><?php echo e(__('Cloud-verified GPS coordinates ensure you are physically present at the assigned deployment site before unlocking terminal actions.')); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light bg-opacity-50 p-4 rounded-4 h-100 border-light border">
                                <div class="p-2 bg-mint bg-opacity-10 text-mint rounded-3 d-inline-block mb-3">
                                    <i class="mdi mdi-face-recognition fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark"><?php echo e(__('Facial Hash')); ?></h6>
                                <p class="text-muted smaller mb-0"><?php echo e(__('Each session requires a unique biometric selfie. Our neural engine matches this against your profile to prevent unauthorized proxy participation.')); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light bg-opacity-50 p-4 rounded-4 h-100 border-light border">
                                <div class="p-2 bg-honey bg-opacity-10 text-honey rounded-3 d-inline-block mb-3">
                                    <i class="mdi mdi-clock-check-outline fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark"><?php echo e(__('Shift Sync')); ?></h6>
                                <p class="text-muted smaller mb-0"><?php echo e(__('The system automatically aligns your check-in with the active roster definition. Late submissions are flagged but accepted with audit notes.')); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light bg-opacity-50 p-4 rounded-4 h-100 border-light border">
                                <div class="p-2 bg-rose bg-opacity-10 text-rose rounded-3 d-inline-block mb-3">
                                    <i class="mdi mdi-database-lock-outline fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark"><?php echo e(__('Audit Integrity')); ?></h6>
                                <p class="text-muted smaller mb-0"><?php echo e(__('Records are immutable once submitted. Location admins review high-precision alerts to maintain perfect organizational accountability.')); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 text-center border-top border-light">
                        <div class="p-3 bg-light rounded-pill d-inline-flex align-items-center gap-3 shadow-sm px-4">
                            <i class="mdi mdi-access-point text-success"></i>
                            <div class="status-badge text-muted"><?php echo e(__('System Synchronized')); ?> : <span class="text-dark fw-800" id="digitalClock"><?php echo e(now()->format('H:i:s')); ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/attendances/checkin.blade.php ENDPATH**/ ?>