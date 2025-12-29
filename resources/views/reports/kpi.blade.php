@extends('layouts.appnew')

@section('content')
@php
  $sections = ['lateness', 'attendance', 'overtime', 'tasks'];
  $activeSection = in_array($section ?? '', $sections, true) ? $section : 'lateness';
  $daysOptions = [7, 30, 90, 180];
@endphp

<div class="bg-light p-3 mb-3 rounded border">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
      <h1 class="h4 mb-1">KPI Dasar</h1>
      <p class="text-muted mb-0">Keterlambatan, kehadiran, overtime, dan produktivitas tugas.</p>
    </div>
    <div>
      <a href="{{ route('dashboard') }}" class="text-decoration-none">Kembali ke Dashboard</a>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('kpi.index') }}" class="d-flex flex-wrap align-items-end gap-2">
      <div>
        <label class="form-label fw-semibold mb-1">Periode KPI</label>
        <select name="days" class="form-select">
          @foreach($daysOptions as $opt)
            <option value="{{ $opt }}" {{ (int) $days === $opt ? 'selected' : '' }}>{{ $opt }} hari</option>
          @endforeach
        </select>
      </div>
      <input type="hidden" name="section" value="{{ $activeSection }}">
      <button type="submit" class="btn btn-primary">Terapkan</button>
    </form>
  </div>
</div>

@php
  $summaryCards = [
    [
      'label' => 'Keterlambatan',
      'value' => number_format($kpiMetrics['lateness_rate'] ?? 0, 2) . '%',
      'meta' => ($kpiMetrics['late_count'] ?? 0) . ' / ' . ($kpiMetrics['attendance_count'] ?? 0),
      'color' => 'danger',
      'icon' => 'mdi-clock-alert',
    ],
    [
      'label' => 'Kehadiran',
      'value' => number_format($kpiMetrics['attendance_rate'] ?? 0, 2) . '%',
      'meta' => ($kpiMetrics['attendance_count'] ?? 0) . ' hadir',
      'color' => 'success',
      'icon' => 'mdi-account-check',
    ],
    [
      'label' => 'Overtime Disetujui',
      'value' => number_format($kpiMetrics['overtime_hours'] ?? 0, 2) . ' jam',
      'meta' => $kpiMetrics['period_label'] ?? '',
      'color' => 'warning',
      'icon' => 'mdi-timer',
    ],
    [
      'label' => 'Produktivitas Tugas',
      'value' => number_format($kpiMetrics['task_productivity_rate'] ?? 0, 2) . '%',
      'meta' => ($kpiMetrics['tasks_completed'] ?? 0) . ' / ' . ($kpiMetrics['tasks_created'] ?? 0),
      'color' => 'primary',
      'icon' => 'mdi-clipboard-check',
    ],
  ];
@endphp

<div class="row">
  @foreach($summaryCards as $card)
    <div class="col-md-6 col-xl-3">
      <div class="card m-b-30">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
              <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center bg-{{ $card['color'] }} text-white">
                <i class="mdi {{ $card['icon'] }}"></i>
              </div>
            </div>
            <div class="flex-grow-1 text-right">
              <p class="text-muted mb-1">{{ $card['label'] }}</p>
              <h4 class="mb-0">{{ $card['value'] }}</h4>
              @if(!empty($card['meta']))
                <small class="text-muted">{{ $card['meta'] }}</small>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  @endforeach
</div>

