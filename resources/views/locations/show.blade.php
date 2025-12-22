@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Detail Lokasi</h3>
        <p class="text-muted mb-0">{{ $location->name }}</p>
    </div>
    <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Location Information</h3>
                            <div class="card-tools">
                                @if(auth()->user()->hasRole('Super Admin'))
                                <a href="{{ route('locations.settings', $location) }}" class="btn btn-primary btn-sm me-1">
                                    <i class="bi bi-gear"></i> Settings
                                </a>
                                @endif
                                <a href="{{ route('locations.edit', $location) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-3">ID</dt>
                                <dd class="col-sm-9">{{ $location->id }}</dd>

                                <dt class="col-sm-3">Name</dt>
                                <dd class="col-sm-9">{{ $location->name }}</dd>

                                <dt class="col-sm-3">Code</dt>
                                <dd class="col-sm-9">
                                    <span class="badge badge-light code-badge border">{{ $location->code }}</span>
                                </dd>

                                <dt class="col-sm-3">Address</dt>
                                <dd class="col-sm-9">{{ $location->address ?: '-' }}</dd>

                                <dt class="col-sm-3">Timezone</dt>
                                <dd class="col-sm-9">{{ $location->timezone }}</dd>

                                <dt class="col-sm-3">Latitude / Longitude</dt>
                                <dd class="col-sm-9">{{ $location->latitude ?? '-' }}, {{ $location->longitude ?? '-' }}</dd>

                                <dt class="col-sm-3">Radius</dt>
                                <dd class="col-sm-9">{{ $location->radius ? ($location->radius . ' m') : '-' }}</dd>

                                <dt class="col-sm-3">Default Location</dt>
                                <dd class="col-sm-9">
                                    @if($location->is_default)
                                        <span class="badge badge-primary">Default</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-3">Shift Enabled</dt>
                                <dd class="col-sm-9">
                                    @if($location->shift_enabled)
                                        <span class="badge badge-success">Yes</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-3">Schedule Type</dt>
                                <dd class="col-sm-9">
                                    @if($location->schedule_type)
                                        <span class="badge badge-info">{{ ucfirst($location->schedule_type) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </dd>

                                @if($location->daily_schedule)
                                <dt class="col-sm-3">Daily Schedule</dt>
                                <dd class="col-sm-9">
                                    <pre class="bg-light p-2 rounded">{{ json_encode($location->daily_schedule, JSON_PRETTY_PRINT) }}</pre>
                                </dd>
                                @endif

                                <dt class="col-sm-3">Status</dt>
                                <dd class="col-sm-9">
                                    @if($location->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-3">Created At</dt>
                                <dd class="col-sm-9">{{ $location->created_at->format('d M Y H:i') }}</dd>

                                <dt class="col-sm-3">Updated At</dt>
                                <dd class="col-sm-9">{{ $location->updated_at->format('d M Y H:i') }}</dd>
                            </dl>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>

                <div class="col-md-4">
                    <!-- Users at this location -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Users at this Location</h3>
                        </div>
                        <div class="card-body p-0">
                            @if($location->users->count() > 0)
                                <ul class="list-group list-group-flush">
                                    @foreach($location->users->take(5) as $user)
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>{{ $user->name }}</span>
                                            <small class="text-muted">{{ $user->role }}</small>
                                        </div>
                                    </li>
                                    @endforeach
                                    @if($location->users->count() > 5)
                                    <li class="list-group-item text-center">
                                        <small class="text-muted">And {{ $location->users->count() - 5 }} more...</small>
                                    </li>
                                    @endif
                                </ul>
                            @else
                                <div class="card-body">
                                    <p class="text-muted text-center">No users assigned to this location</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Attendances -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Recent Attendances</h3>
                        </div>
                        <div class="card-body p-0">
                            @if($location->attendances->count() > 0)
                                <ul class="list-group list-group-flush">
                                    @foreach($location->attendances->take(5) as $attendance)
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $attendance->user->name }}</strong><br>
                                                <small class="text-muted">{{ method_exists($attendance, 'getAttribute') && $attendance->check_in_time ? $attendance->check_in_time->format('d M Y H:i') : '' }}</small>
                                                @if(!empty($attendance->location))
                                                    @php $p = explode(',', $attendance->location); @endphp
                                                    @if(count($p) === 2)
                                                        @php $plat = trim($p[0]); $plng = trim($p[1]); @endphp
                                                        <br><small><a href="https://www.google.com/maps?q={{ $plat }},{{ $plng }}" target="_blank" rel="noopener">Lihat di Peta</a></small>
                                                    @endif
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
                                    @if($location->attendances->count() > 5)
                                    <li class="list-group-item text-center">
                                        <small class="text-muted">And {{ $location->attendances->count() - 5 }} more...</small>
                                    </li>
                                    @endif
                                </ul>
                            @else
                                <div class="card-body">
                                    <p class="text-muted text-center">No attendances recorded</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Locations
                    </a>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection
