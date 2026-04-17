@extends('layouts.appnew')

@section('content')
@php
  $daysOptions = [7, 30, 90, 180];
@endphp
<div class="bg-light p-3 mb-3 rounded border">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
      <h1 class="h4 mb-1">Employee Performance</h1>
      <p class="text-muted mb-0">KPI summary per employee in the selected period.</p>
    </div>
    <div>
      <a href="{{ route('reports.index') }}" class="text-decoration-none">Back to Reports</a>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
      <div class="text-muted small">Export summary of employee performance based on filters.</div>
      <a class="btn btn-sm btn-success" href="{{ route('reports.employee-performance.export', request()->query()) }}">
        Export Excel
      </a>
    </div>
    <form method="GET" action="{{ route('reports.employee-performance') }}" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label fw-semibold">Period</label>
        <select name="days" class="form-select">
          @foreach($daysOptions as $opt)
            <option value="{{ $opt }}" {{ (int) $days === $opt ? 'selected' : '' }}>{{ $opt }} days</option>
          @endforeach
        </select>
      </div>
      @if(!empty($locations))
        <div class="col-md-3">
          <label class="form-label fw-semibold">Location</label>
          <select name="location_id" class="form-select">
            <option value="">All</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" {{ (string) $locationId === (string) $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
            @endforeach
          </select>
        </div>
      @endif
      <div class="col-md-3">
        <label class="form-label fw-semibold">Employees</label>
        <select name="employee_id" class="form-select">
          <option value="">All</option>
          @foreach($employeeOptions as $emp)
            <option value="{{ $emp->id }}" {{ (string) $employeeId === (string) $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Search Name</label>
        <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Employee Name">
      </div>
      <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Apply</button>
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
            <th>Name</th>
            <th>Location</th>
            <th>Attendance</th>
            <th>Lateness</th>
            <th>Overtime</th>
            <th>Min Presence</th>
            <th>Output Efficiency</th>
            <th>Task Productivity</th>
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
                'output_points' => 0,
                'target_points' => 0,
                'output_efficiency_rate' => 0,
                'attendance_minutes' => 0,
                'min_attendance_minutes' => 0,
                'min_attendance_met' => null,
              ];
            @endphp
            <tr>
              <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
              <td class="text-break">{{ $emp->name }}</td>
              <td>{{ optional($emp->location)->name ?? '-' }}</td>
              <td>
                <span class="fw-semibold">{{ number_format($metrics['attendance_rate'], 2) }}%</span>
                <div class="text-muted small">{{ $metrics['attendance_days'] }} days</div>
              </td>
              <td>
                <span class="fw-semibold">{{ number_format($metrics['lateness_rate'], 2) }}%</span>
                <div class="text-muted small">{{ $metrics['late_count'] }} of {{ $metrics['attendance_count'] }}</div>
              </td>
              <td>
                <span class="fw-semibold">{{ number_format($metrics['overtime_hours'], 2) }} hrs</span>
              </td>
              <td>
                @if(is_null($metrics['min_attendance_met']))
                  <span class="text-muted">-</span>
                @else
                  <span class="fw-semibold">{{ $metrics['attendance_minutes'] }} / {{ $metrics['min_attendance_minutes'] }} mins</span>
                  <div class="text-muted small">{{ $metrics['min_attendance_met'] ? 'Met' : 'Not Met' }}</div>
                @endif
              </td>
              <td>
                <span class="fw-semibold">{{ number_format($metrics['output_efficiency_rate'], 2) }}%</span>
                <div class="text-muted small">{{ number_format((float) $metrics['output_points'], 2) }} / {{ $metrics['target_points'] }} points</div>
              </td>
              <td>
                <span class="fw-semibold">{{ number_format($metrics['task_productivity_rate'], 2) }}%</span>
                <div class="text-muted small">{{ $metrics['tasks_completed'] }} / {{ $metrics['tasks_created'] }}</div>
              </td>
            </tr>
          @empty
            <tr><td colspan="9" class="text-center text-muted">No employee data found.</td></tr>
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