<div class="card">
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link {{ $activeSection === 'lateness' ? 'active' : '' }}" data-toggle="tab" href="#tab-lateness" role="tab">Keterlambatan</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $activeSection === 'attendance' ? 'active' : '' }}" data-toggle="tab" href="#tab-attendance" role="tab">Kehadiran</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $activeSection === 'overtime' ? 'active' : '' }}" data-toggle="tab" href="#tab-overtime" role="tab">Overtime</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $activeSection === 'tasks' ? 'active' : '' }}" data-toggle="tab" href="#tab-tasks" role="tab">Produktivitas Tugas</a>
      </li>
    </ul>
  </div>
  <div class="card-body">
    <div class="tab-content">
      <div class="tab-pane fade {{ $activeSection === 'lateness' ? 'show active' : '' }}" id="tab-lateness" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
          <h5 class="mb-0">Daftar Keterlambatan</h5>
          <div class="d-flex gap-2">
            <a href="{{ route('kpi.export', ['section' => 'lateness', 'days' => $days, 'format' => 'xlsx']) }}" class="btn btn-sm btn-success">Export XLSX</a>
            <a href="{{ route('kpi.export', ['section' => 'lateness', 'days' => $days, 'format' => 'csv']) }}" class="btn btn-sm btn-outline-success">Export CSV</a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle mb-0">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Nama</th>
                <th>Lokasi</th>
                <th>Shift</th>
                <th>Check In</th>
                <th>Check Out</th>
              </tr>
            </thead>
            <tbody>
              @forelse($lateAttendances as $att)
                <tr>
                  <td>{{ $loop->iteration + ($lateAttendances->currentPage() - 1) * $lateAttendances->perPage() }}</td>
                  <td>{{ optional($att->user)->name ?? '-' }}</td>
                  <td>{{ optional($att->location)->name ?? '-' }}</td>
                  <td>{{ optional($att->shift)->name ?? '-' }}</td>
                  <td>{{ optional($att->check_in_time)->format('Y-m-d H:i') ?? '-' }}</td>
                  <td>{{ optional($att->check_out_time)->format('Y-m-d H:i') ?? '-' }}</td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center text-muted">Tidak ada data keterlambatan.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($lateAttendances->hasPages())
          <div class="mt-3">
            {{ $lateAttendances->appends(['section' => 'lateness', 'days' => $days])->links() }}
          </div>
        @endif
      </div>

      <div class="tab-pane fade {{ $activeSection === 'attendance' ? 'show active' : '' }}" id="tab-attendance" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
          <h5 class="mb-0">Daftar Kehadiran</h5>
          <div class="d-flex gap-2">
            <a href="{{ route('kpi.export', ['section' => 'attendance', 'days' => $days, 'format' => 'xlsx']) }}" class="btn btn-sm btn-success">Export XLSX</a>
            <a href="{{ route('kpi.export', ['section' => 'attendance', 'days' => $days, 'format' => 'csv']) }}" class="btn btn-sm btn-outline-success">Export CSV</a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle mb-0">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Nama</th>
                <th>Lokasi</th>
                <th>Shift</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Terlambat</th>
              </tr>
            </thead>
            <tbody>
              @forelse($attendanceList as $att)
                <tr>
                  <td>{{ $loop->iteration + ($attendanceList->currentPage() - 1) * $attendanceList->perPage() }}</td>
                  <td>{{ optional($att->user)->name ?? '-' }}</td>
                  <td>{{ optional($att->location)->name ?? '-' }}</td>
                  <td>{{ optional($att->shift)->name ?? '-' }}</td>
                  <td>{{ optional($att->check_in_time)->format('Y-m-d H:i') ?? '-' }}</td>
                  <td>{{ optional($att->check_out_time)->format('Y-m-d H:i') ?? '-' }}</td>
                  <td>{{ $att->is_late ? 'Ya' : 'Tidak' }}</td>
                </tr>
              @empty
                <tr><td colspan="7" class="text-center text-muted">Tidak ada data kehadiran.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($attendanceList->hasPages())
          <div class="mt-3">
            {{ $attendanceList->appends(['section' => 'attendance', 'days' => $days])->links() }}
          </div>
        @endif
      </div>

      <div class="tab-pane fade {{ $activeSection === 'overtime' ? 'show active' : '' }}" id="tab-overtime" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
          <h5 class="mb-0">Overtime Disetujui</h5>
          <div class="d-flex gap-2">
            <a href="{{ route('kpi.export', ['section' => 'overtime', 'days' => $days, 'format' => 'xlsx']) }}" class="btn btn-sm btn-success">Export XLSX</a>
            <a href="{{ route('kpi.export', ['section' => 'overtime', 'days' => $days, 'format' => 'csv']) }}" class="btn btn-sm btn-outline-success">Export CSV</a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle mb-0">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Durasi (jam)</th>
                <th>Alasan</th>
              </tr>
            </thead>
            <tbody>
              @forelse($overtimeList as $ot)
                <tr>
                  <td>{{ $loop->iteration + ($overtimeList->currentPage() - 1) * $overtimeList->perPage() }}</td>
                  <td>{{ optional($ot->user)->name ?? '-' }}</td>
                  <td>{{ optional($ot->date)->format('Y-m-d') ?? '-' }}</td>
                  <td>{{ number_format((float) $ot->duration_hours, 2) }}</td>
                  <td>{{ $ot->reason ?? '-' }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted">Tidak ada data overtime.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($overtimeList->hasPages())
          <div class="mt-3">
            {{ $overtimeList->appends(['section' => 'overtime', 'days' => $days])->links() }}
          </div>
        @endif
      </div>

      <div class="tab-pane fade {{ $activeSection === 'tasks' ? 'show active' : '' }}" id="tab-tasks" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
          <h5 class="mb-0">Tugas Dibuat (Periode)</h5>
          <div class="d-flex gap-2">
            <a href="{{ route('kpi.export', ['section' => 'tasks_created', 'days' => $days, 'format' => 'xlsx']) }}" class="btn btn-sm btn-success">Export XLSX</a>
            <a href="{{ route('kpi.export', ['section' => 'tasks_created', 'days' => $days, 'format' => 'csv']) }}" class="btn btn-sm btn-outline-success">Export CSV</a>
          </div>
        </div>
        <div class="table-responsive mb-4">
          <table class="table table-sm table-striped align-middle mb-0">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Judul</th>
                <th>Assignee</th>
                <th>Status</th>
                <th>Dibuat</th>
                <th>Jatuh Tempo</th>
              </tr>
            </thead>
            <tbody>
              @forelse($tasksCreatedList as $task)
                <tr>
                  <td>{{ $loop->iteration + ($tasksCreatedList->currentPage() - 1) * $tasksCreatedList->perPage() }}</td>
                  <td>{{ $task->title }}</td>
                  <td>{{ optional($task->assignee)->name ?? '-' }}</td>
                  <td>{{ $task->status }}</td>
                  <td>{{ optional($task->created_at)->format('Y-m-d') ?? '-' }}</td>
                  <td>{{ optional($task->due_date)->format('Y-m-d') ?? '-' }}</td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center text-muted">Tidak ada tugas dibuat dalam periode ini.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($tasksCreatedList->hasPages())
          <div class="mb-4">
            {{ $tasksCreatedList->appends(['section' => 'tasks', 'days' => $days])->links() }}
          </div>
        @endif

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
          <h5 class="mb-0">Tugas Selesai (Periode)</h5>
          <div class="d-flex gap-2">
            <a href="{{ route('kpi.export', ['section' => 'tasks_completed', 'days' => $days, 'format' => 'xlsx']) }}" class="btn btn-sm btn-success">Export XLSX</a>
            <a href="{{ route('kpi.export', ['section' => 'tasks_completed', 'days' => $days, 'format' => 'csv']) }}" class="btn btn-sm btn-outline-success">Export CSV</a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle mb-0">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Judul</th>
                <th>Assignee</th>
                <th>Status</th>
                <th>Selesai</th>
                <th>Dibuat</th>
              </tr>
            </thead>
            <tbody>
              @forelse($tasksCompletedList as $task)
                <tr>
                  <td>{{ $loop->iteration + ($tasksCompletedList->currentPage() - 1) * $tasksCompletedList->perPage() }}</td>
                  <td>{{ $task->title }}</td>
                  <td>{{ optional($task->assignee)->name ?? '-' }}</td>
                  <td>{{ $task->status }}</td>
                  <td>{{ optional($task->updated_at)->format('Y-m-d') ?? '-' }}</td>
                  <td>{{ optional($task->created_at)->format('Y-m-d') ?? '-' }}</td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center text-muted">Tidak ada tugas selesai dalam periode ini.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($tasksCompletedList->hasPages())
          <div class="mt-3">
            {{ $tasksCompletedList->appends(['section' => 'tasks', 'days' => $days])->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
