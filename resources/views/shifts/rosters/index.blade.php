@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">
      <div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
          <h3 class="mb-1">Weekly Roster</h3>
          <p class="text-muted mb-0">List of weekly rosters per location (factory/non-office).</p>
        </div>
        <a href="{{ route('shifts.rosters.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> Create Roster</a>
      </div>
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title">Roster Per Location</h3>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Location</th>
                <th>Shift</th>
                <th>Week</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($rosters as $r)
              <tr>
                <td>{{ $loop->iteration + ($rosters->currentPage()-1)*$rosters->perPage() }}</td>
                <td>{{ $r->location->name ?? '-' }}</td>
                <td>{{ $r->locationShift->shift->name ?? 'Shift' }}</td>
                <td>{{ $r->week_start->toDateString() }} to {{ $r->week_end->toDateString() }}</td>
                <td>{!! $r->locked ? '<span class="badge badge-secondary">Locked</span>' : '<span class="badge badge-success">Active</span>' !!}</td>
                <td class="d-flex gap-2">
                  <a href="{{ route('shifts.rosters.show', $r) }}" class="btn btn-sm btn-outline-primary">Details</a>
                  <a href="{{ route('shifts.rosters.edit', $r) }}" class="btn btn-sm btn-outline-info">Edit / Rolling</a>
                  <form action="{{ route('shifts.rosters.destroy', $r) }}" method="POST" onsubmit="return confirm('Delete this roster?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                  </form>
                </td>
              </tr>
              @empty
              <tr><td colspan="6" class="text-center">No rosters found.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="card-footer">
          {{ $rosters->links() }}
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
