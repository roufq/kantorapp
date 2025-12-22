@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
  <div>
    <h3 class="mb-1">Tugas Lokasi</h3>
    <p class="text-muted mb-0">Daftar tugas yang dibuat untuk lokasi Anda.</p>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="{{ route('tasks.progress.approvals') }}" class="btn btn-warning btn-sm">Approval Progress</a>
    <a href="{{ route('location-admin-tasks.create') }}" class="btn btn-primary btn-sm">Assign Task</a>
  </div>
</div>

<div class="mb-4">
  <form method="GET" action="{{ route('location-admin-tasks.index') }}" class="d-flex">
    <input type="text" name="search" class="form-control me-2" placeholder="Search by task title or assignee name..." value="{{ request('search') }}">
    <button type="submit" class="btn btn-outline-primary">Search</button>
    @if(request('search'))
      <a href="{{ route('location-admin-tasks.index') }}" class="btn btn-outline-secondary ms-2">Clear</a>
    @endif
  </form>
</div>

<div class="row">
  @forelse($tasks as $task)
    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title"><a href="{{ route('location-admin-tasks.show', $task) }}">{{ $task->title }}</a></h5>
        </div>
        <div class="card-body">
          <p>{{ $task->description }}</p>
          <p class="mb-1"><strong>Status:</strong> {{ ucfirst($task->status) }}</p>
          <div class="mb-2">
            <div class="d-flex justify-content-between small">
              <span>Progress</span>
              <span>{{ $task->progress ?? 0 }}%</span>
            </div>
            <div class="progress" style="height:8px;">
              <div class="progress-bar" role="progressbar" style="width: {{ $task->progress ?? 0 }}%;" aria-valuenow="{{ $task->progress ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>
          <p><strong>Due:</strong> {{ $task->due_date ? $task->due_date->format('d M Y') : 'No due date' }}</p>
          <p><strong>Assigned to:</strong> {{ optional($task->assignee)->name }}</p>
        </div>
        <div class="card-footer">
          <a href="{{ route('location-admin-tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary">Edit</a>
          <form action="{{ route('location-admin-tasks.destroy', $task) }}" method="POST" style="display: inline" onsubmit="return confirm('Are you sure?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
          </form>
        </div>
      </div>
    </div>
  @empty
    <p>No tasks found.</p>
  @endforelse
</div>

@if($tasks->hasPages())
  <div class="d-flex justify-content-center mt-4">
    {{ $tasks->appends(request()->query())->links() }}
  </div>
@endif
@endsection
