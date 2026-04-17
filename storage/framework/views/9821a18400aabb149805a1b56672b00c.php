<?php $__env->startPush('styles'); ?>
<style>
    .soft-card-mint { background-color: var(--soft-mint) !important; color: var(--soft-mint-text) !important; border: 1px solid var(--soft-mint-border) !important; }
    .soft-card-celeste { background-color: var(--soft-celeste) !important; color: var(--soft-celeste-text) !important; border: 1px solid var(--soft-celeste-border) !important; }
    .soft-card-lavender { background-color: var(--soft-lavender) !important; color: var(--soft-lavender-text) !important; border: 1px solid var(--soft-lavender-border) !important; }
    .soft-card-rose { background-color: var(--soft-rose) !important; color: var(--soft-rose-text) !important; border: 1px solid var(--soft-rose-border) !important; }
    .pulse-indicator { animation: pulse 2s infinite; }
    @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.6; } 100% { opacity: 1; } }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <div class="content pt-4">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="row mb-5 align-items-center">
                <div class="col-lg-7">
                    <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        <?php echo e(__('Attendance')); ?>

                    </h1>
                    <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Clock-in and clock-out to record your work hours.')); ?></p>
                </div>
                <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
                    <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-light text-dark rounded-pill px-4 fw-bold shadow-sm border">
                        <i class="mdi mdi-view-dashboard-outline me-2"></i><?php echo e(__('Dashboard')); ?>

                    </a>
                </div>
            </div>

            <!-- Role & Info Status -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card p-3 soft-card-celeste">
                        <?php if(auth()->guard()->check()): ?>
                            <?php
                                $roleName = auth()->user()->getRoleNames()->first();
                                $effectiveName = isset($effectiveLocation) && $effectiveLocation ? ($effectiveLocation->name ?? null) : (auth()->user()->location->name ?? null);
                            ?>
                            <div class="d-flex align-items-center p-2">
                                <div class="avatar-sm me-4 rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="mdi mdi-account-star-outline fs-3 text-info"></i>
                                </div>
                                <div>
                                    <h6 class="text-dark fw-bold mb-1" style="font-size: 1.1rem;"><?php echo e(__('Active Session')); ?>: <span class="text-primary"><?php echo e($roleName); ?></span></h6>
                                    <div class="text-muted fw-semibold">
                                        <i class="mdi mdi-map-marker-radius-outline me-1"></i><?php echo e($effectiveName ? $effectiveName : __('No primary location assigned')); ?>

                                        <?php if(!empty($effectiveTemporary)): ?>
                                            <span class="ms-2 badge bg-warning text-dark border-0"><?php echo e(__('Temporary Shift/Location active')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <!-- Terminal / Form Section -->
                <div class="col-xl-5 col-lg-6">
                    <div class="card p-4 h-100 shadow-sm border-0">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-light">
                            <h5 class="text-dark fw-bold mb-0">
                                <i class="mdi mdi-shield-account-outline text-primary me-2"></i><?php echo e(__('Verification')); ?>

                            </h5>
                            <div class="pulse-indicator">
                                <span class="badge bg-success bg-opacity-10 text-success border-success px-3">
                                    <i class="mdi mdi-shield-check me-1"></i>SECURE
                                </span>
                            </div>
                        </div>

                        <?php if(session('success')): ?>
                            <div class="alert soft-card-mint border-0 text-success p-3 rounded-4 mb-4 shadow-sm">
                                <i class="mdi mdi-check-circle-outline me-2"></i><?php echo e(session('success')); ?>

                            </div>
                        <?php endif; ?>

                        <?php if($errors->any()): ?>
                            <div class="alert soft-card-rose border-0 text-danger p-3 rounded-4 mb-4 shadow-sm">
                                <ul class="mb-0 ps-3">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div id="geolocation-error" class="alert soft-card-rose border-0 text-danger p-3 rounded-4 mb-4 d-none shadow-sm"></div>
                        <div id="geolocation-info" class="alert soft-card-celeste border-0 text-info p-3 rounded-4 mb-4 d-none shadow-sm"></div>

                        <?php if($todayAttendance && !$todayAttendance->check_out_time): ?>
                            <div class="p-4 soft-card-celeste rounded-4 mb-4 text-center border-0">
                                <div class="text-muted small fw-bold text-uppercase mb-2" style="letter-spacing: 1px;"><?php echo e(__('Clock-In Time')); ?></div>
                                <div class="text-primary h2 fw-bold mb-0 font-monospace"><?php echo e($todayAttendance->check_in_time->format('H:i:s')); ?></div>
                            </div>
                        <?php elseif($todayAttendance && $todayAttendance->check_out_time): ?>
                            <div class="p-5 soft-card-mint rounded-5 text-center mb-4 border-0">
                                <div class="bg-white rounded-circle d-inline-flex p-3 mb-3 shadow-sm">
                                    <i class="mdi mdi-check-decagram-outline text-success fs-1"></i>
                                </div>
                                <h4 class="text-dark fw-bold"><?php echo e(__('Shift Completed')); ?></h4>
                                <p class="text-muted mb-0"><?php echo e(__('Great work today! You have finished your shift.')); ?></p>
                                <div class="mt-4 text-success fw-bold font-monospace bg-white d-inline-block px-4 py-2 rounded-pill shadow-sm">OUT: <?php echo e($todayAttendance->check_out_time->format('H:i:s')); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if(!$todayAttendance || ($todayAttendance && !$todayAttendance->check_out_time)): ?>
                            <?php if(!$todayAttendance): ?>
                                <form id="checkinForm" method="POST" action="<?php echo e(route('attendance.checkin.post')); ?>" enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" id="latitude" name="latitude">
                                    <input type="hidden" id="longitude" name="longitude">
                                    <input type="hidden" id="accuracy" name="accuracy">
                                    <input type="hidden" id="device_id" name="device_id">
                                    <input type="hidden" name="check_in_selfie_data" id="check_in_selfie_data">

                                    <div class="mb-4">
                                        <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('GPS Verification')); ?></label>
                                        <div class="input-group bg-light rounded-pill p-1 border shadow-none">
                                            <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="mdi mdi-crosshairs-gps"></i></span>
                                            <input type="text" id="locationDisplay" class="form-control bg-transparent border-0 text-dark shadow-none px-2 fw-semibold" readonly placeholder="<?php echo e(__('Awaiting GPS scan...')); ?>">
                                            <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="getLocation()"><i class="mdi mdi-radar me-1"></i>SCAN</button>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Photo Identity')); ?></label>
                                        <div class="position-relative bg-light rounded-5 border-dashed border-2 overflow-hidden d-flex align-items-center justify-content-center" style="min-height: 280px; border-color: #cbd5e1 !important;">
                                            <video id="checkinVideo" class="w-100" autoplay playsinline muted style="height: 280px; object-fit: cover;"></video>
                                            <canvas id="checkinCanvas" class="d-none"></canvas>
                                            <img id="checkinPreview" class="w-100 d-none" style="height: 280px; object-fit: cover;" alt="Selfie preview">
                                            
                                            <!-- Overlay -->
                                            <div id="video-overlay" class="position-absolute d-flex flex-column align-items-center text-center p-3">
                                                <i class="mdi mdi-camera-account text-muted opacity-25" style="font-size: 4rem;"></i>
                                                <div class="text-muted fw-bold small mt-2"><?php echo e(__('READY FOR SELFIE')); ?></div>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-3 mt-4">
                                            <button type="button" class="btn btn-outline-info flex-grow-1 rounded-pill fw-bold" id="checkinStartCamera">
                                                <i class="mdi mdi-video-outline me-1"></i><?php echo e(__('CAMERA')); ?>

                                            </button>
                                            <button type="button" class="btn btn-primary flex-grow-1 rounded-pill fw-bold shadow-lg" id="checkinCapture" disabled>
                                                <i class="mdi mdi-camera-outline me-1"></i><?php echo e(__('CAPTURE')); ?>

                                            </button>
                                            <button type="button" class="btn btn-outline-danger d-none rounded-pill" id="checkinRetake">
                                                <i class="mdi mdi-refresh"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success rounded-pill w-100 py-3 fw-bold shadow-lg mt-3" id="checkinBtn" disabled>
                                        <i class="mdi mdi-login-variant me-2"></i><?php echo e(__('CONFIRM CHECK-IN')); ?>

                                    </button>
                                </form>
                            <?php else: ?>
                                <form id="checkoutForm" method="POST" action="<?php echo e(route('attendance.checkout')); ?>" enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" id="checkout_latitude" name="latitude">
                                    <input type="hidden" id="checkout_longitude" name="longitude">
                                    <input type="hidden" id="checkout_accuracy" name="accuracy">
                                    <input type="hidden" id="checkout_device_id" name="device_id">
                                    <input type="hidden" name="check_out_selfie_data" id="check_out_selfie_data">

                                    <div class="mb-4">
                                        <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Exit Location')); ?></label>
                                        <div class="input-group bg-light rounded-pill p-1 border shadow-none">
                                            <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="mdi mdi-crosshairs-gps"></i></span>
                                            <div id="checkoutLocationDisplay" class="form-control bg-transparent border-0 text-dark shadow-none px-2 fw-semibold d-flex align-items-center" style="height: 38px;"><?php echo e(__('Awaiting scan...')); ?></div>
                                            <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="getLocation()"><i class="mdi mdi-radar me-1"></i>VERIFY</button>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Exit Photo')); ?></label>
                                        <div class="position-relative bg-light rounded-5 border-dashed border-2 overflow-hidden d-flex align-items-center justify-content-center" style="min-height: 280px; border-color: #cbd5e1 !important;">
                                            <video id="checkoutVideo" class="w-100" autoplay playsinline muted style="height: 280px; object-fit: cover;"></video>
                                            <canvas id="checkoutCanvas" class="d-none"></canvas>
                                            <img id="checkoutPreview" class="w-100 d-none" style="height: 280px; object-fit: cover;" alt="Selfie preview">
                                            
                                            <!-- Overlay -->
                                            <div id="checkout-overlay" class="position-absolute d-flex flex-column align-items-center text-center p-3">
                                                <i class="mdi mdi-camera-account text-muted opacity-25" style="font-size: 4rem;"></i>
                                                <div class="text-muted fw-bold small mt-2"><?php echo e(__('READY FOR EXIT PHOTO')); ?></div>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-3 mt-4">
                                            <button type="button" class="btn btn-outline-info flex-grow-1 rounded-pill fw-bold" id="checkoutStartCamera">
                                                <i class="mdi mdi-video-outline me-1"></i><?php echo e(__('CAMERA')); ?>

                                            </button>
                                            <button type="button" class="btn btn-primary flex-grow-1 rounded-pill fw-bold shadow-lg" id="checkoutCapture" disabled>
                                                <i class="mdi mdi-camera-outline me-1"></i><?php echo e(__('CAPTURE')); ?>

                                            </button>
                                            <button type="button" class="btn btn-outline-danger d-none rounded-pill" id="checkoutRetake">
                                                <i class="mdi mdi-refresh"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-warning rounded-pill w-100 py-3 fw-bold shadow-lg mt-3 text-dark" id="checkoutBtn" disabled>
                                        <i class="mdi mdi-logout-variant me-2"></i><?php echo e(__('CONFIRM CHECK-OUT')); ?>

                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Info Area Section -->
                <div class="col-xl-7 col-lg-6">
                    <div class="row g-4 h-100">
                        <div class="col-12">
                            <div class="card p-4 soft-card-lavender h-100 border-0">
                                <h5 class="text-dark fw-bold mb-4"><i class="mdi mdi-information-outline me-2 text-info"></i><?php echo e(__('Usage Policy')); ?></h5>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="bg-white p-3 rounded-4 shadow-sm h-100">
                                            <div class="text-primary fw-bold mb-2 small"><i class="mdi mdi-check-circle me-1"></i> GPS PRIVACY</div>
                                            <p class="text-muted small mb-0"><?php echo e(__('Your precise location is only used for workplace validation durante check-in/out.')); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-white p-3 rounded-4 shadow-sm h-100">
                                            <div class="text-primary fw-bold mb-2 small"><i class="mdi mdi-check-circle me-1"></i> BIOMETRICS</div>
                                            <p class="text-muted small mb-0"><?php echo e(__('Real-time selfies prevent proxy attendance and protect your identity.')); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-auto pt-4 text-center">
                                    <div class="text-muted small fw-bold opacity-50"><i class="mdi mdi-clock-outline me-1"></i> SERVER TIME: <?php echo e(now()->format('H:i:s')); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startSection('scripts'); ?>
