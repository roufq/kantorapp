@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Location Shift Details</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('location-shifts.index') }}">Location Shifts</a></li>
                        <li class="breadcrumb-item active">Show</li>
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
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ $location->name }} Shift Configuration</h3>
                            <div class="card-tools">
                                <a href="{{ route('location-shifts.edit', $location) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Manage Shifts
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Location Details</strong>
                                    <dl class="row mt-2">
                                        <dt class="col-sm-4">Name:</dt>
                                        <dd class="col-sm-8">{{ $location->name }}</dd>

                                        <dt class="col-sm-4">Code:</dt>
                                        <dd class="col-sm-8">{{ $location->code }}</dd>

                                        <dt class="col-sm-4">Address:</dt>
                                        <dd class="col-sm-8">{{ $location->address }}</dd>

                                        <dt class="col-sm-4">Timezone:</dt>
                                        <dd class="col-sm-8">{{ $location->timezone }}</dd>

                                        <dt class="col-sm-4">Status:</dt>
                                        <dd class="col-sm-8">
                                            @if($location->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </dd>
                                    </dl>
                                </div>

                                <div class="col-md-6">
                                    <strong>Shift Information</strong>
                                    <div class="mt-2">
                                        <p><strong>Total Assigned Shifts:</strong> {{ $location->shifts->count() }}</p>

                                        @if($location->shifts->count() > 0)
                                            <strong>Assigned Shifts:</strong>
                                            <div class="mt-2">
                                                @foreach($location->shifts as $shift)
                                                    <div class="card card-outline card-info mb-2">
                                                        <div class="card-header p-2">
                                                            <h6 class="card-title mb-0">
                                                                {{ $shift->name }} ({{ $shift->code }})
                                                            </h6>
                                                        </div>
                                                        <div class="card-body p-2">
                                                            <small>
                                                                <strong>Schedule:</strong> {{ $shift->getFormattedSchedule() }}<br>
                                                                @if($shift->day)
                                                                    <strong>Day:</strong> {{ $shift->getDayName() }}<br>
                                                                @endif
                                                                <strong>Type:</strong> {{ ucfirst($shift->shift_type) }}<br>
                                                                <strong>Status:</strong>
                                                                @if($shift->is_active)
                                                                    <span class="badge badge-success">Active</span>
                                                                @else
                                                                    <span class="badge badge-danger">Inactive</span>
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="alert alert-warning mt-2">
                                                <i class="icon fas fa-exclamation-triangle"></i>
                                                No shifts are currently assigned to this location.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <a href="{{ route('location-shifts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <a href="{{ route('location-shifts.edit', $location) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Manage Shifts
                            </a>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection
