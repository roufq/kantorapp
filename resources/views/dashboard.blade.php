@extends('layouts.appnew')
@section('title')
<div class="row">
  <div class="col-sm-12">
    <div class="page-title-box">
      <div class="btn-group float-right">
        <ol class="breadcrumb hide-phone p-0 m-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </div>
      <h4 class="page-title">Dashboard</h4>
    </div>
  </div>
</div>
@endsection
@section('content')
@php
  $statCards = [];
  $secondaryCards = [];
  if($user->hasRole('Super Admin')) {
    $statCards = [
      ['label' => 'Total Super Admins', 'value' => $totalMasters, 'icon' => 'mdi-account-key', 'color' => 'primary'],
      ['label' => 'Total Employees', 'value' => $totalEmployees, 'icon' => 'mdi-account-group', 'color' => 'danger'],
      ['label' => 'Total Tasks', 'value' => $totalTasks, 'icon' => 'mdi-check-circle', 'color' => 'success'],
      ['label' => 'Unread Messages', 'value' => $unreadMessages, 'icon' => 'mdi-email-outline', 'color' => 'warning'],
    ];
  } elseif($user->hasRole('Admin Lokasi')) {
    $statCards = [
      ['label' => 'Employees (My Location)', 'value' => $totalKaryawans, 'icon' => 'mdi-account-group', 'color' => 'danger'],
      ['label' => 'Total Tasks', 'value' => $totalTasks, 'icon' => 'mdi-check-circle', 'color' => 'success'],
      ['label' => 'Unread Messages', 'value' => $unreadMessages, 'icon' => 'mdi-email-outline', 'color' => 'warning'],
      ['label' => 'Absences Today', 'value' => $absencesTodayCount ?? 0, 'icon' => 'mdi-alert-circle', 'color' => 'info'],
    ];
    $secondaryCards = [
      ['label' => 'Clock In (Saya)', 'value' => ($todayAttendance && $todayAttendance->check_in_time) ? $todayAttendance->check_in_time->format('H:i') : '--', 'icon' => 'mdi-clock-start', 'color' => 'info'],
      ['label' => 'Clock Out (Saya)', 'value' => ($todayAttendance && $todayAttendance->check_out_time) ? $todayAttendance->check_out_time->format('H:i') : '--', 'icon' => 'mdi-clock-end', 'color' => 'dark'],
    ];
  } else {
    $statCards = [
      ['label' => 'My Tasks', 'value' => $totalTasks, 'icon' => 'mdi-check-circle', 'color' => 'success'],
    ];
    if($user->hasRole('Super Admin') || $user->hasRole('Admin Lokasi') || !$user->hasRole('Karyawan')) {
      $statCards[] = ['label' => 'Unread Messages', 'value' => $unreadMessages, 'icon' => 'mdi-email-outline', 'color' => 'warning'];
      $statCards[] = ['label' => 'Today\'s Shift', 'value' => '', 'icon' => 'mdi-calendar-clock', 'color' => 'info', 'extra' => true];
      $statCards[] = ['label' => 'Clock In', 'value' => ($todayAttendance && $todayAttendance->check_in_time) ? $todayAttendance->check_in_time->format('H:i') : '--', 'icon' => 'mdi-clock-start', 'color' => 'secondary'];
      $secondaryCards = [
        ['label' => 'Clock Out', 'value' => ($todayAttendance && $todayAttendance->check_out_time) ? $todayAttendance->check_out_time->format('H:i') : '--', 'icon' => 'mdi-clock-end', 'color' => 'dark'],
      ];
    }
  }
@endphp

