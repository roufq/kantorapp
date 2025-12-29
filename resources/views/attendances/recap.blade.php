@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
  <div>
    <h3 class="mb-1">Attendance Recap</h3>
    <p class="text-muted mb-0">Ringkasan kehadiran berdasarkan filter.</p>
  </div>
</div>
<div class="card">
  <div class="card-header"><h3 class="card-title">Filter</h3></div>
  <div class="card-body">
    <form method="GET" class="row g-2">
      <div class="col-md-3">
        <label class="form-label">Start Date</label>
        <input type="date" name="start_date" class="form-control" value="{{ request('start_date', $start) }}"/>
      </div>
      <div class="col-md-3">
        <label class="form-label">End Date</label>
        <input type="date" name="end_date" class="form-control" value="{{ request('end_date', $end) }}"/>
      </div>
      @if(auth()->user()->hasRole('Super Admin'))
      <div class="col-md-3">
        <label class="form-label">Location</label>
        <select name="location_id" class="form-select">
          <option value="">All</option>
          @foreach($locations as $loc)
            <option value="{{ $loc->id }}" @selected(request('location_id')==$loc->id)>{{ $loc->name }}</option>
          @endforeach
        </select>
      </div>
      @endif
      <div class="col-md-3">
        <label class="form-label">User (opsional)</label>
        @php
          $uQuery = \App\Models\User::orderBy('name');
          if(auth()->user()->hasRole('Admin Lokasi')){ $uQuery->where('location_id', auth()->user()->location_id); }
          if(auth()->user()->hasRole('Karyawan')){ $uQuery->where('id', auth()->id()); }
          $users = $uQuery->get();
        @endphp
        <select name="user_id" class="form-select">
          <option value="">All</option>
          @foreach($users as $u)
            <option value="{{ $u->id }}" @selected(request('user_id')==$u->id)>{{ $u->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-12 d-flex align-items-end gap-2 mt-3">
    <button class="btn btn-primary" type="submit">Apply</button>
    <button class="btn btn-success" type="submit" formaction="{{ route('attendance.recap.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}">Export Excel</button>
    <button class="btn btn-outline-success" type="submit" formaction="{{ route('attendance.recap.export', array_merge(request()->query(), ['format' => 'csv'])) }}">Export CSV</button>
  </div>
</form>
  </div>
</div>

<div class="card">
  <div class="card-header"><h3 class="card-title">Recap</h3></div>
  <div class="card-body table-responsive">
    <table class="table table-bordered table-striped table-sm">
      <thead>
        <tr>
          <th style="width:50px">No</th>
          <th>User</th>
          <th>Location</th>
          <th>Total Days</th>
          <th>Holiday</th>
          <th>Weekly Off</th>
          <th>Leave</th>
          <th>Working Days</th>
          <th>Present</th>
          <th>Alfa</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $r['user']->name }}</td>
          <td>{{ optional($r['location'])->name }}</td>
          <td>{{ $r['totalDays'] }}</td>
          <td>{{ $r['holidayDays'] }}</td>
          <td>{{ $r['weeklyOffDays'] }}</td>
          <td>{{ $r['leaveDays'] }}</td>
          <td>{{ $r['workingDays'] }}</td>
          <td>{{ $r['presentDays'] }}</td>
          <td>{{ $r['alphaDays'] }}</td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center">No data</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
