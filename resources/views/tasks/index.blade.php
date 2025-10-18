@extends('layouts.app')
@section('title')
<div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Tasks</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Tasks</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>My Tasks</h1>
    <div>        
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">Create Task Employee</a>
    </div>
</div>

<!-- Search Form -->
<div class="mb-4">
    <form method="GET" action="{{ route('tasks.index') }}" class="d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Search by task title or assignee name..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-outline-primary">Search</button>
        @if(request('search'))
            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary ms-2">Clear</a>
        @endif
    </form>
</div>
<div class="row">
    @foreach($tasks as $task)
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><a href="{{ route('tasks.show', $task) }}">{{ $task->title }}</a></h5>
                </div>
                <div class="card-body">
                    <p>{{ $task->description }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($task->status) }}</p>
                    <p><strong>Due:</strong> {{ $task->due_date ? $task->due_date->format('d M Y') : 'No due date' }}</p>
                    <p><strong>Assigned by:</strong> {{ $task->assigner->name }}</p>
                </div>
                <div class="card-footer">
                    <form action="{{ route('tasks.update', $task) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </form>
                    @if(auth()->user()->role === 'master' || $task->assigned_to === auth()->id())
                        <div class="mt-2">
                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Pagination -->
@if($tasks->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $tasks->appends(request()->query())->links() }}
    </div>
@endif
@endsection
