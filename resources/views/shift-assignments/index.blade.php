@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Shift Assignments</h3>
        <div class="d-flex gap-2">
          <a href="{{ route('shift-assignments.export', request()->query()) }}" class="btn btn-outline-success btn-sm">Export</a>
          <a href="{{ route('shift-assignments.create') }}" class="btn btn-primary btn-sm">Create</a>
        </div>
      </div>
      <div class="card-body">
        <form class="row g-2 mb-3" method="GET" action="{{ route('shift-assignments.index') }}">
          <div class="col-md-3"><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" placeholder="From"></div>
          <div class="col-md-3"><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" placeholder="To"></div>
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

        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Date</th>
                <th>User</th>
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
                <td>{{ optional($a->shift)->name }}</td>
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

        {{ $assignments->appends(request()->query())->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
