@extends('layouts.app')
@section('title')
<div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Dashboard</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
@endsection
@section('content')
<!-- Info boxes -->
@if($user->hasRole('Super Admin'))
  <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-primary shadow-sm">
          <i class="bi bi-person-fill-gear"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Super Admins</span>
          <span class="info-box-number">{{ $totalMasters }}</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-danger shadow-sm">
          <i class="bi bi-people-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Employees</span>
          <span class="info-box-number">{{ $totalEmployees }}</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-success shadow-sm">
          <i class="bi bi-check-circle-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Tasks</span>
          <span class="info-box-number">{{ $totalTasks }}</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-warning shadow-sm">
          <i class="bi bi-chat-text-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Unread Messages</span>
          <span class="info-box-number">{{ $unreadMessages }}</span>
        </div>
      </div>
    </div>
  </div>
@elseif($user->hasRole('Admin Lokasi'))
  <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-danger shadow-sm">
          <i class="bi bi-people-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Employees (My Location)</span>
          <span class="info-box-number">{{ $totalKaryawans }}</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-success shadow-sm">
          <i class="bi bi-check-circle-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Tasks</span>
          <span class="info-box-number">{{ $totalTasks }}</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-warning shadow-sm">
          <i class="bi bi-chat-text-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Unread Messages</span>
          <span class="info-box-number">{{ $unreadMessages }}</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-danger shadow-sm">
          <i class="bi bi-x-octagon-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Absences Today</span>
          <span class="info-box-number">{{ $absencesTodayCount ?? 0 }}</span>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-light shadow-sm">
          <i class="bi bi-clock-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Clock In (Saya)</span>
          <span class="info-box-number">
            @if($todayAttendance && $todayAttendance->check_in_time)
              {{ $todayAttendance->check_in_time->format('H:i') }}
            @else
              --
            @endif
          </span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-dark shadow-sm">
          <i class="bi bi-clock-history"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Clock Out (Saya)</span>
          <span class="info-box-number">
            @if($todayAttendance && $todayAttendance->check_out_time)
              {{ $todayAttendance->check_out_time->format('H:i') }}
            @else
              --
            @endif
          </span>
        </div>
      </div>
    </div>
  </div>
