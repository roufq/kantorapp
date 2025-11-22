@extends('layouts.app')
@section('title')
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-6"><h3 class="mb-0">Location Tasks</h3></div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-end">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Location Tasks</li>
      </ol>
    </div>
  </div>
</div>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1>Tasks in My Location</h1>
  <div>
    <a href="{{ route('location-admin-tasks.create') }}" class="btn btn-primary">Assign Task</a>
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
          <p><strong>Status:</strong> {{ ucfirst($task->status) }}</p>
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

