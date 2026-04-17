@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">
      <div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
          <h3 class="mb-1">Create Weekly Roster</h3>
          <p class="text-muted mb-0">Select location, location shift, employees, and off pattern.</p>
        </div>
        <a href="{{ route('shifts.rosters.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
      </div>
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        </div>
      @endif
      <div class="card">
        <div class="card-body">
          {{-- Small GET form to change location without triggering POST validation --}}
          <form method="GET" action="{{ route('shifts.rosters.create') }}" class="mb-3">
            <div class="row g-3 align-items-end">
              <div class="col-md-4">
                <label class="form-label">Location</label>
                <select name="location_id" class="form-control" onchange="this.form.submit()">
                  @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" @selected($locationId==$loc->id)>{{ $loc->name }} ({{ $loc->code }})</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Week (start)</label>
                <input type="date" name="week_start" class="form-control" value="{{ $weekStart->toDateString() }}">
              </div>
              <div class="col-md-4">
                <button class="btn btn-outline-primary" type="submit">Load Location</button>
              </div>
            </div>
          </form>

          @php
            $defaultLocationShiftId = old('location_shift_id') ?? ($locationShifts->first()->id ?? null);
            $oldUsers = collect(old('user_ids', []))->map(fn($v)=> (int)$v)->toArray();
            if (empty($oldUsers) && $users->count()) {
                $oldUsers = $users->pluck('id')->toArray(); // auto-select all employees if none selected
            }
          @endphp
          <form method="POST" action="{{ route('shifts.rosters.store') }}">
            @csrf
            <input type="hidden" name="location_id" value="{{ $locationId }}">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Week</label>
                <div class="d-flex gap-2">
                  <input type="date" name="week_start" class="form-control" value="{{ $weekStart->toDateString() }}">
                  <input type="date" name="week_end" class="form-control" value="{{ $weekEnd->toDateString() }}">
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label">Weekly Off</label>
                <div class="d-flex gap-2">
                  <input type="number" min="1" name="weekly_off_every" class="form-control" value="{{ old('weekly_off_every', 6) }}">
                  <input type="text" name="weekly_off_label" class="form-control" value="{{ old('weekly_off_label','OFF') }}">
                </div>
                <small class="text-muted">Example: 6 means 1 day off every 6 working days.</small>
              </div>
            </div>

            <hr>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Shift (Location Shift)</label>
                <select name="location_shift_id" class="form-control" size="8" required>
                  @forelse($locationShifts as $ls)
                    <option value="{{ $ls->id }}" @selected($defaultLocationShiftId==$ls->id)>{{ $ls->shift->name ?? 'Shift' }} ({{ $ls->id }}) - {{ $ls->shift->category ?? '-' }}</option>
                  @empty
                    <option disabled>No shifts in this location</option>
                  @endforelse
                </select>
                <small class="text-muted d-block mt-1">Only Non Office (Factory) category shifts are shown.</small>
              </div>
              <div class="col-md-6">
                <label class="form-label">Employees (in this location)</label>
                <select name="user_ids[]" class="form-control" multiple size="8" required>
                  @forelse($users as $u)
                    <option value="{{ $u->id }}" @selected(in_array($u->id, $oldUsers))>{{ $u->name }} ({{ $u->email }})</option>
                  @empty
                    <option disabled>No employees in this location</option>
                  @endforelse
                </select>
                <small class="text-muted d-block mt-1">If unselected, all employees in this location are automatically selected.</small>
              </div>
            </div>

            <div class="mt-3 d-flex justify-content-between">
              <a href="{{ route('shifts.rosters.index') }}" class="btn btn-outline-secondary">Back</a>
              <button class="btn btn-primary" type="submit"><i class="fas fa-magic me-1"></i> Generate Roster</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
