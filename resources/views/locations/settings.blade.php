@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
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
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Geolocation</h3>
        <small class="text-muted">Set latitude, longitude, and radius</small>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('locations.update', $location) }}">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label">Latitude</label>
                <input type="number" step="any" class="form-control" id="loc_latitude" name="latitude" value="{{ old('latitude', $location->latitude) }}" placeholder="-6.2">
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label">Longitude</label>
                <input type="number" step="any" class="form-control" id="loc_longitude" name="longitude" value="{{ old('longitude', $location->longitude) }}" placeholder="106.8">
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label">Radius (meter)</label>
                <input type="number" class="form-control" id="loc_radius" name="radius" value="{{ old('radius', $location->radius ?? 50) }}" placeholder="50">
                <small class="text-muted">Default disarankan 50m</small>
              </div>
            </div>
          </div>

          <div class="row mt-2">
            <div class="col-12">
              <div id="loc-map" style="height: 340px; border-radius: 6px; overflow: hidden; border: 1px solid #dee2e6;"></div>
              <small class="text-muted d-block mt-1">Tip: drag marker atau klik peta untuk memilih titik. Gunakan pencarian untuk mencari alamat.</small>
            </div>
          </div>

          @if(auth()->user()->hasRole('Super Admin'))
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" value="1" id="is_default" name="is_default" @if($location->is_default) checked @endif>
            <label class="form-check-label" for="is_default">
              Set as Default Location
            </label>
          </div>
          @endif

          <div class="d-flex gap-2 align-items-center">
            <button type="submit" class="btn btn-primary">Save Geolocation</button>
            <button type="button" class="btn btn-outline-secondary" onclick="fillFromDevice()">Use My Device Location</button>
            <span id="geo-status" class="text-muted"></span>
          </div>
        </form>
      </div>
    </div>
    @if(auth()->user()->hasRole('Super Admin'))
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Branding</h3>
        <small class="text-muted">Update brand fields</small>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('locations.update', $location) }}">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Brand Name</label>
                <input type="text" class="form-control" name="brand_name" value="{{ old('brand_name', $location->brand_name) }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Brand Logo URL/Path</label>
                <input type="text" class="form-control" name="brand_logo_url" value="{{ old('brand_logo_url', $location->brand_logo_url) }}">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Primary Color</label>
                <input type="text" class="form-control" name="primary_color" value="{{ old('primary_color', $location->primary_color) }}" placeholder="#0d6efd">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Secondary Color</label>
                <input type="text" class="form-control" name="secondary_color" value="{{ old('secondary_color', $location->secondary_color) }}">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Custom CSS URL</label>
                <input type="text" class="form-control" name="custom_css_url" value="{{ old('custom_css_url', $location->custom_css_url) }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Custom JS URL</label>
                <input type="text" class="form-control" name="custom_js_url" value="{{ old('custom_js_url', $location->custom_js_url) }}">
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Save Branding</button>
        </form>
      </div>
    </div>
    @endif
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Location Settings - {{ $location->name }}</h3>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('locations.settings.update', $location) }}">
          @csrf
          @method('PATCH')

          <div class="table-responsive">
            <table class="table" id="settings-table">
              <thead>
                <tr>
                  <th>Key</th>
                  <th>Value</th>
                  <th>Type</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach($location->locationSettings as $idx => $setting)
                <tr>
                  <td><input type="text" name="settings[{{ $idx }}][key]" value="{{ $setting->key }}" class="form-control" required></td>
                  <td><input type="text" name="settings[{{ $idx }}][value]" value='{{ is_string($setting->value) ? $setting->value : json_encode($setting->value) }}' class="form-control" required></td>
                  <td>
                    <select name="settings[{{ $idx }}][type]" class="form-control" required>
                      @foreach(['string','number','boolean','json'] as $type)
                      <option value="{{ $type }}" @if($setting->type === $type) selected @endif>{{ $type }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">Remove</button></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <button type="button" class="btn btn-secondary" onclick="addRow()">Add Setting</button>
          <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  // Leaflet assets
  (function ensureLeaflet() {
    const head = document.head;
    if (!document.querySelector('link[href*="leaflet@1.9.4"]')) {
      const lcss = document.createElement('link');
      lcss.rel = 'stylesheet';
      lcss.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
      lcss.integrity = 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=';
      lcss.crossOrigin = '';
      head.appendChild(lcss);
    }
    if (!document.querySelector('script[src*="leaflet@1.9.4"]')) {
      const ljs = document.createElement('script');
      ljs.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
      ljs.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
      ljs.crossOrigin = '';
      head.appendChild(ljs);
    }
    if (!document.querySelector('link[href*="leaflet-control-geocoder"]')) {
      const gcss = document.createElement('link');
      gcss.rel = 'stylesheet';
      gcss.href = 'https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css';
      head.appendChild(gcss);
    }
    if (!document.querySelector('script[src*="Control.Geocoder.js"]')) {
      const gjs = document.createElement('script');
      gjs.src = 'https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js';
      head.appendChild(gjs);
    }
  })();

  function initMapWhenReady() {
    if (!(window.L && L.map)) {
      return setTimeout(initMapWhenReady, 100);
    }
    const latInput = document.getElementById('loc_latitude');
    const lngInput = document.getElementById('loc_longitude');
    const radiusInput = document.getElementById('loc_radius');
    const fallback = {
      lat: -7.8121780996020185,
      lng: 110.35047828093953,
      zoom: 12
    }; // Jakarta default
    const initLat = parseFloat(latInput.value) || fallback.lat;
    const initLng = parseFloat(lngInput.value) || fallback.lng;
    const map = L.map('loc-map').setView([initLat, initLng], fallback.zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = L.marker([initLat, initLng], {
      draggable: true
    }).addTo(map);
    let circle = null;

    function redrawCircle() {
      const r = parseFloat(radiusInput.value) || 50;
      if (circle) {
        map.removeLayer(circle);
      }
      circle = L.circle(marker.getLatLng(), {
        radius: r,
        color: '#0d6efd',
        fillOpacity: 0.08
      }).addTo(map);
    }
    redrawCircle();

    marker.on('dragend', () => {
      const p = marker.getLatLng();
      latInput.value = p.lat;
      lngInput.value = p.lng;
      redrawCircle();
    });
    map.on('click', (e) => {
      marker.setLatLng(e.latlng);
      if (latInput) latInput.value = e.latlng.lat;
      if (lngInput) lngInput.value = e.latlng.lng;
      redrawCircle();
    });
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
    radiusInput.addEventListener('change', redrawCircle);

    // Geocoder
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
        latInput.value = ll.lat;
        lngInput.value = ll.lng;
        redrawCircle();
      });
      geocoder.addTo(map);
    }
  }
  initMapWhenReady();

  function fillFromDevice() {
    const status = document.getElementById('geo-status');
    if (!navigator.geolocation) {
      if (status) status.textContent = 'Geolocation not supported by this browser.';
      return;
    }
    if (status) status.textContent = 'Getting locationâ€¦';
    navigator.geolocation.getCurrentPosition(function(pos) {
      const lat = pos.coords.latitude;
      const lng = pos.coords.longitude;
      const acc = pos.coords.accuracy;
      document.getElementById('loc_latitude').value = lat;
      document.getElementById('loc_longitude').value = lng;
      if (!document.getElementById('loc_radius').value) {
        document.getElementById('loc_radius').value = 50;
      }
      if (status) status.textContent = 'Location set: ' + lat.toFixed(6) + ', ' + lng.toFixed(6) + ' (Â±' + Math.round(acc) + 'm)';
    }, function(err) {
      if (status) status.textContent = 'Error: ' + err.message;
    }, {
      enableHighAccuracy: true,
      timeout: 15000,
      maximumAge: 0
    });
  }

  function addRow() {
    const tbody = document.querySelector('#settings-table tbody');
    const idx = tbody.children.length;
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><input type="text" name="settings[${idx}][key]" class="form-control" required></td>
      <td><input type="text" name="settings[${idx}][value]" class="form-control" required></td>
      <td>
        <select name="settings[${idx}][type]" class="form-control" required>
          <option value="string">string</option>
          <option value="number">number</option>
          <option value="boolean">boolean</option>
          <option value="json">json</option>
        </select>
      </td>
      <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">Remove</button></td>
    `;
    tbody.appendChild(tr);
  }

  function removeRow(btn) {
    const tr = btn.closest('tr');
    tr.parentNode.removeChild(tr);
  }
</script>
@endsection

