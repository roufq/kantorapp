<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Attendance Management</h3>
        <p class="text-muted mb-0">Clock-in dan clock-out sesuai lokasi Anda.</p>
    </div>
    <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><?php echo e(__('Attendance Management')); ?></div>

                <div class="card-body">
                    <?php if(auth()->guard()->check()): ?>
                        <?php
                            $roleName = auth()->user()->getRoleNames()->first();
                            $effectiveName = isset($effectiveLocation) && $effectiveLocation ? ($effectiveLocation->name ?? null) : (auth()->user()->location->name ?? null);
                        ?>
                        <?php if(auth()->user()->hasRole('Admin Lokasi')): ?>
                            <div class="alert alert-info">
                                Anda masuk sebagai <strong>Admin Lokasi</strong><?php echo e($effectiveName ? ' - ' . e($effectiveName) : ''); ?>.
                                <?php if(!empty($effectiveTemporary)): ?>
                                    <br><small class="text-muted">Catatan: perubahan lokasi sementara untuk hari ini.</small>
                                <?php endif; ?>
                                <br>
                                Informasi: Clock-in/Clock-out diterapkan untuk karyawan pada lokasi ini. Akun Anda juga mengikuti lokasi ini.
                            </div>
                        <?php elseif(auth()->user()->hasRole('Karyawan')): ?>
                            <div class="alert alert-secondary">
                                Anda masuk sebagai <strong>Karyawan</strong><?php echo e($effectiveName ? ' di lokasi ' . e($effectiveName) : ''); ?>.
                                <?php if(!empty($effectiveTemporary)): ?>
                                    <br><small class="text-muted">Catatan: perubahan lokasi sementara untuk hari ini.</small>
                                <?php endif; ?>
                                <br>
                                Anda dapat melakukan Clock-in/Clock-out untuk lokasi ini.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if(session('success')): ?>
                        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div id="geolocation-error" class="alert alert-danger" style="display: none;"></div>
                    <div id="geolocation-info" class="alert alert-secondary" style="display:none;"></div>
                    <div id="manual-mode-info" class="alert alert-info" style="display: none;"></div>

                    <h4>Check In / Check Out</h4>

                    <?php if($todayAttendance && !$todayAttendance->check_out_time): ?>
                        <div class="alert alert-info">You checked in at <?php echo e($todayAttendance->check_in_time->format('H:i:s')); ?></div>
                    <?php elseif($todayAttendance && $todayAttendance->check_out_time): ?>
                        <div class="alert alert-success">You have completed your attendance for today. Checked out at <?php echo e($todayAttendance->check_out_time->format('H:i:s')); ?></div>
                    <?php else: ?>
                        
                        <form id="checkinForm" method="POST" action="<?php echo e(route('attendance.checkin.post')); ?>" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">
                            <input type="hidden" id="accuracy" name="accuracy">
                            <input type="hidden" id="device_id" name="device_id">
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" id="locationDisplay" class="form-control" readonly placeholder="Click 'Get GPS Location' to fetch your current location">
                                <button type="button" class="btn btn-secondary mt-1" onclick="getLocation()">Get GPS Location</button>
                                <small class="text-muted d-block mt-1">Aktifkan GPS dan pastikan akurasi lokasi baik.</small>
                            </div>
                            <input type="hidden" name="check_in_selfie_data" id="check_in_selfie_data">
                            <div class="mb-3">
                                <label class="form-label">Selfie Check In</label>
                                <div class="border rounded p-2">
                                    <video id="checkinVideo" class="w-100 rounded" autoplay playsinline muted style="max-height:260px;"></video>
                                    <canvas id="checkinCanvas" class="d-none"></canvas>
                                    <img id="checkinPreview" class="w-100 rounded d-none" alt="Selfie check-in preview">
                                </div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="checkinStartCamera">Aktifkan Kamera</button>
                                    <button type="button" class="btn btn-primary btn-sm" id="checkinCapture" disabled>Ambil Selfie</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm d-none" id="checkinRetake">Ulangi</button>
                                </div>
                                <div class="mt-2 d-none" id="checkinFallback">
                                    <input type="file" name="check_in_photo" class="form-control" accept="image/*" capture="user">
                                    <small class="text-muted">Fallback: upload foto jika kamera tidak tersedia.</small>
                                </div>
                                <small class="text-muted d-block mt-2">Wajib selfie dengan kamera depan saat check-in.</small>
                            </div>
                            <button type="submit" class="btn btn-primary" id="checkinBtn" disabled>Check In</button>
                        </form>
                    <?php endif; ?>

                    <?php if($todayAttendance && !$todayAttendance->check_out_time): ?>
                        <hr>

                        
                        <form id="checkoutForm" method="POST" action="<?php echo e(route('attendance.checkout')); ?>" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" id="checkout_latitude" name="latitude">
                            <input type="hidden" id="checkout_longitude" name="longitude">
                            <input type="hidden" id="checkout_accuracy" name="accuracy">
                            <input type="hidden" id="checkout_device_id" name="device_id">
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <div id="checkoutLocationDisplay" class="form-control" readonly>Click "Get GPS Location" to fetch your current location</div>
                                <button type="button" class="btn btn-secondary mt-1" onclick="getLocation()">Get GPS Location</button>
                            </div>
                            <input type="hidden" name="check_out_selfie_data" id="check_out_selfie_data">
                            <div class="mb-3">
                                <label class="form-label">Selfie Check Out</label>
                                <div class="border rounded p-2">
                                    <video id="checkoutVideo" class="w-100 rounded" autoplay playsinline muted style="max-height:260px;"></video>
                                    <canvas id="checkoutCanvas" class="d-none"></canvas>
                                    <img id="checkoutPreview" class="w-100 rounded d-none" alt="Selfie check-out preview">
                                </div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="checkoutStartCamera">Aktifkan Kamera</button>
                                    <button type="button" class="btn btn-primary btn-sm" id="checkoutCapture" disabled>Ambil Selfie</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm d-none" id="checkoutRetake">Ulangi</button>
                                </div>
                                <div class="mt-2 d-none" id="checkoutFallback">
                                    <input type="file" name="check_out_photo" class="form-control" accept="image/*" capture="user">
                                    <small class="text-muted">Fallback: upload foto jika kamera tidak tersedia.</small>
                                </div>
                                <small class="text-muted d-block mt-2">Wajib selfie dengan kamera depan saat check-out.</small>
                            </div>
                            <button type="submit" class="btn btn-warning" id="checkoutBtn" disabled>Check Out</button>
                        </form>
                    <?php endif; ?>

                    <script>
                        let currentLat, currentLng, currentAccuracy;
                        let watchId = null;
                        let bestFix = null;
                        const TARGET_ACCURACY_M = 30; // stop early if <= 30m
                        const WATCH_TIMEOUT_MS = 20000; // watch for up to 20s

                        function getLocation() {
                            if (navigator.geolocation) {
                                // reset state
                                bestFix = null;
                                currentAccuracy = undefined;
                                const infoDiv = document.getElementById('geolocation-info');
                                const errDiv = document.getElementById('geolocation-error');
                                if (errDiv) { errDiv.style.display = 'none'; errDiv.textContent = ''; }
                                if (infoDiv) {
                                    infoDiv.style.display = 'block';
                                    infoDiv.textContent = 'Mengambil lokasi... tunggu hingga 20 detik.';
                                    infoDiv.className = 'alert alert-secondary';
                                }

                                // First, request an immediate reading
                                navigator.geolocation.getCurrentPosition(function(position) {
                                    updateFix(position);
                                    // Start watching for a better fix for a short time window
                                    startWatch();
                                
                                }, function(error) {
                                    showGeoError(error);
                                }, function(error) {
                                    showGeoError(error);
                                }, { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 });
                            } else {
                                const errorMessage = 'Geolocation is not supported by this browser.';
                                const errorDiv = document.getElementById('geolocation-error');
                                errorDiv.textContent = errorMessage;
                                errorDiv.style.display = 'block';

                                // Disable buttons
                                const checkinBtn = document.getElementById('checkinBtn');
                                const checkoutBtn = document.getElementById('checkoutBtn');
                                if (checkinBtn) checkinBtn.disabled = true;
                                if (checkoutBtn) checkoutBtn.disabled = true;
                            }
                        }

                        function startWatch() {
                            const infoDiv = document.getElementById('geolocation-info');
                            // Stop an existing watch if any
                            if (watchId !== null) {
                                navigator.geolocation.clearWatch(watchId);
                                watchId = null;
                            }
                            const startedAt = Date.now();
                            watchId = navigator.geolocation.watchPosition(function(pos) {
                                updateFix(pos);
                                const acc = pos.coords.accuracy;
                                if (infoDiv) {
                                    infoDiv.style.display = 'block';
                                    infoDiv.textContent = 'Accuracy ~ ' + Math.round(acc) + ' m';
                                }
                                // Stop early if good enough
                                if (acc <= TARGET_ACCURACY_M) {
                                    stopWatchAndApply();
                                }
                                // Stop by timeout window
                                if (Date.now() - startedAt > WATCH_TIMEOUT_MS) {
                                    stopWatchAndApply();
                                }
                            }, function(err){
                                // Ignore watch errors; fallback to any fix we already have
                                stopWatchAndApply();
                            }, { enableHighAccuracy: true, maximumAge: 0 });
                        }

                        function stopWatchAndApply() {
                            if (watchId !== null) {
                                navigator.geolocation.clearWatch(watchId);
                                watchId = null;
                            }
                            const infoDiv = document.getElementById('geolocation-info');
                            if (infoDiv) infoDiv.style.display = bestFix ? 'block' : 'none';
                            if (bestFix) {
                                applyFix(bestFix);
                            }
                        }

                        function updateFix(position) {
                            currentLat = position.coords.latitude;
                            currentLng = position.coords.longitude;
                            currentAccuracy = position.coords.accuracy; // meters
                            bestFix = position;
                        }

                        function applyFix(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            const acc = position.coords.accuracy;
                            const location = lat + ', ' + lng;

                            const infoDiv = document.getElementById('geolocation-info');
                            if (infoDiv) {
                                infoDiv.style.display = 'block';
                                infoDiv.textContent = 'Location: ' + location + ' (Akurasi ' + Math.round(acc) + ' m)';
                                if (acc > 100) {
                                    infoDiv.className = 'alert alert-warning';
                                    infoDiv.textContent += ' Akurasi rendah, coba aktifkan GPS atau pindah ke area terbuka.';
                                } else {
                                    infoDiv.className = 'alert alert-secondary';
                                }
                            }

                            // For check-in form
                            const latitudeInput = document.getElementById('latitude');
                            const longitudeInput = document.getElementById('longitude');
                            const accuracyInput = document.getElementById('accuracy');
                            const locationDisplay = document.getElementById('locationDisplay');
                            const checkinBtn = document.getElementById('checkinBtn');

                            if (latitudeInput) latitudeInput.value = lat;
                            if (longitudeInput) longitudeInput.value = lng;
                            if (accuracyInput) accuracyInput.value = acc;
                            if (locationDisplay) locationDisplay.value = location;
                            if (checkinBtn) checkinBtn.disabled = false;

                            // For check-out form
                            const checkoutLatitudeInput = document.getElementById('checkout_latitude');
                            const checkoutLongitudeInput = document.getElementById('checkout_longitude');
                            const checkoutAccuracyInput = document.getElementById('checkout_accuracy');
                            const checkoutLocationDisplay = document.getElementById('checkoutLocationDisplay');
                            const checkoutBtn = document.getElementById('checkoutBtn');

                            if (checkoutLatitudeInput) checkoutLatitudeInput.value = lat;
                            if (checkoutLongitudeInput) checkoutLongitudeInput.value = lng;
                            if (checkoutAccuracyInput) checkoutAccuracyInput.value = acc;
                            if (checkoutLocationDisplay) checkoutLocationDisplay.textContent = location;
                            if (checkoutBtn) checkoutBtn.disabled = false;
                        }

                        function showGeoError(error) {
                            const errorMessage = 'Error getting location: ' + error.message;
                            const errorDiv = document.getElementById('geolocation-error');
                            errorDiv.textContent = errorMessage;
                            errorDiv.style.display = 'block';
                            const infoDiv = document.getElementById('geolocation-info');
                            if (infoDiv) infoDiv.style.display = 'none';

                            // Disable buttons
                            const checkinBtn = document.getElementById('checkinBtn');
                            const checkoutBtn = document.getElementById('checkoutBtn');
                            if (checkinBtn) checkinBtn.disabled = true;
                            if (checkoutBtn) checkoutBtn.disabled = true;
                        }

                        function ensureDeviceId() {
                            const key = 'kantorapp_device_id';
                            let id = localStorage.getItem(key);
                            if (!id) {
                                id = 'dev-' + Math.random().toString(36).slice(2) + Date.now().toString(36);
                                localStorage.setItem(key, id);
                            }
                            const deviceInput = document.getElementById('device_id');
                            const checkoutDeviceInput = document.getElementById('checkout_device_id');
                            if (deviceInput) deviceInput.value = id;
                            if (checkoutDeviceInput) checkoutDeviceInput.value = id;
                        }

                        ensureDeviceId();

                        const maxSelfieBytes = 2 * 1024 * 1024;

                        function setupSelfieCapture(opts) {
                            const {
                                videoId, canvasId, previewId, startBtnId, captureBtnId, retakeBtnId, fallbackId, dataInputId,
                            } = opts;
                            const video = document.getElementById(videoId);
                            const canvas = document.getElementById(canvasId);
                            const preview = document.getElementById(previewId);
                            const startBtn = document.getElementById(startBtnId);
                            const captureBtn = document.getElementById(captureBtnId);
                            const retakeBtn = document.getElementById(retakeBtnId);
                            const fallback = document.getElementById(fallbackId);
                            const dataInput = document.getElementById(dataInputId);
                            let stream = null;

                            async function startCamera() {
                                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                                    if (fallback) fallback.classList.remove('d-none');
                                    return;
                                }
                                try {
                                    stream = await navigator.mediaDevices.getUserMedia({
                                        video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                                        audio: false,
                                    });
                                    if (video) {
                                        video.srcObject = stream;
                                        video.classList.remove('d-none');
                                    }
                                    if (fallback) fallback.classList.add('d-none');
                                    captureBtn.disabled = false;
                                } catch (err) {
                                    if (fallback) fallback.classList.remove('d-none');
                                }
                            }

                            function stopCamera() {
                                if (stream) {
                                    stream.getTracks().forEach((t) => t.stop());
                                    stream = null;
                                }
                            }

                            function captureSelfie() {
                                if (!video || !canvas) {
                                    return;
                                }
                                const width = video.videoWidth || 640;
                                const height = video.videoHeight || 480;
                                canvas.width = width;
                                canvas.height = height;
                                const ctx = canvas.getContext('2d');
                                ctx.drawImage(video, 0, 0, width, height);
                                const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                                const byteSize = Math.ceil((dataUrl.length - 'data:image/jpeg;base64,'.length) * 0.75);
                                if (byteSize > maxSelfieBytes) {
                                    alert('Ukuran selfie terlalu besar. Coba lagi dengan kondisi cahaya lebih terang.');
                                    return;
                                }
                                if (dataInput) dataInput.value = dataUrl;
                                if (preview) {
                                    preview.src = dataUrl;
                                    preview.classList.remove('d-none');
                                }
                                if (video) {
                                    video.classList.add('d-none');
                                }
                                retakeBtn.classList.remove('d-none');
                                captureBtn.disabled = true;
                                stopCamera();
                            }

                            function resetSelfie() {
                                if (dataInput) dataInput.value = '';
                                if (preview) {
                                    preview.src = '';
                                    preview.classList.add('d-none');
                                }
                                if (video) video.classList.remove('d-none');
                                retakeBtn.classList.add('d-none');
                                captureBtn.disabled = true;
                                startCamera();
                            }

                            if (startBtn) startBtn.addEventListener('click', startCamera);
                            if (captureBtn) captureBtn.addEventListener('click', captureSelfie);
                            if (retakeBtn) retakeBtn.addEventListener('click', resetSelfie);
                        }

                        setupSelfieCapture({
                            videoId: 'checkinVideo',
                            canvasId: 'checkinCanvas',
                            previewId: 'checkinPreview',
                            startBtnId: 'checkinStartCamera',
                            captureBtnId: 'checkinCapture',
                            retakeBtnId: 'checkinRetake',
                            fallbackId: 'checkinFallback',
                            dataInputId: 'check_in_selfie_data',
                        });

                        setupSelfieCapture({
                            videoId: 'checkoutVideo',
                            canvasId: 'checkoutCanvas',
                            previewId: 'checkoutPreview',
                            startBtnId: 'checkoutStartCamera',
                            captureBtnId: 'checkoutCapture',
                            retakeBtnId: 'checkoutRetake',
                            fallbackId: 'checkoutFallback',
                            dataInputId: 'check_out_selfie_data',
                        });

                        // Auto-get location when page loads for check-out if user is already checked in
                        <?php if($todayAttendance && !$todayAttendance->check_out_time): ?>
                        window.onload = function() {
                            getLocation();
                        };
                        <?php endif; ?>
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\attendances\checkin.blade.php ENDPATH**/ ?>