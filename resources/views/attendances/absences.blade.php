@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
  <div>
    <h3 class="mb-1">Absence Report</h3>
    <p class="text-muted mb-0">Rekap absensi berdasarkan filter.</p>
  </div>
  <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="card">
  <div class="card-header"><h3 class="card-title">Filter</h3></div>
  <div class="card-body">
    <form method="GET" class="row g-2">
      <div class="col-md-3">
        <label class="form-label">Start Date</label>
        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control"/>
      </div>
      <div class="col-md-3">
        <label class="form-label">End Date</label>
        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control"/>
      </div>
      @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi'))
      <div class="col-md-3">
        <label class="form-label">User</label>
        <select name="user_id" class="form-select">
          <option value="">All</option>
          @php
            $uQuery = \App\Models\User::orderBy('name');
            if(auth()->user()->hasRole('Admin Lokasi')){ $uQuery->where('location_id', auth()->user()->location_id); }
            $users = $uQuery->get();
          @endphp
          @foreach($users as $u)
            <option value="{{ $u->id }}" @selected(request('user_id')==$u->id)>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>
      @endif
      <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-primary" type="submit">Apply</button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header"><h3 class="card-title">Absences</h3></div>
  <div class="card-body table-responsive">
    <table class="table table-bordered table-striped table-sm">
      <thead>
        <tr>
          <th style="width:50px">No</th>
          <th>Date</th>
          <th>User</th>
          <th>Location</th>
          <th>Type</th>
          <th>Notes</th>
        </tr>
      </thead>
      <tbody>
        @forelse($absences as $a)
          <tr>
            <td>{{ $loop->iteration + ($absences->currentPage()-1)*$absences->perPage() }}</td>
            <td>{{ $a->date->format('Y-m-d') }}</td>
            <td>{{ optional($a->user)->name }}</td>
            <td>{{ optional($a->location)->name }}</td>
            <td><span class="badge text-bg-danger">{{ strtoupper($a->type) }}</span></td>
            <td>{{ $a->notes }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center">No data</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="mt-2">{{ $absences->links() }}</div>
  </div>
  
</div>
@endsection
