@extends('layouts.appnew')

@push('styles')
<style>
    .scanner-container {
        min-height: 320px;
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 2rem;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    .scanner-overlay {
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(2px);
        pointer-events: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }
    .face-guide {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 240px;
        height: 320px;
        border: 2px solid rgba(13, 110, 253, 0.3);
        border-radius: 120px;
        box-shadow: 0 0 0 1000px rgba(255, 255, 255, 0.6);
        pointer-events: none;
        z-index: 1;
    }
    .status-badge {
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.05rem;
        text-transform: uppercase;
    }
</style>
@endpush

@section('content')
<!-- Header Area -->
<div class="row mb-5 align-items-center">
    <div class="col-lg-7">
        <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ __('Attendance Terminal') }}
        </h1>
        <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;">{{ __('Secure, multi-vector personnel verification protocol.') }}</p>
    </div>
    <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-light text-dark border bg-white rounded-pill px-4 fw-bold shadow-soft">
            <i class="mdi mdi-view-dashboard-outline me-2"></i>{{ __('Dashboard') }}
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
            <div class="text-muted status-badge mb-1">{{ __('Active Base Location') }}</div>
            <h5 class="text-dark fw-bold mb-0">
                {{ $effectiveLocation->name ?? 'Enterprise Global HQ' }}
                @if(!empty($effectiveTemporary))
                    <span class="ms-2 badge badge-warning status-badge"><i class="mdi mdi-clock-alert-outline me-1"></i>{{ __('Temporary Context') }}</span>
                @endif
            </h5>
        </div>
        <div class="ms-auto d-none d-md-block">
            <div class="text-end">
                <div class="text-muted status-badge mb-1">{{ __('Auth Identity') }}</div>
                <div class="fw-bold text-dark">{{ auth()->user()->name }} <span class="text-primary mx-1">/</span> <span class="text-muted smaller fw-bold">{{ auth()->user()->getRoleNames()->first() }}</span></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- Verification Main Console -->
    <div class="col-xl-5 col-lg-6">
        <div class="card border-0 shadow-soft rounded-5 h-100 bg-white">
            <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark fw-bold"><i class="mdi mdi-shield-check-outline text-success me-2"></i>{{ __('Verification Unit') }}</h5>
                <span class="badge badge-success bg-opacity-10 text-success fw-bold status-badge shadow-none">{{ __('Active') }}</span>
            </div>
            
            <div class="card-body p-4">
                @if (session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-4 d-flex align-items-center">
                        <div class="bg-white rounded-circle p-2 me-3 text-success shadow-sm">
                            <i class="mdi mdi-check-circle fs-4"></i>
                        </div>
                        <div class="fw-bold smaller">{{ session('success') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-4 d-flex align-items-center">
                        <div class="bg-white rounded-circle p-2 me-3 text-danger shadow-sm">
                            <i class="mdi mdi-alert-circle fs-4"></i>
                        </div>
                        <div class="fw-bold smaller">{{ session('error') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-4">
                        <ul class="mb-0 smaller fw-bold">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="geolocation-error" class="alert alert-danger border-0 shadow-sm rounded-4 p-3 mb-4 d-none smaller fw-bold"></div>
                <div id="geolocation-info" class="alert alert-info border-0 shadow-sm rounded-4 p-3 mb-4 d-none smaller fw-bold"></div>

                @if($todayAttendance)
                    <div class="p-4 bg-light bg-opacity-50 rounded-5 mb-4 text-center border-light shadow-soft pulse">
                        <div class="text-muted status-badge mb-2">{{ __('Current Session Active') }}</div>
                        <div class="text-primary h2 fw-800 mb-0 font-monospace">{{ $todayAttendance->check_in_time->format('H:i:s') }}</div>
                        @if($todayAttendance->check_out_time)
                            <div class="mt-3 pt-3 border-top border-light">
                                <div class="text-muted status-badge mb-1">{{ __('Completion Timestamp') }}</div>
                                <div class="text-success h2 fw-800 mb-0 font-monospace">{{ $todayAttendance->check_out_time->format('H:i:s') }}</div>
                            </div>
                        @endif
                    </div>
                @endif

                @if(!$todayAttendance || ($todayAttendance && !$todayAttendance->check_out_time))
                    <form id="{{ !$todayAttendance ? 'checkinForm' : 'checkoutForm' }}" method="POST" action="{{ !$todayAttendance ? route('attendance.checkin.post') : route('attendance.checkout') }}" enctype="multipart/form-data" class="pt-2">
                        @csrf
                        @if($todayAttendance) @method('POST') @endif
                        <input type="hidden" id="{{ !$todayAttendance ? 'latitude' : 'checkout_latitude' }}" name="latitude">
                        <input type="hidden" id="{{ !$todayAttendance ? 'longitude' : 'checkout_longitude' }}" name="longitude">
                        <input type="hidden" id="{{ !$todayAttendance ? 'accuracy' : 'checkout_accuracy' }}" name="accuracy">
                        <input type="hidden" id="{{ !$todayAttendance ? 'device_id' : 'checkout_device_id' }}" name="device_id">
                        <input type="hidden" name="{{ !$todayAttendance ? 'check_in_selfie_data' : 'check_out_selfie_data' }}" id="{{ !$todayAttendance ? 'check_in_selfie_data' : 'check_out_selfie_data' }}">

                        <!-- Location Sec -->
                        <div class="mb-4">
                            <label class="form-label text-muted status-badge mb-2 ms-1">{{ __('Geo-Spatial Precision') }}</label>
                            <div class="input-group bg-light rounded-pill p-1 border border-light shadow-none overflow-hidden">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="mdi mdi-map-marker-radius"></i></span>
                                <input type="text" id="{{ !$todayAttendance ? 'locationDisplay' : 'checkoutLocationDisplay' }}" class="form-control bg-transparent border-0 shadow-none smaller fw-bold" readonly placeholder="{{ __('Scan for location...') }}">
                                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-soft" onclick="getLocation()">VERIFY</button>
                            </div>
                        </div>

                        <!-- Biometric Sec -->
                        <div class="mb-5">
                            <label class="form-label text-muted status-badge mb-2 ms-1">{{ __('Identity Verification Feed') }}</label>
                            <div class="scanner-container shadow-sm">
                                <video id="{{ !$todayAttendance ? 'checkinVideo' : 'checkoutVideo' }}" class="w-100 h-100 object-fit-cover"></video>
                                <canvas id="{{ !$todayAttendance ? 'checkinCanvas' : 'checkoutCanvas' }}" class="d-none"></canvas>
                                <img id="{{ !$todayAttendance ? 'checkinPreview' : 'checkoutPreview' }}" class="w-100 h-100 object-fit-cover d-none shadow-soft rounded-4" alt="">
                                
                                <div id="{{ !$todayAttendance ? 'video-overlay' : 'checkout-overlay' }}" class="scanner-overlay">
                                    <div class="p-4 bg-white bg-opacity-90 rounded-circle mb-3 shadow-soft">
                                        <i class="mdi mdi-face-recognition fs-1 text-primary"></i>
                                    </div>
                                    <div class="text-dark fw-bold smallest letter-spacing-1">{{ __('READY FOR BIOMETRIC SCAN') }}</div>
                                </div>
                                <div class="face-guide"></div>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="button" class="btn btn-outline-light text-dark border bg-white flex-grow-1 rounded-pill fw-bold smaller py-2 shadow-soft" id="{{ !$todayAttendance ? 'checkinStartCamera' : 'checkoutStartCamera' }}">
                                    <i class="mdi mdi-video-plus-outline me-1 text-primary"></i>{{ __('ACTIVATE') }}
                                </button>
                                <button type="button" class="btn btn-primary flex-grow-1 rounded-pill fw-bold shadow-soft smaller py-2" id="{{ !$todayAttendance ? 'checkinCapture' : 'checkoutCapture' }}" disabled>
                                    <i class="mdi mdi-camera-outline me-1"></i>{{ __('CAPTURE IDENTITY') }}
                                </button>
                                <button type="button" class="btn btn-outline-danger d-none rounded-circle p-0 shadow-soft" style="width: 42px; height: 42px;" id="{{ !$todayAttendance ? 'checkinRetake' : 'checkoutRetake' }}">
                                    <i class="mdi mdi-refresh"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn {{ !$todayAttendance ? 'btn-success' : 'btn-warning' }} rounded-pill w-100 py-3 fw-bold shadow-soft pulse-on-hover" id="{{ !$todayAttendance ? 'checkinBtn' : 'checkoutBtn' }}" disabled>
                            <i class="mdi {{ !$todayAttendance ? 'mdi-login-variant' : 'mdi-logout-variant' }} me-2"></i>{{ !$todayAttendance ? __('EXECUTE CHECK-IN') : __('EXECUTE CHECK-OUT') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Secondary Context & Policy -->
    <div class="col-xl-7 col-lg-6">
        <div class="row g-4 h-100">
            <div class="col-12 h-100">
                <div class="card p-5 border-0 shadow-soft rounded-5 h-100 bg-white d-flex flex-column">
                    <h5 class="text-dark fw-bold mb-4 pb-2 border-bottom border-light"><i class="mdi mdi-shield-account-outline me-2 text-primary"></i>{{ __('Compliance Intelligence') }}</h5>
                    
                    <div class="row g-4 mb-auto">
                        <div class="col-md-6">
                            <div class="bg-light bg-opacity-50 p-4 rounded-4 h-100 border-light border">
                                <div class="p-2 bg-indigo bg-opacity-10 text-indigo rounded-3 d-inline-block mb-3">
                                    <i class="mdi mdi-crosshairs-gps fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark">{{ __('Geo-Perimeter') }}</h6>
                                <p class="text-muted smaller mb-0">{{ __('Cloud-verified GPS coordinates ensure you are physically present at the assigned deployment site before unlocking terminal actions.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light bg-opacity-50 p-4 rounded-4 h-100 border-light border">
                                <div class="p-2 bg-mint bg-opacity-10 text-mint rounded-3 d-inline-block mb-3">
                                    <i class="mdi mdi-face-recognition fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark">{{ __('Facial Hash') }}</h6>
                                <p class="text-muted smaller mb-0">{{ __('Each session requires a unique biometric selfie. Our neural engine matches this against your profile to prevent unauthorized proxy participation.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light bg-opacity-50 p-4 rounded-4 h-100 border-light border">
                                <div class="p-2 bg-honey bg-opacity-10 text-honey rounded-3 d-inline-block mb-3">
                                    <i class="mdi mdi-clock-check-outline fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark">{{ __('Shift Sync') }}</h6>
                                <p class="text-muted smaller mb-0">{{ __('The system automatically aligns your check-in with the active roster definition. Late submissions are flagged but accepted with audit notes.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light bg-opacity-50 p-4 rounded-4 h-100 border-light border">
                                <div class="p-2 bg-rose bg-opacity-10 text-rose rounded-3 d-inline-block mb-3">
                                    <i class="mdi mdi-database-lock-outline fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark">{{ __('Audit Integrity') }}</h6>
                                <p class="text-muted smaller mb-0">{{ __('Records are immutable once submitted. Location admins review high-precision alerts to maintain perfect organizational accountability.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 text-center border-top border-light">
                        <div class="p-3 bg-light rounded-pill d-inline-flex align-items-center gap-3 shadow-sm px-4">
                            <i class="mdi mdi-access-point text-success"></i>
                            <div class="status-badge text-muted">{{ __('System Synchronized') }} : <span class="text-dark fw-800" id="digitalClock">{{ now()->format('H:i:s') }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // System Timing
    setInterval(() => {
        const clock = document.getElementById('digitalClock');
        if(clock) clock.innerText = new Date().toLocaleTimeString('id-ID', { hour12: false });
    }, 1000);

    // Geolocation Core
    let watchId = null; 
    let bestFix = null; 
    const TARGET_ACCURACY_M = 30; 
    const WATCH_TIMEOUT_MS = 20000; 

    function getLocation() {
        if (!navigator.geolocation) {
            showGeoError({message: 'Geolocation not supported'});
            return;
        }
        bestFix = null; 
        const infoDiv = document.getElementById('geolocation-info'); 
        const errDiv = document.getElementById('geolocation-error');
        if (errDiv) { errDiv.classList.add('d-none'); errDiv.textContent = ''; }
        if (infoDiv) { 
            infoDiv.classList.remove('d-none'); 
            infoDiv.textContent = "{{ __('Requesting GPS Lock...') }}"; 
            infoDiv.className = 'alert alert-info border-0 shadow-sm p-3 rounded-4 mb-4 fw-bold smaller'; 
        }
        
        navigator.geolocation.getCurrentPosition(function(position) { 
            updateFix(position); 
            startWatch(); 
        }, function(error) { 
            showGeoError(error); 
        }, { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 });
    }

    function startWatch() {
        const infoDiv = document.getElementById('geolocation-info'); 
        if (watchId !== null) { navigator.geolocation.clearWatch(watchId); watchId = null; }
        const startedAt = Date.now(); 
        watchId = navigator.geolocation.watchPosition(function(pos) {
            updateFix(pos); 
            const acc = pos.coords.accuracy; 
            if (infoDiv) infoDiv.textContent = 'Refining Accuracy: ~' + Math.round(acc) + ' meters...';
            if (acc <= TARGET_ACCURACY_M || (Date.now() - startedAt > WATCH_TIMEOUT_MS)) stopWatchAndApply();
        }, function(err){ stopWatchAndApply(); }, { enableHighAccuracy: true, maximumAge: 0 });
    }

    function stopWatchAndApply() { 
        if (watchId !== null) { navigator.geolocation.clearWatch(watchId); watchId = null; } 
        if (bestFix) applyFix(bestFix); 
    }

    function updateFix(position) { bestFix = position; }

    function applyFix(position) {
        const lat = position.coords.latitude; 
        const lng = position.coords.longitude; 
        const acc = position.coords.accuracy; 
        const locationStr = lat.toFixed(6) + ', ' + lng.toFixed(6);
        const infoDiv = document.getElementById('geolocation-info');
        
        if (infoDiv) {
            infoDiv.classList.remove('d-none'); 
            infoDiv.textContent = 'GPS Synchronized: ' + locationStr + ' (' + Math.round(acc) + 'm)';
            if (acc > 80) { 
                infoDiv.className = 'alert alert-warning border-0 shadow-sm p-3 rounded-4 mb-4 fw-bold smaller'; 
                infoDiv.textContent += " {{ __('Low accuracy, consider open space.') }}"; 
            } else { 
                infoDiv.className = 'alert alert-success border-0 shadow-sm p-3 rounded-4 mb-4 fw-bold smaller'; 
            }
        }
        ['latitude','checkout_latitude'].forEach(id => { let el = document.getElementById(id); if(el) el.value = lat; });
        ['longitude','checkout_longitude'].forEach(id => { let el = document.getElementById(id); if(el) el.value = lng; });
        ['accuracy','checkout_accuracy'].forEach(id => { let el = document.getElementById(id); if(el) el.value = acc; });
        
        const locDisplay = document.getElementById('locationDisplay'); 
        if (locDisplay) locDisplay.value = locationStr;
        const checkOutDisplay = document.getElementById('checkoutLocationDisplay'); 
        if (checkOutDisplay) checkOutDisplay.value = locationStr;
        
        validateFinalState();
    }

    function showGeoError(error) { 
        const errorDiv = document.getElementById('geolocation-error'); 
        if (errorDiv) { 
            errorDiv.textContent = 'GPS Acquisition Failed: ' + error.message; 
            errorDiv.classList.remove('d-none'); 
        } 
        const infoDiv = document.getElementById('geolocation-info'); 
        if (infoDiv) infoDiv.classList.add('d-none'); 
    }

    // Device Integrity
    function ensureDeviceId() { 
        const key = 'kantorapp_device_id'; 
        let id = localStorage.getItem(key); 
        if (!id) { 
            id = 'dev-' + Math.random().toString(36).slice(2) + Date.now().toString(36); 
            localStorage.setItem(key, id); 
        }
        ['device_id','checkout_device_id'].forEach(idField => { 
            let el = document.getElementById(idField); 
            if(el) el.value = id; 
        });
    }
    ensureDeviceId();

    // Biometric Capture Engine
    const maxSelfieBytes = 3 * 1024 * 1024; // 3MB limit
    function setupSelfieCapture(opts) {
        const { videoId, canvasId, previewId, startBtnId, captureBtnId, retakeBtnId, dataInputId, overlayId } = opts;
        const video = document.getElementById(videoId); 
        const canvas = document.getElementById(canvasId); 
        const preview = document.getElementById(previewId);
        const startBtn = document.getElementById(startBtnId); 
        const captureBtn = document.getElementById(captureBtnId); 
        const retakeBtn = document.getElementById(retakeBtnId);
        const dataInput = document.getElementById(dataInputId); 
        const overlay = document.getElementById(overlayId);
        let stream = null;

        if(!video) return;

        async function startCamera() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) { 
                alert("{{ __('Camera hardware not detected or browser unsupported.') }}"); 
                return; 
            }
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } }, 
                    audio: false 
                });
                video.srcObject = stream; 
                video.classList.remove('d-none');
                if (overlay) overlay.classList.add('d-none'); 
                captureBtn.disabled = false;
                startBtn.classList.add('d-none');
            } catch (err) { 
                alert("{{ __('Access Denied: Please unlock camera permissions in your system settings.') }}");
            }
        }

        function stopCamera() { if (stream) { stream.getTracks().forEach((t) => t.stop()); stream = null; } }

        function captureSelfie() {
            if (!video || !canvas) return; 
            const width = video.videoWidth || 640; 
            const height = video.videoHeight || 480;
            canvas.width = width; 
            canvas.height = height; 
            const ctx = canvas.getContext('2d'); 
            ctx.drawImage(video, 0, 0, width, height);
            
            const dataUrl = canvas.toDataURL('image/jpeg', 0.9); 
            const byteSize = Math.ceil((dataUrl.length - 'data:image/jpeg;base64,'.length) * 0.75);
            
            if (byteSize > maxSelfieBytes) { alert("{{ __('Captured entity too dense. Please retry.') }}"); return; }
            if (dataInput) dataInput.value = dataUrl; 
            if (preview) { preview.src = dataUrl; preview.classList.remove('d-none'); }
            video.classList.add('d-none'); 
            retakeBtn.classList.remove('d-none'); 
            captureBtn.disabled = true; 
            stopCamera();
            validateFinalState();
        }

        function resetSelfie() { 
            if (dataInput) dataInput.value = ''; 
            if (preview) { preview.src = ''; preview.classList.add('d-none'); }
            video.classList.remove('d-none'); 
            retakeBtn.classList.add('d-none'); 
            captureBtn.disabled = true; 
            startCamera();
            validateFinalState();
        }

        if (startBtn) startBtn.addEventListener('click', startCamera); 
        if (captureBtn) captureBtn.addEventListener('click', captureSelfie); 
        if (retakeBtn) retakeBtn.addEventListener('click', resetSelfie);
    }

    setupSelfieCapture({ 
        videoId: 'checkinVideo', canvasId: 'checkinCanvas', previewId: 'checkinPreview', 
        startBtnId: 'checkinStartCamera', captureBtnId: 'checkinCapture', retakeBtnId: 'checkinRetake', 
        dataInputId: 'check_in_selfie_data', overlayId: 'video-overlay' 
    });
    
    setupSelfieCapture({ 
        videoId: 'checkoutVideo', canvasId: 'checkoutCanvas', previewId: 'checkoutPreview', 
        startBtnId: 'checkoutStartCamera', captureBtnId: 'checkoutCapture', retakeBtnId: 'checkoutRetake', 
        dataInputId: 'check_out_selfie_data', overlayId: 'checkout-overlay' 
    });

    function validateFinalState() {
        const hasLocation = document.getElementById('latitude')?.value || document.getElementById('checkout_latitude')?.value;
        const hasSelfie = document.getElementById('check_in_selfie_data')?.value || document.getElementById('check_out_selfie_data')?.value;
        
        ['checkinBtn','checkoutBtn'].forEach(id => { 
            let el = document.getElementById(id); 
            if(el) el.disabled = !(hasLocation && hasSelfie); 
        });
    }

    @if($todayAttendance && !$todayAttendance->check_out_time) 
        window.onload = function() { getLocation(); }; 
    @endif
</script>

<style>
    .smaller { font-size: 0.75rem; }
    .smallest { font-size: 0.65rem; }
    .letter-spacing-1 { letter-spacing: 0.05rem; }
    .shadow-soft { box-shadow: 0 10px 40px rgba(0,0,0,0.04) !important; }
    .fw-800 { font-weight: 800; }
    .pulse-on-hover:not(:disabled):hover { transform: translateY(-2px); box-shadow: 0 15px 45px rgba(0,0,0,0.1) !important; }
    .pulse { animation: pulse-soft 3s infinite; }
    @keyframes pulse-soft {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.1); }
        50% { transform: scale(1.01); box-shadow: 0 0 10px 10px rgba(13, 110, 253, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); }
    }
</style>
@endsection
