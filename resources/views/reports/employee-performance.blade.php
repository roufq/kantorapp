@extends('layouts.appnew')

@section('content')
@php
  $daysOptions = [7, 30, 90, 180];
@endphp
<div class="bg-light p-3 mb-3 rounded border">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
      <h1 class="h4 mb-1">Performa Karyawan</h1>
      <p class="text-muted mb-0">Ringkas KPI per karyawan dalam periode terpilih.</p>
    </div>
    <div>
      <a href="{{ route('reports.index') }}" class="text-decoration-none">Kembali ke Laporan</a>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
      <div class="text-muted small">Export ringkas performa karyawan sesuai filter.</div>
      <a class="btn btn-sm btn-success" href="{{ route('reports.employee-performance.export', request()->query()) }}">
        Export Excel
      </a>
    </div>
    <form method="GET" action="{{ route('reports.employee-performance') }}" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label fw-semibold">Periode</label>
        <select name="days" class="form-select">
          @foreach($daysOptions as $opt)
            <option value="{{ $opt }}" {{ (int) $days === $opt ? 'selected' : '' }}>{{ $opt }} hari</option>
          @endforeach
        </select>
      </div>
      @if(!empty($locations))
        <div class="col-md-3">
          <label class="form-label fw-semibold">Lokasi</label>
          <select name="location_id" class="form-select">
            <option value="">Semua</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" {{ (string) $locationId === (string) $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
            @endforeach
          </select>
        </div>
      @endif
      <div class="col-md-3">
        <label class="form-label fw-semibold">Karyawan</label>
        <select name="employee_id" class="form-select">
          <option value="">Semua</option>
          @foreach($employeeOptions as $emp)
            <option value="{{ $emp->id }}" {{ (string) $employeeId === (string) $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Cari Nama</label>
        <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Nama karyawan">
      </div>
      <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Terapkan</button>
        <a href="{{ route('reports.employee-performance') }}" class="btn btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped align-middle mb-0">
        <thead>
          <tr>
            <th style="width:50px">No</th>
            <th>Nama</th>
            <th>Lokasi</th>
            <th>Kehadiran</th>
            <th>Keterlambatan</th>
            <th>Overtime</th>
            <th>Produktivitas Tugas</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $emp)
            @php
              $metrics = $metricsByUser[$emp->id] ?? [
                'attendance_rate' => 0,
                'attendance_days' => 0,
                'attendance_count' => 0,
                'late_count' => 0,
                'lateness_rate' => 0,
                'overtime_hours' => 0,
                'tasks_created' => 0,
                'tasks_completed' => 0,
                'task_productivity_rate' => 0,
              ];
            @endphp
            <tr>
              <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
              <td class="text-break">{{ $emp->name }}</td>
              <td>{{ optional($emp->location)->name ?? '-' }}</td>
              <td>
                <span class="fw-semibold">{{ number_format($metrics['attendance_rate'], 2) }}%</span>
                <div class="text-muted small">{{ $metrics['attendance_days'] }} hari</div>
              </td>
              <td>
                <span class="fw-semibold">{{ number_format($metrics['lateness_rate'], 2) }}%</span>
                <div class="text-muted small">{{ $metrics['late_count'] }} dari {{ $metrics['attendance_count'] }}</div>
              </td>
              <td>
                <span class="fw-semibold">{{ number_format($metrics['overtime_hours'], 2) }} jam</span>
              </td>
              <td>
                <span class="fw-semibold">{{ number_format($metrics['task_productivity_rate'], 2) }}%</span>
                <div class="text-muted small">{{ $metrics['tasks_completed'] }} / {{ $metrics['tasks_created'] }}</div>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted">Tidak ada data karyawan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($users->hasPages())
    <div class="p-3">
      {{ $users->links() }}
    </div>
  @endif
</div>
@endsection