@else
  <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-success shadow-sm">
          <i class="bi bi-check-circle-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">My Tasks</span>
          <span class="info-box-number">{{ $totalTasks }}</span>
        </div>
      </div>
    </div>
    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi') || !auth()->user()->hasRole('Karyawan'))
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-warning shadow-sm">
          <i class="bi bi-chat-text-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Unread Messages</span>
          <span class="info-box-number">{{ $unreadMessages }}</span>
        </div>
      </div>
    </div>
    @endif
    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi') || !auth()->user()->hasRole('Karyawan'))
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
          <span class="info-box-icon text-bg-info shadow-sm">
            <i class="bi bi-calendar3"></i>
          </span>
          <div class="info-box-content">
           <span class="info-box-text">Today's Shift</span>
           <span class="info-box-number">
              @if(isset($todayAssignmentTime) && $todayAssignmentTime === 'Hari libur Anda')
                <div>{{ $todayAssignmentTime }}</div>
              @elseif($todayAssignment && $todayAssignment->shift)
                {{ $todayAssignment->shift->name }}
                @if(!empty($todayAssignmentTime))
                  <div class="text-muted small">{{ $todayAssignmentTime }}</div>
                @endif
              @elseif(!empty($todayAssignmentTime))
                <div class="text-muted small">{{ $todayAssignmentTime }}</div>
              @else
                --
              @endif
           </span>
         </div>
       </div>
    </div>
    @endif
    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi') || !auth()->user()->hasRole('Karyawan'))
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-light shadow-sm">
          <i class="bi bi-clock-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Clock In</span>
          <span class="info-box-number">
            @if($todayAttendance && $todayAttendance->check_in_time)
              {{ $todayAttendance->check_in_time->format('H:i') }}
            @else
              --
            @endif
          </span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-dark shadow-sm">
          <i class="bi bi-clock-history"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Clock Out</span>
          <span class="info-box-number">
            @if($todayAttendance && $todayAttendance->check_out_time)
              {{ $todayAttendance->check_out_time->format('H:i') }}
            @else
              --
            @endif
          </span>
        </div>
      </div>
    </div>
  </div>
  @endif
  @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi') || !auth()->user()->hasRole('Karyawan'))
  <!-- Lokasi & Jadwal Hari Ini -->
  <div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-header">
          <h5 class="card-title mb-0">Lokasi & Jadwal Hari Ini</h5>
        </div>
        <div class="card-body">
          <div class="row">
           <div class="col-md-4">
             <div class="fw-semibold">Lokasi</div>
             <div class="text-muted">{{ $userLocationName ?? '-' }}</div>
           </div>
           <div class="col-md-4">
             <div class="fw-semibold">Shift (rencana)</div>
             <div class="text-muted">
                @if(isset($todayAssignmentTime) && $todayAssignmentTime === 'Hari libur Anda')
                  {{ $todayAssignmentTime }}
                @elseif($todayAssignment && $todayAssignment->shift)
                  {{ $todayAssignment->shift->name }} @if(!empty($todayAssignmentTime)) ({{ $todayAssignmentTime }}) @endif
                @elseif(!empty($todayAssignmentTime))
                  {{ $todayAssignmentTime }}
                @else
                  --
                @endif
                @if(!empty($todayPlannedDate))
                  <div class="text-muted small">Tanggal: {{ $todayPlannedDate }}</div>
                @endif
              </div>
            </div>
            <div class="col-md-4">
              <div class="fw-semibold">Clock In / Clock Out</div>
              <div class="text-muted">
                @php
                  $ci = $todayAttendance && $todayAttendance->check_in_time ? $todayAttendance->check_in_time->format('H:i') : '--';
                  $co = $todayAttendance && $todayAttendance->check_out_time ? $todayAttendance->check_out_time->format('H:i') : '--';
                @endphp
                @if(!empty($todayPlannedDate))
                  <div class="text-muted small">{{ $todayPlannedDate }}</div>
                @endif
                {{ $ci }} / {{ $co }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
@endif

@if($user->hasRole('Admin Lokasi') && $attendanceList && $attendanceList->count() > 0)
  <div class="row mt-3">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
          <h5 class="card-title mb-0">Clock In/Out Hari Ini</h5>
          <small class="text-muted">Data sesuai lokasi Anda.</small>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle mb-0">
              <thead>
                <tr>
                  @if($user->hasRole('Admin Lokasi'))
                    <th>Nama</th>
                  @endif
                  <th>Clock In</th>
                  <th>Clock Out</th>
                </tr>
              </thead>
              <tbody>
                @foreach($attendanceList as $att)
                  <tr>
                    @if($user->hasRole('Admin Lokasi'))
                      <td class="text-break">{{ optional($att->user)->name ?? 'Unknown' }}</td>
                    @endif
                    <td>{{ $att->check_in_time ? $att->check_in_time->format('H:i') : '--' }}</td>
                    <td>{{ $att->check_out_time ? $att->check_out_time->format('H:i') : '--' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endif

@if(!$user->hasRole('Karyawan'))
  <!-- Additional Info boxes -->
  <div class="row">
    <div class="col-12 col-sm-6 col-md-4">
      <div class="info-box">
        <span class="info-box-icon text-bg-info shadow-sm">
          <i class="bi bi-people-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Users</span>
          <span class="info-box-number">{{ $totalUsers }}</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
      <div class="info-box">
        <span class="info-box-icon text-bg-secondary shadow-sm">
          <i class="bi bi-building-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Divisions</span>
          <span class="info-box-number">{{ $totalDivisions }}</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
      <div class="info-box">
        <span class="info-box-icon text-bg-dark shadow-sm">
          <i class="bi bi-person-badge-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Employees</span>
          <span class="info-box-number">{{ $totalKaryawans }}</span>
        </div>
      </div>
    </div>
  </div>
  <!-- Location Performance Metrics -->
  <div class="row">
    <div class="col-12 col-sm-6 col-md-4">
      <div class="info-box">
        <span class="info-box-icon text-bg-primary shadow-sm">
          <i class="bi bi-clipboard2-check"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Task Completion Rate</span>
          <span class="info-box-number">{{ number_format($locationMetrics['task_completion_rate'] ?? 0, 2) }}%</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
      <div class="info-box">
        <span class="info-box-icon text-bg-success shadow-sm">
          <i class="bi bi-person-check-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Attendance Rate (Today)</span>
          <span class="info-box-number">{{ number_format($locationMetrics['attendance_rate_today'] ?? 0, 2) }}%</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
      <div class="info-box">
        <span class="info-box-icon text-bg-warning shadow-sm">
          <i class="bi bi-alarm-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Overtime (Last 30d)</span>
          <span class="info-box-number">{{ number_format($locationMetrics['overtime_hours_30d'] ?? 0, 2) }} hrs</span>
        </div>
      </div>
    </div>
  </div>
@endif
@if(!$user->hasRole('Karyawan'))
  <!--begin::Row-->
  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title">Monthly Recap Report</h5>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
              <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
              <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
            </button>
            <div class="btn-group">
              <button
                type="button"
                class="btn btn-tool dropdown-toggle"
                data-bs-toggle="dropdown"
              >
                <i class="bi bi-wrench"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" role="menu">
                <a href="#" class="dropdown-item">Action</a>
                <a href="#" class="dropdown-item">Another action</a>
                <a href="#" class="dropdown-item"> Something else here </a>
                <a class="dropdown-divider"></a>
                <a href="#" class="dropdown-item">Separated link</a>
              </div>
            </div>
            <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <!-- Search Form -->
          <div class="mb-3">
            <form method="GET" action="{{ route('dashboard') }}" class="d-flex">
              <input type="text" name="search" class="form-control me-2" placeholder="Search by task title or assignee name..." value="{{ request('search') }}">
              <button type="submit" class="btn btn-outline-primary">Search</button>
              @if(request('search'))
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary ms-2">Clear</a>
              @endif
            </form>
          </div>
          @if($tasks->hasPages())
            <div class="d-flex justify-content-center mt-4">
              {{ $tasks->appends(request()->query())->links() }}
            </div>
          @endif
          <div class="table-responsive">
            @if($user->hasRole('Super Admin'))
              <table class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th>Employee Name</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Due Date</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($tasks as $task)
                    <tr>
                      <td class="text-break">{{ $task->assignee->name ?? 'Unassigned' }}</td>
                      <td class="text-break">{{ $task->title }}</td>
                      <td class="text-break">{{ Str::limit($task->description, 50) }}</td>
                      <td>
                        @switch($task->status)
                          @case('pending')
                            <span class="badge text-bg-warning">Pending</span>
                            @break
                          @case('in_progress')
                            <span class="badge text-bg-info">In Progress</span>
                            @break
                          @case('completed')
                            <span class="badge text-bg-success">Completed</span>
                            @break
                          @default
                            <span class="badge text-bg-secondary">{{ $task->status }}</span>
                        @endswitch
                      </td>
                      <td class="text-break">{{ $task->due_date ? $task->due_date->format('Y-m-d') : 'No due date' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @else
              <table class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Due Date</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($tasks as $task)
                    <tr>
                      <td class="text-break">{{ $task->title }}</td>
                      <td class="text-break">{{ Str::limit($task->description, 50) }}</td>
                      <td>
                        @switch($task->status)
                          @case('pending')
                            <span class="badge text-bg-warning">Pending</span>
                            @break
                          @case('in_progress')
                            <span class="badge text-bg-info">In Progress</span>
                            @break
                          @case('completed')
                            <span class="badge text-bg-success">Completed</span>
                            @break
                          @default
                            <span class="badge text-bg-secondary">{{ $task->status }}</span>
                        @endswitch
                      </td>
                      <td class="text-break">{{ $task->due_date ? $task->due_date->format('Y-m-d') : 'No due date' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @endif
          </div>
        </div>
        <!-- ./card-body -->
        @if($tasks->hasPages())
          <div class="d-flex justify-content-center mt-3">
            {{ $tasks->appends(request()->query())->links() }}
          </div>
        @endif
        
      </div>
      <!-- /.card -->
    </div>
    <!-- /.col -->
  </div>
  <!--end::Row-->
@endif
<!--begin::Row-->

<!--end::Row-->
<!--begin::Row: Quick Actions and Info-->
<div class="row mt-3">
  <div class="col-md-4">
    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi') || !auth()->user()->hasRole('Karyawan'))
    <div class="card">
      <div class="card-header"><h3 class="card-title">Quick Actions</h3></div>
      <div class="card-body">
        <div class="d-grid gap-2">
          <a class="btn btn-outline-primary" href="{{ route('attendance.checkin') }}">Attendance: Check In/Out</a>
          @if($user->hasRole('Admin Lokasi') && $user->location_id)
            <a class="btn btn-outline-secondary" href="{{ route('locations.settings', $user->location_id) }}">My Location Settings</a>
          @endif
          <a class="btn btn-outline-info" href="{{ route('tasks.create') }}">Create Task</a>
        </div>
      </div>
    </div>
    @endif
  </div>
  @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi') || !auth()->user()->hasRole('Karyawan'))
  <div class="col-md-8">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Messages</h3></div>
      <div class="card-body">
        <p class="mb-2">Unread: <strong>{{ $unreadMessages }}</strong></p>
        <a class="btn btn-sm btn-primary" href="{{ route('messages.index') }}">Open Messages</a>

        @if(!auth()->user()->hasTwoFactorEnabled())
            <div class="alert alert-warning mt-3">
                <strong>Security Recommendation:</strong> Enable two-factor authentication to better protect your account.
                <a href="{{ route('2fa.setup') }}" class="btn btn-sm btn-warning ms-2">Enable 2FA</a>
            </div>
        @else
            <div class="alert alert-success mt-3">
                <strong>✓ Two-Factor Authentication Enabled</strong>
                <small class="d-block">Method: {{ ucfirst(auth()->user()->two_factor_method) }}</small>
                <form action="{{ route('2fa.disable') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger ms-2"
                            onclick="return confirm('Are you sure you want to disable 2FA?')">Disable 2FA</button>
                </form>
            </div>
        @endif
      </div>
    </div>
  </div>
  @endif
</div>
<!--end::Row-->
@if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi') || !auth()->user()->hasRole('Karyawan'))
<!--begin::Row: Notices-->
<div class="row mt-3">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Notifikasi Libur / Izin</h3></div>
      <div class="card-body">
        @if(!empty($todayNotices))
          <div class="mb-2">
            <strong>Hari ini:</strong>
            <ul class="mb-0">
              @foreach($todayNotices as $n)
                <li>{{ $n }}</li>
              @endforeach
            </ul>
          </div>
        @else
          <p class="mb-2 text-muted">Tidak ada notifikasi untuk hari ini.</p>
        @endif

        @if(!empty($upcomingNotices))
          <div class="mt-2">
            <strong>Mendatang:</strong>
            <ul class="mb-0">
              @foreach($upcomingNotices as $item)
                <li>
                  <span class="badge text-bg-light">{{ $item['date'] }}</span>
                  @foreach($item['labels'] as $label)
                    <span class="badge text-bg-secondary">{{ $label }}</span>
                  @endforeach
                </li>
              @endforeach
            </ul>
          </div>
        @else
          <p class="mb-0 text-muted">Tidak ada notifikasi dalam 14 hari ke depan.</p>
        @endif
      </div>
    </div>
  </div>
  <!-- /.col -->
</div>
<!--end::Row-->
@endif
@endsection

@section('scripts')
@endsection
