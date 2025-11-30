@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Edit Shift Lokasi</h1>
            <p class="text-muted mb-0">Perbarui pengaturan shift untuk lokasi ini.</p>
        </div>
        <div>
            <a href="{{ route('location-shifts.index') }}" class="text-decoration-none">Kembali</a>
        </div>
    </div>
</div>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manage Shifts for {{ $location->name }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('location-shifts.index') }}">Location Shifts</a></li>
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
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Manage Shifts for {{ $location->name }} ({{ $location->code }})</h3>
                        </div>
                        <!-- /.card-header -->

                        <!-- form start -->
                        <form action="{{ route('location-shifts.update', $location) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info"></i> Current Location</h5>
                                    <strong>{{ $location->name }}</strong> ({{ $location->code }})<br>
                                    <small>{{ $location->address }}</small>
                                </div>

                                <div class="form-group">
                                    <label for="shift_ids">Select Shifts <span class="text-danger">*</span></label>
                                    <div class="border p-3" style="max-height: 400px; overflow-y: auto;">
                                        @foreach($allShifts as $shift)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="shift_{{ $shift->id }}" name="shift_ids[]" value="{{ $shift->id }}"
                                                       {{ in_array($shift->id, $assignedShiftIds) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="shift_{{ $shift->id }}">
                                                    <strong>{{ $shift->name }}</strong> ({{ $shift->code }})
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $shift->getFormattedSchedule() }}
                                                        @if($shift->day)
                                                            - {{ $shift->getDayName() }}
                                                        @endif
                                                    </small>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('shift_ids')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">Select the shifts that should be available at this location.</small>
                                </div>

                                @if($assignedShiftIds)
                                    <div class="form-group">
                                        <label>Currently Assigned Shifts:</label>
                                        <div class="d-flex flex-wrap">
                                            @foreach($location->shifts as $shift)
                                                <span class="badge badge-success mr-2 mb-2">
                                                    {{ $shift->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Shifts
                                </button>
                                <a href="{{ route('location-shifts.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <a href="{{ route('location-shifts.show', $location) }}" class="btn btn-info float-right">
                                    <i class="fas fa-eye"></i> View Details
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
