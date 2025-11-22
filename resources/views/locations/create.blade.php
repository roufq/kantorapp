@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Add New Location</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">Locations</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Location Information</h3>
                        </div>
                        <!-- /.card-header -->

                        <!-- form start -->
                        <form action="{{ route('locations.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="brand_name">Brand Name</label>
                                            <input type="text" class="form-control @error('brand_name') is-invalid @enderror" id="brand_name" name="brand_name" value="{{ old('brand_name') }}">
                                            @error('brand_name')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="brand_logo_url">Brand Logo URL/Path</label>
                                            <input type="text" class="form-control @error('brand_logo_url') is-invalid @enderror" id="brand_logo_url" name="brand_logo_url" value="{{ old('brand_logo_url') }}" placeholder="/storage/logos/main.png or https://...">
                                            @error('brand_logo_url')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-12">
                                        <div id="loc-map" style="height: 340px; border-radius: 6px; overflow: hidden; border: 1px solid #dee2e6; position:relative;">
                                            <div id="loc-map-status" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#6c757d;font-size:14px;">Loading map…</div>
                                        </div>
                                        <small class="text-muted d-block mt-1">Tip: drag marker atau klik peta untuk memilih titik. Gunakan pencarian untuk mencari alamat.</small>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="primary_color">Primary Color (e.g., #0d6efd)</label>
                                            <input type="text" class="form-control @error('primary_color') is-invalid @enderror" id="primary_color" name="primary_color" value="{{ old('primary_color') }}">
                                            @error('primary_color')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="secondary_color">Secondary Color</label>
                                            <input type="text" class="form-control @error('secondary_color') is-invalid @enderror" id="secondary_color" name="secondary_color" value="{{ old('secondary_color') }}">
                                            @error('secondary_color')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="custom_css_url">Custom CSS URL</label>
                                            <input type="text" class="form-control @error('custom_css_url') is-invalid @enderror" id="custom_css_url" name="custom_css_url" value="{{ old('custom_css_url') }}">
                                            @error('custom_css_url')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="custom_js_url">Custom JS URL</label>
                                            <input type="text" class="form-control @error('custom_js_url') is-invalid @enderror" id="custom_js_url" name="custom_js_url" value="{{ old('custom_js_url') }}">
                                            @error('custom_js_url')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="code">Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required maxlength="10">
                                            @error('code')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                            <small class="form-text text-muted">Unique code for the location (max 10 characters)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                            @error('address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="timezone">Timezone</label>
                                            <select class="form-control @error('timezone') is-invalid @enderror" id="timezone" name="timezone">
                                                <option value="Asia/Jakarta" {{ old('timezone', 'Asia/Jakarta') == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                                                <option value="Asia/Makassar" {{ old('timezone') == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                                                <option value="Asia/Jayapura" {{ old('timezone') == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                                            </select>
                                            @error('timezone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="latitude">Latitude</label>
                                            <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude" value="{{ old('latitude') }}" placeholder="-6.2088">
                                            @error('latitude')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                            <small class="form-text text-muted">Optional: Latitude coordinate for geo-fencing</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="longitude">Longitude</label>
                                            <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude" value="{{ old('longitude') }}" placeholder="106.8456">
                                            @error('longitude')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                            <small class="form-text text-muted">Optional: Longitude coordinate for geo-fencing</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="radius">Radius (meters)</label>
                                            <input type="number" class="form-control @error('radius') is-invalid @enderror" id="radius" name="radius" value="{{ old('radius', 50) }}" min="1" max="10000">
                                            @error('radius')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                            <small class="form-text text-muted">Radius in meters for attendance check-in (default: 50m)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="shift_enabled" name="shift_enabled" value="1" {{ old('shift_enabled') ? 'checked' : '' }}>
                                        <label for="shift_enabled" class="custom-control-label">
                                            Enable Shift System
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Enable shift-based attendance for this location. If disabled, employees will use fixed working hours.</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="default_shift_id">Schedule Type</label>
                                            <select class="form-control @error('default_shift_id') is-invalid @enderror" id="default_shift_id" name="default_shift_id">
                                                <option value="">Select Shift</option>
                                                @php($selectedShiftId = old('default_shift_id'))
                                                @if(isset($allActiveShiftsMultiple))
                                                <optgroup label="All Active Shifts — Multiple">
                                                    @foreach($allActiveShiftsMultiple as $s)
                                                        <option value="{{ $s->id }}" data-type="multiple" data-scope="global" {{ (string)$selectedShiftId === (string)$s->id ? 'selected' : '' }}>
                                                            {{ $s->name }} ({{ $s->getFormattedSchedule() }})
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                                @endif
                                                @if(isset($allActiveShiftsSingle))
                                                <optgroup label="All Active Shifts — Single">
                                                    @foreach($allActiveShiftsSingle as $s)
                                                        <option value="{{ $s->id }}" data-type="single" data-scope="global" {{ (string)$selectedShiftId === (string)$s->id ? 'selected' : '' }}>
                                                            {{ $s->name }} ({{ $s->getFormattedSchedule() }})
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                                @endif
                                            </select>
                                            @error('default_shift_id')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                            <small class="form-text text-muted">Saat "Enable Shift System" dicentang tampil opsi Multiple Shift, jika tidak maka tampil Single Shift. Nilai yang disimpan adalah shift_id terpilih.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" id="daily_schedule_group" style="display: none;">
                                    <label for="daily_schedule">Daily Schedule</label>
                                    <textarea class="form-control @error('daily_schedule') is-invalid @enderror" id="daily_schedule" name="daily_schedule" rows="5" placeholder='Example: {"monday": {"shift_id": 1}, "tuesday": {"shift_id": 2}}'>{{ old('daily_schedule') }}</textarea>
                                    @error('daily_schedule')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    <small class="form-text text-muted">Define working schedule per day. For flexible schedules, use JSON format. For fixed schedules, assign shift IDs to days.</small>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label for="is_active" class="custom-control-label">
                                            Active
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Inactive locations cannot be used for new assignments</small>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Location
                                </button>
                                <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}" />
<link rel="stylesheet" href="{{ asset('vendor/leaflet/Control.Geocoder.css') }}" />
<style>
    .leaflet-control-geocoder {
        max-width: 420px
    }

    .leaflet-control-geocoder-form input {
        width: 320px;
        min-width: 220px;
        font-size: 14px;
        color: #212529;
        background: #fff;
        padding: 6px 10px
    }

    .leaflet-control-geocoder .leaflet-control-geocoder-alternatives {
        max-height: 260px;
        overflow: auto
    }
</style>
<script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
<script src="{{ asset('vendor/leaflet/Control.Geocoder.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const defaultShift = document.getElementById('default_shift_id');
        const dailyGroup = document.getElementById('daily_schedule_group');
        const dailyText = document.getElementById('daily_schedule');

        // Hide daily schedule group by default (can be enabled later if needed)
        if (dailyGroup) dailyGroup.style.display = 'none';

        // Filter shift options by Enable Shift System
        const shiftEnabled = document.getElementById('shift_enabled');
        function filterShiftOptions() {
            if (!defaultShift) return;
            const want = (shiftEnabled && shiftEnabled.checked) ? 'multiple' : 'single';
            Array.from(defaultShift.options).forEach((opt) => {
                if (!opt.value) return;
                const t = opt.getAttribute('data-type');
                const show = !t || t === want;
                opt.hidden = !show;
            });
            const sel = defaultShift.selectedOptions[0];
            if (sel && sel.hidden) defaultShift.value = '';
        }
        if (shiftEnabled) shiftEnabled.addEventListener('change', filterShiftOptions);
        filterShiftOptions();

        function initMapWhenReady() {
            if (!(window.L && L.map)) {
                const st = document.getElementById('loc-map-status');
                if (st) st.textContent = 'Loading map assets…';
                return setTimeout(initMapWhenReady, 150);
            }

            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const radiusInput = document.getElementById('radius');

            const fallback = {
                lat: -7.8121780996020185,
                lng: 110.35047828093953,
                zoom: 12
            };
            const initLat = parseFloat(latInput?.value) || fallback.lat;
            const initLng = parseFloat(lngInput?.value) || fallback.lng;

            const map = L.map('loc-map').setView([initLat, initLng], fallback.zoom);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const st = document.getElementById('loc-map-status');
            if (st) st.style.display = 'none';

            let marker = L.marker([initLat, initLng], {
                draggable: true
            }).addTo(map);
            let circle = null;

            function redrawCircle() {
                const r = parseFloat(radiusInput?.value) || 50;
                if (circle) map.removeLayer(circle);
                circle = L.circle(marker.getLatLng(), {
                    radius: r,
                    color: '#0d6efd',
                    fillOpacity: 0.08
                }).addTo(map);
            }
            redrawCircle();

            marker.on('dragend', () => {
                const p = marker.getLatLng();
                if (latInput) latInput.value = p.lat;
                if (lngInput) lngInput.value = p.lng;
                redrawCircle();
            });
            map.on('click', (e) => {
                marker.setLatLng(e.latlng);
                if (latInput) latInput.value = e.latlng.lat;
                if (lngInput) lngInput.value = e.latlng.lng;
                redrawCircle();
            });

            if (latInput && lngInput) {
                latInput.addEventListener('change', () => {
                    const lat = parseFloat(latInput.value);
                    const lng = parseFloat(lngInput.value);
                    if (isFinite(lat) && isFinite(lng)) {
                        const ll = L.latLng(lat, lng);
                        marker.setLatLng(ll);
                        map.setView(ll);
                        redrawCircle();
                    }
                });
                lngInput.addEventListener('change', () => latInput.dispatchEvent(new Event('change')));
            }
            if (radiusInput) radiusInput.addEventListener('change', redrawCircle);

            if (L.Control && L.Control.Geocoder) {
                const geocodeParams = {
                    limit: 6,
                    addressdetails: 1,
                    "accept-language": "id,en"
                };
                if (typeof window.APP_GEO_COUNTRY_CODES === "string" && window.APP_GEO_COUNTRY_CODES.trim().length) {
                    geocodeParams.countrycodes = window.APP_GEO_COUNTRY_CODES.trim();
                } else {
                    geocodeParams.countrycodes = "id";
                }
                const geocoderService = L.Control.Geocoder.nominatim({
                    serviceUrl: "https://nominatim.openstreetmap.org/",
                    geocodingQueryParams: geocodeParams
                });
                const geocoder = L.Control.geocoder({
                    defaultMarkGeocode: false,
                    collapsed: false,
                    placeholder: "Cari alamat atau tempat…",
                    position: "topleft",
                    geocoder: geocoderService,
                    suggestMinLength: 3,
                    suggestTimeout: 200
                }).on("markgeocode", function(e) {
                    const ll = e.geocode.center;
                    map.setView(ll, 17);
                    marker.setLatLng(ll);
                    if (latInput) latInput.value = ll.lat;
                    if (lngInput) lngInput.value = ll.lng;
                    redrawCircle();
                });
                geocoder.addTo(map);
            }
        }
        if (document.readyState === 'complete') initMapWhenReady();
        else {
            window.addEventListener('load', initMapWhenReady);
            setTimeout(initMapWhenReady, 800);
        }
    });
</script>
@endsection




