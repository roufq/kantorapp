@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">
      <div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
          <h3 class="mb-1">Roster Details</h3>
          <p class="text-muted mb-0">{{ $roster->location->name ?? '-' }} | {{ $roster->locationShift->shift->name ?? 'Shift' }}</p>
        </div>
        <a href="{{ route('shifts.rosters.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
      </div>
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Week {{ $roster->week_start->toDateString() }} to {{ $roster->week_end->toDateString() }}</h3>
          <div class="card-tools">
            <a class="btn btn-sm btn-success" href="{{ route('shifts.rosters.export', $roster) }}">
              <i class="bi bi-download me-1"></i> Export Excel
            </a>
          </div>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-bordered align-middle">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Date</th>
                <th>Slot</th>
                <th>Hours</th>
                <th>Employees</th>
                <th>Status</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
              @forelse($roster->entries->sortBy(['date','slot_index']) as $e)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $e->date->toDateString() }}</td>
                  <td>
                    @if($e->status === 'off')
                      <span class="text-muted">OFF</span>
                    @else
                      {{ $e->slot_index + 1 }}
                    @endif
                  </td>
                  <td>
                    @if($e->status === 'off')
                      <span class="text-muted">Day off</span>
                    @elseif(isset($slotMap[$e->slot_index]))
                      {{ $slotMap[$e->slot_index]['start'] ?? '?' }} - {{ $slotMap[$e->slot_index]['end'] ?? '?' }}
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>{{ $e->user->name ?? '-' }}</td>
                  <td>
                    @if($e->status === 'off')
                      <span class="badge badge-secondary">OFF</span>
                    @else
                      <span class="badge badge-success">Scheduled</span>
                    @endif
                  </td>
                  <td>{{ $e->notes }}</td>
                </tr>
              @empty
                <tr><td colspan="7" class="text-center text-muted">No entries for this roster.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="card-footer d-flex justify-content-between">
          <a href="{{ route('shifts.rosters.index') }}" class="btn btn-outline-secondary">Back</a>
          <a href="{{ route('shifts.rosters.edit', $roster) }}" class="btn btn-info">Edit / Rolling</a>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