<script>
    // [SCRIPTS KEPT FROM ORIGINAL - COMPRESSED FOR BREVITY BUT FULL LOGIC PRESERVED]
    let watchId = null; let bestFix = null; const TARGET_ACCURACY_M = 30; const WATCH_TIMEOUT_MS = 20000; 
    function getLocation() {
        if (navigator.geolocation) {
            bestFix = null; const infoDiv = document.getElementById('geolocation-info'); const errDiv = document.getElementById('geolocation-error');
            if (errDiv) { errDiv.classList.add('d-none'); errDiv.textContent = ''; }
            if (infoDiv) { infoDiv.classList.remove('d-none'); infoDiv.textContent = "<?php echo e(__('Getting location...')); ?>"; infoDiv.className = 'alert soft-card-celeste border-0 text-info p-3 rounded-4 mb-4 shadow-sm'; }
            navigator.geolocation.getCurrentPosition(function(position) { updateFix(position); startWatch(); }, function(error) { showGeoError(error); }, { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 });
        } else { showGeoError({message: 'Geolocation not supported'}); }
    }
    function startWatch() {
        const infoDiv = document.getElementById('geolocation-info'); if (watchId !== null) { navigator.geolocation.clearWatch(watchId); watchId = null; }
        const startedAt = Date.now(); watchId = navigator.geolocation.watchPosition(function(pos) {
            updateFix(pos); const acc = pos.coords.accuracy; if (infoDiv) infoDiv.textContent = 'Scanning Accuracy: ~' + Math.round(acc) + ' meters';
            if (acc <= TARGET_ACCURACY_M || (Date.now() - startedAt > WATCH_TIMEOUT_MS)) stopWatchAndApply();
        }, function(err){ stopWatchAndApply(); }, { enableHighAccuracy: true, maximumAge: 0 });
    }
    function stopWatchAndApply() { if (watchId !== null) { navigator.geolocation.clearWatch(watchId); watchId = null; } if (bestFix) applyFix(bestFix); }
    function updateFix(position) { bestFix = position; }
    function applyFix(position) {
        const lat = position.coords.latitude; const lng = position.coords.longitude; const acc = position.coords.accuracy; const locationStr = lat.toFixed(6) + ', ' + lng.toFixed(6);
        const infoDiv = document.getElementById('geolocation-info');
        if (infoDiv) {
            infoDiv.classList.remove('d-none'); infoDiv.textContent = 'GPS Verified: ' + locationStr + ' (' + Math.round(acc) + 'm)';
            if (acc > 50) { infoDiv.className = 'alert soft-card-rose border-0 text-danger p-3 rounded-4 mb-4 shadow-sm'; infoDiv.textContent += " <?php echo e(__('Low accuracy, try moving.')); ?>"; }
            else { infoDiv.className = 'alert soft-card-mint border-0 text-success p-3 rounded-4 mb-4 shadow-sm'; }
        }
        ['latitude','checkout_latitude'].forEach(id => { let el = document.getElementById(id); if(el) el.value = lat; });
        ['longitude','checkout_longitude'].forEach(id => { let el = document.getElementById(id); if(el) el.value = lng; });
        ['accuracy','checkout_accuracy'].forEach(id => { let el = document.getElementById(id); if(el) el.value = acc; });
        const locDisplay = document.getElementById('locationDisplay'); if (locDisplay) locDisplay.value = locationStr;
        const checkOutDisplay = document.getElementById('checkoutLocationDisplay'); if (checkOutDisplay) checkOutDisplay.textContent = locationStr;
        ['checkinBtn','checkoutBtn'].forEach(id => { let el = document.getElementById(id); if(el) el.disabled = false; });
    }
    function showGeoError(error) { const errorDiv = document.getElementById('geolocation-error'); if (errorDiv) { errorDiv.textContent = 'GPS Error: ' + error.message; errorDiv.classList.remove('d-none'); } const infoDiv = document.getElementById('geolocation-info'); if (infoDiv) infoDiv.classList.add('d-none'); }
    function ensureDeviceId() { const key = 'kantorapp_device_id'; let id = localStorage.getItem(key); if (!id) { id = 'dev-' + Math.random().toString(36).slice(2) + Date.now().toString(36); localStorage.setItem(key, id); }
        ['device_id','checkout_device_id'].forEach(idField => { let el = document.getElementById(idField); if(el) el.value = id; });
    }
    ensureDeviceId();
    const maxSelfieBytes = 2 * 1024 * 1024;
    function setupSelfieCapture(opts) {
        const { videoId, canvasId, previewId, startBtnId, captureBtnId, retakeBtnId, fallbackId, dataInputId, overlayId } = opts;
        const video = document.getElementById(videoId); const canvas = document.getElementById(canvasId); const preview = document.getElementById(previewId);
        const startBtn = document.getElementById(startBtnId); const captureBtn = document.getElementById(captureBtnId); const retakeBtn = document.getElementById(retakeBtnId);
        const fallback = document.getElementById(fallbackId); const dataInput = document.getElementById(dataInputId); const overlay = document.getElementById(overlayId);
        let stream = null;
        async function startCamera() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) { if (fallback) fallback.classList.remove('d-none'); return; }
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }, audio: false });
                if (video) { video.srcObject = stream; video.classList.remove('d-none'); }
                if (overlay) overlay.classList.add('d-none'); if (fallback) fallback.classList.add('d-none'); captureBtn.disabled = false;
            } catch (err) { if (fallback) fallback.classList.remove('d-none'); }
        }
        function stopCamera() { if (stream) { stream.getTracks().forEach((t) => t.stop()); stream = null; } }
        function captureSelfie() {
            if (!video || !canvas) return; const width = video.videoWidth || 640; const height = video.videoHeight || 480;
            canvas.width = width; canvas.height = height; const ctx = canvas.getContext('2d'); ctx.drawImage(video, 0, 0, width, height);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.85); const byteSize = Math.ceil((dataUrl.length - 'data:image/jpeg;base64,'.length) * 0.75);
            if (byteSize > maxSelfieBytes) { alert("<?php echo e(__('Selfie too large.')); ?>"); return; }
            if (dataInput) dataInput.value = dataUrl; if (preview) { preview.src = dataUrl; preview.classList.remove('d-none'); }
            if (video) video.classList.add('d-none'); retakeBtn.classList.remove('d-none'); captureBtn.disabled = true; stopCamera();
        }
        function resetSelfie() { if (dataInput) dataInput.value = ''; if (preview) { preview.src = ''; preview.classList.add('d-none'); }
            if (video) video.classList.remove('d-none'); retakeBtn.classList.add('d-none'); captureBtn.disabled = true; startCamera();
        }
        if (startBtn) startBtn.addEventListener('click', startCamera); if (captureBtn) captureBtn.addEventListener('click', captureSelfie); if (retakeBtn) retakeBtn.addEventListener('click', resetSelfie);
    }
    setupSelfieCapture({ videoId: 'checkinVideo', canvasId: 'checkinCanvas', previewId: 'checkinPreview', startBtnId: 'checkinStartCamera', captureBtnId: 'checkinCapture', retakeBtnId: 'checkinRetake', fallbackId: 'checkinFallback', dataInputId: 'check_in_selfie_data', overlayId: 'video-overlay' });
    setupSelfieCapture({ videoId: 'checkoutVideo', canvasId: 'checkoutCanvas', previewId: 'checkoutPreview', startBtnId: 'checkoutStartCamera', captureBtnId: 'checkoutCapture', retakeBtnId: 'checkoutRetake', fallbackId: 'checkoutFallback', dataInputId: 'check_out_selfie_data', overlayId: 'checkout-overlay' });
    <?php if($todayAttendance && !$todayAttendance->check_out_time): ?> window.onload = function() { getLocation(); }; <?php endif; ?>
</script>
<?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/attendances/checkin.blade.php ENDPATH**/ ?>