@if(!empty($statCards))
  <div class="row">
    @foreach($statCards as $card)
      <div class="col-md-6 col-xl-3">
        <div class="card m-b-30">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center bg-{{ $card['color'] ?? 'primary' }} text-white">
                  <i class="mdi {{ $card['icon'] ?? 'mdi-information' }}"></i>
                </div>
              </div>
              <div class="flex-grow-1 text-right">
                <p class="text-muted mb-1">{{ $card['label'] }}</p>
                @if(!empty($card['extra']))
                  <h5 class="mb-0">
                    @if(isset($todayAssignmentTime) && $todayAssignmentTime === 'Hari libur Anda')
                      {{ $todayAssignmentTime }}
                    @elseif($todayAssignment && $todayAssignment->shift)
                      {{ $todayAssignment->shift->name }}
                      @if(!empty($todayAssignmentTime))
                        <span class="d-block text-muted small">{{ $todayAssignmentTime }}</span>
                      @endif
                    @elseif(!empty($todayAssignmentTime))
                      <span class="text-muted small">{{ $todayAssignmentTime }}</span>
                    @else
                      --
                    @endif
                  </h5>
                @else
                  <h4 class="mb-0">{{ $card['value'] }}</h4>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif

@if(!empty($secondaryCards))
  <div class="row">
    @foreach($secondaryCards as $card)
      <div class="col-md-6 col-xl-3">
        <div class="card m-b-30">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center bg-{{ $card['color'] ?? 'primary' }} text-white">
                  <i class="mdi {{ $card['icon'] ?? 'mdi-information' }}"></i>
                </div>
              </div>
              <div class="flex-grow-1 text-right">
                <p class="text-muted mb-1">{{ $card['label'] }}</p>
                <h4 class="mb-0">{{ $card['value'] }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif

@if(auth()->user()->hasRole('Karyawan'))
  <div class="row">
    <div class="col-md-12">
      <div class="card m-b-30">
        <div class="card-header">
          <h5 class="card-title mb-0">Lokasi & Jadwal Hari Ini</h5>
        </div>
        <div class="card-body">
          <div class="row">
           <div class="col-md-4">
             <div class="font-weight-bold">Lokasi</div>
             <div class="text-muted">{{ $userLocationName ?? '-' }}</div>
           </div>
           <div class="col-md-4">
             <div class="font-weight-bold">Shift (rencana)</div>
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
              <div class="font-weight-bold">Clock In / Clock Out</div>
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
                  <th style="width:50px">No</th>
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
                    <td>{{ $loop->iteration }}</td>
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
  @php
    $extraStats = [
      ['label' => 'Total Users', 'value' => $totalUsers, 'icon' => 'mdi-account-multiple', 'color' => 'info'],
      ['label' => 'Total Divisions', 'value' => $totalDivisions, 'icon' => 'mdi-office-building', 'color' => 'secondary'],
      ['label' => 'Total Employees', 'value' => $totalKaryawans, 'icon' => 'mdi-account-badge-horizontal', 'color' => 'dark'],
    ];
    $performanceStats = [
      ['label' => 'Task Completion Rate', 'value' => number_format($locationMetrics['task_completion_rate'] ?? 0, 2) . '%', 'icon' => 'mdi-clipboard-check', 'color' => 'primary'],
      ['label' => 'Attendance Rate (Today)', 'value' => number_format($locationMetrics['attendance_rate_today'] ?? 0, 2) . '%', 'icon' => 'mdi-account-check', 'color' => 'success'],
      ['label' => 'Overtime (Last 30d)', 'value' => number_format($locationMetrics['overtime_hours_30d'] ?? 0, 2) . ' hrs', 'icon' => 'mdi-timer', 'color' => 'warning'],
    ];
  @endphp
  <div class="row">
    @foreach($extraStats as $card)
      <div class="col-md-6 col-xl-4">
        <div class="card m-b-30">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center bg-{{ $card['color'] ?? 'primary' }} text-white">
                  <i class="mdi {{ $card['icon'] ?? 'mdi-information' }}"></i>
                </div>
              </div>
              <div class="flex-grow-1 text-right">
                <p class="text-muted mb-1">{{ $card['label'] }}</p>
                <h4 class="mb-0">{{ $card['value'] }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
  <div class="row">
    @foreach($performanceStats as $card)
      <div class="col-md-6 col-xl-4">
        <div class="card m-b-30">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center bg-{{ $card['color'] ?? 'primary' }} text-white">
                  <i class="mdi {{ $card['icon'] ?? 'mdi-information' }}"></i>
                </div>
              </div>
              <div class="flex-grow-1 text-right">
                <p class="text-muted mb-1">{{ $card['label'] }}</p>
                <h4 class="mb-0">{{ $card['value'] }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif
@if(!$user->hasRole('Karyawan'))
  <!--begin::Row-->
  <div class="row" id="monthly-recap">
    <div class="col-md-12">
      <div class="card mb-4" id="monthly-recap-card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title">Monthly Recap Report</h5>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-report-toggle="collapse" aria-label="Collapse">
              <i class="bi bi-dash-lg collapse-icon"></i>
              <i class="bi bi-plus-lg expand-icon d-none"></i>
            </button>
            <div class="btn-group">
              <button
                type="button"
                class="btn btn-tool dropdown-toggle"
                data-bs-toggle="dropdown"
                aria-label="Actions"
              >
                <i class="bi bi-wrench"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" role="menu">
                <a href="#" class="dropdown-item report-action" data-report-action="refresh">Refresh data</a>
                <a href="#" class="dropdown-item report-action" data-report-action="clear-search">Clear search</a>
                <a href="#" class="dropdown-item report-action" data-report-action="export-csv">Export CSV (visible)</a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item report-action" data-report-action="print">Print table</a>
              </div>
            </div>
            <button type="button" class="btn btn-tool" data-report-toggle="remove" aria-label="Close">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body" id="monthly-recap-body">
          <!-- Search Form -->
          <div class="mb-3">
            <form method="GET" action="{{ route('dashboard') }}" class="d-flex" id="monthly-recap-search">
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
          <div class="table-responsive" id="monthly-recap-table">
            @if($user->hasRole('Super Admin'))
              <table class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th style="width:50px">No</th>
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
                      <td>{{ $loop->iteration }}</td>
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
                    <th style="width:50px">No</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Due Date</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($tasks as $task)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
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
    <div class="card m-b-30">
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
    <div class="card m-b-30">
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
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const recapCard = document.getElementById('monthly-recap-card');
    if (!recapCard) return;

    const body = document.getElementById('monthly-recap-body');
    const searchForm = document.getElementById('monthly-recap-search');
    const tableWrap = document.getElementById('monthly-recap-table');
    const collapseBtn = recapCard.querySelector('[data-report-toggle="collapse"]');
    const removeBtn = recapCard.querySelector('[data-report-toggle="remove"]');

    function toggleCollapse() {
      if (!body) return;
      const collapsed = body.classList.toggle('d-none');
      collapseBtn.querySelector('.collapse-icon')?.classList.toggle('d-none', collapsed);
      collapseBtn.querySelector('.expand-icon')?.classList.toggle('d-none', !collapsed);
    }

    function removeCard() {
      recapCard.classList.add('d-none');
    }

    collapseBtn?.addEventListener('click', toggleCollapse);
    removeBtn?.addEventListener('click', removeCard);

    recapCard.querySelectorAll('.report-action').forEach(function (actionLink) {
      actionLink.addEventListener('click', function (e) {
        e.preventDefault();
        const action = this.dataset.reportAction;
        if (action === 'refresh') {
          window.location.reload();
        } else if (action === 'clear-search' && searchForm) {
          const input = searchForm.querySelector('input[name="search"]');
          if (input) {
            input.value = '';
            searchForm.submit();
          }
        } else if (action === 'export-csv') {
          exportTableToCsv();
        } else if (action === 'print') {
          window.print();
        }
      });
    });

    function exportTableToCsv() {
      if (!tableWrap) return;
      const table = tableWrap.querySelector('table');
      if (!table) return;
      let csv = [];
      table.querySelectorAll('tr').forEach(function (row) {
        const cols = Array.from(row.querySelectorAll('th,td')).map(function (cell) {
          return '"' + (cell.innerText || '').replace(/"/g, '""') + '"';
        });
        csv.push(cols.join(','));
      });
      const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = 'monthly-recap.csv';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
    }
  });
</script>
@endsection
