@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Location</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">Locations</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Edit Location Information</h3>
                        </div>
                        <!-- /.card-header -->

                        <!-- form start -->
                        <form action="{{ route('locations.update', $location) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="brand_name">Brand Name</label>
                                            <input type="text" class="form-control @error('brand_name') is-invalid @enderror" id="brand_name" name="brand_name" value="{{ old('brand_name', $location->brand_name) }}">
                                            @error('brand_name')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="brand_logo_url">Brand Logo URL/Path</label>
                                            <input type="text" class="form-control @error('brand_logo_url') is-invalid @enderror" id="brand_logo_url" name="brand_logo_url" value="{{ old('brand_logo_url', $location->brand_logo_url) }}" placeholder="/storage/logos/main.png or https://...">
                                            @error('brand_logo_url')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        @php $logo = $location->brand_logo_url ? (Str::startsWith($location->brand_logo_url, ['http://','https://']) ? $location->brand_logo_url : asset($location->brand_logo_url)) : null; @endphp
                                        @if($logo)
                                          <img src="{{ $logo }}" alt="Logo" class="img-thumbnail" style="max-height:48px;">
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="primary_color">Primary Color (e.g., #0d6efd)</label>
                                            <input type="text" class="form-control @error('primary_color') is-invalid @enderror" id="primary_color" name="primary_color" value="{{ old('primary_color', $location->primary_color) }}">
                                            @error('primary_color')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="secondary_color">Secondary Color</label>
                                            <input type="text" class="form-control @error('secondary_color') is-invalid @enderror" id="secondary_color" name="secondary_color" value="{{ old('secondary_color', $location->secondary_color) }}">
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
                                            <input type="text" class="form-control @error('custom_css_url') is-invalid @enderror" id="custom_css_url" name="custom_css_url" value="{{ old('custom_css_url', $location->custom_css_url) }}">
                                            @error('custom_css_url')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="custom_js_url">Custom JS URL</label>
                                            <input type="text" class="form-control @error('custom_js_url') is-invalid @enderror" id="custom_js_url" name="custom_js_url" value="{{ old('custom_js_url', $location->custom_js_url) }}">
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
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $location->name) }}" required>
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
                                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $location->code) }}" required maxlength="10">
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
                                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $location->address) }}</textarea>
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
                                                <option value="Asia/Jakarta" {{ old('timezone', $location->timezone) == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                                                <option value="Asia/Makassar" {{ old('timezone', $location->timezone) == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                                                <option value="Asia/Jayapura" {{ old('timezone', $location->timezone) == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
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
                                            <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude" value="{{ old('latitude', $location->latitude) }}" placeholder="-6.2088">
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
                                            <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude" value="{{ old('longitude', $location->longitude) }}" placeholder="106.8456">
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
                                            <input type="number" class="form-control @error('radius') is-invalid @enderror" id="radius" name="radius" value="{{ old('radius', $location->radius ?? 50) }}" min="1" max="10000">
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
                                        <input class="custom-control-input" type="checkbox" id="shift_enabled" name="shift_enabled" value="1" {{ old('shift_enabled', $location->shift_enabled) ? 'checked' : '' }}>
                                        <label for="shift_enabled" class="custom-control-label">
                                            Enable Shift System
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Enable shift-based attendance for this location. If disabled, employees will use fixed working hours.</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="schedule_type">Schedule Type</label>
                                            <select class="form-control @error('schedule_type') is-invalid @enderror" id="schedule_type" name="schedule_type">
                                                <option value="">Select Schedule Type</option>
                                                <option value="daily" {{ old('schedule_type', $location->schedule_type) == 'daily' ? 'selected' : '' }}>Daily Schedule</option>
                                                <option value="shifts" {{ old('schedule_type', $location->schedule_type) == 'shifts' ? 'selected' : '' }}>Shift-Based Schedule</option>
                                            </select>
                                            @error('schedule_type')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <small class="form-text text-muted">Choose how working hours are managed for this location</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" id="daily_schedule_group" style="display: none;">
                                    <label for="daily_schedule">Daily Schedule</label>
                                    <textarea class="form-control @error('daily_schedule') is-invalid @enderror" id="daily_schedule" name="daily_schedule" rows="5" placeholder='Example: {"monday": {"shift_id": 1}, "tuesday": {"shift_id": 2}}'>{{ old('daily_schedule', $location->daily_schedule ? json_encode($location->daily_schedule, JSON_PRETTY_PRINT) : '') }}</textarea>
                                    @error('daily_schedule')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">Define working schedule per day. For flexible schedules, use JSON format. For fixed schedules, assign shift IDs to days.</small>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $location->is_active) ? 'checked' : '' }}>
                                        <label for="is_active" class="custom-control-label">
                                            Active
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Inactive locations cannot be used for new assignments</small>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update Location
                                </button>
                                @if(auth()->user()->hasRole('Super Admin'))
                                <a href="{{ route('locations.settings', $location) }}" class="btn btn-primary">
                                    <i class="bi bi-gear"></i> Settings
                                </a>
                                @endif
                                <a href="{{ route('locations.show', $location) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
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
<script>
$(document).ready(function() {
    // Toggle daily schedule input based on schedule type
    $('#schedule_type').change(function() {
        if ($(this).val() === 'daily' || $(this).val() === 'shifts') {
            $('#daily_schedule_group').show();
        } else {
            $('#daily_schedule_group').hide();
            $('#daily_schedule').val('');
        }
    });

    // Initialize on page load if there's old input or existing data
    if ($('#schedule_type').val() === 'daily' || $('#schedule_type').val() === 'shifts') {
        $('#daily_schedule_group').show();
    }
});
</script>
@endsection
