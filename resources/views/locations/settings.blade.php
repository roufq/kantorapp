@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-md-8">
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
