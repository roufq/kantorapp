@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Shift Details</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shifts.index') }}">Shifts</a></li>
                        <li class="breadcrumb-item active">{{ $shift->name }}</li>
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
                            <h3 class="card-title">Shift Information</h3>
                            <div class="card-tools">
                                <a href="{{ route('shifts.edit', $shift) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-3">ID</dt>
                                <dd class="col-sm-9">{{ $shift->id }}</dd>

                                <dt class="col-sm-3">Locations</dt>
                                <dd class="col-sm-9">
                                    <span class="text-muted">-</span>
                                </dd>

                                <dt class="col-sm-3">Name</dt>
                                <dd class="col-sm-9">{{ $shift->name }}</dd>

                                <dt class="col-sm-3">Code</dt>
                                <dd class="col-sm-9">
                                    <span class="badge badge-info">{{ $shift->code }}</span>
                                </dd>

                                <dt class="col-sm-3">Time Range</dt>
                                <dd class="col-sm-9">{{ $shift->start_time }} - {{ $shift->end_time }}</dd>

                                <dt class="col-sm-3">Work Hours</dt>
                                <dd class="col-sm-9">{{ $shift->work_hours }} hours</dd>

                                <dt class="col-sm-3">Status</dt>
                                <dd class="col-sm-9">
                                    @if($shift->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-3">Description</dt>
                                <dd class="col-sm-9">{{ $shift->description ?: '-' }}</dd>

                                <dt class="col-sm-3">Created At</dt>
                                <dd class="col-sm-9">{{ $shift->created_at->format('d M Y H:i') }}</dd>

                                <dt class="col-sm-3">Updated At</dt>
                                <dd class="col-sm-9">{{ $shift->updated_at->format('d M Y H:i') }}</dd>
                            </dl>

                            @if($shift->break_times && count($shift->break_times) > 0)
                            <h5 class="mt-4">Break Times</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($shift->break_times as $break)
                                        <tr>
                                            <td>{{ $break['start'] }}</td>
                                            <td>{{ $break['end'] }}</td>
                                            <td>{{ $break['description'] ?: '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>

                <div class="col-md-4">
                    <!-- Recent Attendances -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Recent Attendances</h3>
                        </div>
                        <div class="card-body p-0">
                            @if($shift->attendances->count() > 0)
                                <ul class="list-group list-group-flush">
                                    @foreach($shift->attendances->take(10) as $attendance)
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $attendance->user->name }}</strong><br>
                                                <small class="text-muted">{{ $attendance->check_in->format('d M Y H:i') }}</small>
                                                @if($attendance->check_out)
                                                    <br><small class="text-muted">Out: {{ $attendance->check_out->format('H:i') }}</small>
                                                @endif
                                            </div>
                                            @if($attendance->status === 'present')
                                                <span class="badge badge-success">Present</span>
                                            @elseif($attendance->status === 'late')
                                                <span class="badge badge-warning">Late</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($attendance->status) }}</span>
                                            @endif
                                        </div>
                                    </li>
                                    @endforeach
                                    @if($shift->attendances->count() > 10)
                                    <li class="list-group-item text-center">
                                        <small class="text-muted">And {{ $shift->attendances->count() - 10 }} more...</small>
                                    </li>
                                    @endif
                                </ul>
                            @else
                                <div class="card-body">
                                    <p class="text-muted text-center">No attendances recorded for this shift</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <a href="{{ route('shifts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Shifts
                    </a>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection
