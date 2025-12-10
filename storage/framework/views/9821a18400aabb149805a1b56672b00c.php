<?php $__env->startSection('content'); ?>
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
                    <div id="manual-mode-info" class="alert alert-info" style="display: none;">Manual check-in is enabled. Please fill in the location manually.</div>

                    <h4>Check In / Check Out</h4>

                    <?php if($todayAttendance && !$todayAttendance->check_out_time): ?>
                        <div class="alert alert-info">You checked in at <?php echo e($todayAttendance->check_in_time->format('H:i:s')); ?></div>
                    <?php elseif($todayAttendance && $todayAttendance->check_out_time): ?>
                        <div class="alert alert-success">You have completed your attendance for today. Checked out at <?php echo e($todayAttendance->check_out_time->format('H:i:s')); ?></div>
                    <?php else: ?>
                        
                        <form id="checkinForm" method="POST" action="<?php echo e(route('attendance.checkin.post')); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">
                            <input type="hidden" id="accuracy" name="accuracy">
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" id="locationDisplay" class="form-control" readonly placeholder="Click 'Get GPS Location' to fetch your current location">
                                <div id="manualInputs" class="row mt-2" style="display:none;">
                                    <div class="col-md-6 mb-2">
                                        <input type="number" step="any" class="form-control" id="manual_latitude" placeholder="Latitude (e.g. -6.2)">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <input type="number" step="any" class="form-control" id="manual_longitude" placeholder="Longitude (e.g. 106.8)">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary mt-1" onclick="getLocation()">Get GPS Location</button>
                                <button type="button" class="btn btn-info mt-1" onclick="enableManualCheckin()">Manual Check-in</button>
                            </div>
                            <button type="submit" class="btn btn-primary" id="checkinBtn" disabled>Check In</button>
                        </form>
                    <?php endif; ?>

                    <?php if($todayAttendance && !$todayAttendance->check_out_time): ?>
                        <hr>

                        
                        <form id="checkoutForm" method="POST" action="<?php echo e(route('attendance.checkout')); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" id="checkout_latitude" name="latitude">
                            <input type="hidden" id="checkout_longitude" name="longitude">
                            <input type="hidden" id="checkout_accuracy" name="accuracy">
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <div id="checkoutLocationDisplay" class="form-control" readonly>Click "Get GPS Location" to fetch your current location</div>
                                <button type="button" class="btn btn-secondary mt-1" onclick="getLocation()">Get GPS Location</button>
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
                                if (infoDiv) { infoDiv.style.display = 'block'; infoDiv.textContent = 'Getting location… please wait (up to 20s).'; }

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
                                infoDiv.textContent = 'Location: ' + location + ' (±' + Math.round(acc) + ' m)';
                                if (acc > 100) {
                                    infoDiv.className = 'alert alert-warning';
                                    infoDiv.textContent += ' — Low accuracy, try enabling Wi‑Fi or use mobile device for GPS.';
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

                        function enableManualCheckin() {
                            // Hide error message
                            const errorDiv = document.getElementById('geolocation-error');
                            errorDiv.style.display = 'none';

                            // Show manual mode info
                            const manualModeDiv = document.getElementById('manual-mode-info');
                            manualModeDiv.style.display = 'block';
                            const infoDiv = document.getElementById('geolocation-info');
                            if (infoDiv) infoDiv.style.display = 'none';

                            // Enable buttons
                            const checkinBtn = document.getElementById('checkinBtn');
                            const checkoutBtn = document.getElementById('checkoutBtn');
                            if (checkinBtn) checkinBtn.disabled = false;
                            if (checkoutBtn) checkoutBtn.disabled = false;

                            // Make location fields editable
                            const locationDisplay = document.getElementById('locationDisplay');
                            const checkoutLocationDisplay = document.getElementById('checkoutLocationDisplay');
                            if (locationDisplay) locationDisplay.readOnly = false;
                            if (checkoutLocationDisplay) checkoutLocationDisplay.readOnly = false;

                            // Show manual lat/lng inputs and bind to hidden fields
                            const manualInputs = document.getElementById('manualInputs');
                            if (manualInputs) manualInputs.style.display = 'flex';
                            const manualLat = document.getElementById('manual_latitude');
                            const manualLng = document.getElementById('manual_longitude');
                            const latitudeInput = document.getElementById('latitude');
                            const longitudeInput = document.getElementById('longitude');
                            const checkoutLatitudeInput = document.getElementById('checkout_latitude');
                            const checkoutLongitudeInput = document.getElementById('checkout_longitude');
                            function syncManual() {
                                const lat = manualLat.value;
                                const lng = manualLng.value;
                                if (latitudeInput) latitudeInput.value = lat;
                                if (longitudeInput) longitudeInput.value = lng;
                                if (checkoutLatitudeInput) checkoutLatitudeInput.value = lat;
                                if (checkoutLongitudeInput) checkoutLongitudeInput.value = lng;
                                if (locationDisplay) locationDisplay.value = (lat && lng) ? (lat + ', ' + lng) : '';
                            }
                            if (manualLat) manualLat.addEventListener('input', syncManual);
                            if (manualLng) manualLng.addEventListener('input', syncManual);
                        }

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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/attendances/checkin.blade.php ENDPATH**/ ?>