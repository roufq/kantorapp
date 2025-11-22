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
    <h1>Tasks</h1>
    <div>
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi'))
            <a href="{{ route('tasks.create') }}" class="btn btn-primary me-2">Assign Task</a>
        @endif
        <a href="{{ route('tasks.create.self') }}" class="btn btn-outline-primary">Create Task for Myself</a>
    </div>
</div>

<!-- Search Form -->
<div class="mb-4">
    <form method="GET" action="{{ route('tasks.index') }}" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Title or assignee name" value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ request('status')=='in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status')=='completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        @if(auth()->user()->hasRole('Super Admin'))
        <div class="col-md-2">
            <label class="form-label">Role</label>
            <select name="assignee_role" class="form-select">
                <option value="">All</option>
                <option value="Karyawan" {{ request('assignee_role')=='Karyawan' ? 'selected' : '' }}>Karyawan</option>
                <option value="Admin Lokasi" {{ request('assignee_role')=='Admin Lokasi' ? 'selected' : '' }}>Admin Lokasi</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Location</label>
            <select name="location_id" class="form-select">
                <option value="">All</option>
                @foreach(\App\Models\Location::orderBy('name')->get() as $loc)
                    <option value="{{ $loc->id }}" {{ (string)request('location_id')===(string)$loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="col-md-12 d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-outline-primary">Apply</button>
            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Clear</a>
        </div>
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
                    <p><strong>Assigned to:</strong> {{ optional($task->assignee)->name }}</p>
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
                    @php($me = auth()->user())
                    @if($me->hasRole('Super Admin') || $task->assigned_to === $me->id || ($me->hasRole('Admin Lokasi') && optional($task->assignee)->location_id === $me->location_id))
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
