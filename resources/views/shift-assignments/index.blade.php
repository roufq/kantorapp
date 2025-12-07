@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Shift Assignments</h3>
        <div class="d-flex gap-2">
          <a href="{{ route('shift-assignments.export', request()->query()) }}" class="btn btn-outline-success btn-sm">Export</a>
          <a href="{{ route('shift-assignments.calendar') }}" class="btn btn-outline-secondary btn-sm">Kalender</a>
          <a href="{{ route('shift-assignments.create') }}" class="btn btn-primary btn-sm">Create</a>
        </div>
      </div>
      <div class="card-body">
        <form class="row g-2 mb-3" method="GET" action="{{ route('shift-assignments.index') }}">
          <div class="col-md-3"><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" placeholder="From"></div>
          <div class="col-md-3"><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" placeholder="To"></div>
          <div class="col-md-3">
            <select name="location_id" class="form-control">
              <option value="">All Locations</option>
              @foreach($locations as $loc)
                <option value="{{ $loc->id }}" @if(request('location_id')==$loc->id) selected @endif>{{ $loc->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3"><input type="text" name="user_id" value="{{ request('user_id') }}" class="form-control" placeholder="User ID"></div>
          <div class="col-md-2">
            <select name="status" class="form-control">
              <option value="">All</option>
              @foreach(['scheduled','cancelled','completed'] as $st)
              <option value="{{ $st }}" @if(request('status')===$st) selected @endif>{{ ucfirst($st) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-1"><button class="btn btn-secondary w-100">Filter</button></div>
        </form>

        <div class="alert alert-light border d-flex align-items-center justify-content-between mb-3">
          <div>
            <strong>Rotasi Shift Cepat</strong>
            <div class="small text-muted">Generate penugasan berputar untuk beberapa hari ke depan.</div>
          </div>
          <form method="POST" action="{{ route('shift-assignments.rotate') }}" class="d-flex align-items-center gap-2">
            @csrf
            @if(auth()->user()->hasRole('Super Admin'))
            <select name="location_id" class="form-control form-control-sm">
              @foreach($locations as $loc)
                <option value="{{ $loc->id }}" @if(request('location_id')==$loc->id) selected @endif>{{ $loc->name }}</option>
              @endforeach
            </select>
            @else
              <input type="hidden" name="location_id" value="{{ auth()->user()->location_id }}">
              <span class="text-muted small">Lokasi: {{ optional(auth()->user()->location)->name }}</span>
            @endif
            <input type="number" name="days" value="14" min="1" max="60" class="form-control form-control-sm" style="width:80px">
            <button class="btn btn-outline-primary btn-sm" type="submit">Generate</button>
          </form>
        </div>

        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Date</th>
                <th>User</th>
                <th>Location</th>
                <th>Shift</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse($assignments as $a)
              <tr>
                <td>{{ $a->date->format('Y-m-d') }}</td>
                <td>{{ optional($a->user)->name }} (ID: {{ $a->user_id }})</td>
                <td>{{ optional($a->location)->name ?? 'N/A' }}</td>
                <td>
                  <div>{{ optional($a->shift)->name ?? 'Tidak ada shift' }}</div>
                  @php
                    $slots = $a->locationShift ? $a->locationShift->normalizedSlots() : (optional($a->shift) ? $a->shift->normalizedSlots() : []);
                    $slotText = collect($slots)->map(fn($s) => $s['start'] . ' - ' . $s['end'] . (!empty($s['days']) ? ' (' . implode(',', $s['days']) . ')' : ''))->implode('; ');
                  @endphp
                  <div class="small text-muted">{{ $slotText ?: 'Slot belum diatur' }}</div>
                  @if($a->handover_required)
                    <div class="badge bg-secondary mt-1">Handover</div>
                    @if($a->handover_note)
                      <div class="small text-muted">Note: {{ $a->handover_note }}</div>
                    @endif
                  @endif
                </td>
                <td><span class="badge bg-info">{{ ucfirst($a->status) }}</span></td>
                <td>
                  <a href="{{ route('shift-assignments.edit', $a) }}" class="btn btn-sm btn-warning">Edit</a>
                  <form action="{{ route('shift-assignments.destroy', $a) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this assignment?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                  </form>
                </td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center">No assignments</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
          {{ $assignments->appends(request()->query())->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
