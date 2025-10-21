@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Assign Shifts to Location</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('location-shifts.index') }}">Location Shifts</a></li>
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
                            <h3 class="card-title">Assign Shifts to Location</h3>
                        </div>
                        <!-- /.card-header -->

                        <!-- form start -->
                        <form action="{{ route('location-shifts.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="location_id">Select Location <span class="text-danger">*</span></label>
                                    <select class="form-control @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
                                        <option value="">Choose a location</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }} ({{ $location->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="shift_ids">Select Shifts <span class="text-danger">*</span></label>
                                    <div class="border p-3" style="max-height: 300px; overflow-y: auto;">
                                        @foreach($shifts as $shift)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="shift_{{ $shift->id }}" name="shift_ids[]" value="{{ $shift->id }}"
                                                       {{ in_array($shift->id, old('shift_ids', [])) ? 'checked' : '' }}>
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
                                    <small class="form-text text-muted">Select one or more shifts to assign to the selected location.</small>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Assign Shifts
                                </button>
                                <a href="{{ route('location-shifts.index') }}" class="btn btn-secondary">
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
