@extends('layouts.appnew')
@section('title')
<div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">My Assigned Super Admin Tasks</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">My Assigned Super Admin Tasks</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>My Assigned Super Admin Tasks</h1>
    <div>
        <a href="{{ route('master-tasks.create.self') }}" class="btn btn-success">Create Super Admin Task</a>
    </div>
</div>

<!-- Search Form -->
<div class="mb-4">
    <form method="GET" action="{{ route('master-tasks.index') }}" class="d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Search by task title..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-outline-primary">Search</button>
        @if(request('search'))
            <a href="{{ route('master-tasks.index') }}" class="btn btn-outline-secondary ms-2">Clear</a>
        @endif
    </form>
</div>

@if($tasks->count() > 0)
    <div class="row">
        @foreach($tasks as $masterTask)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"><a href="{{ route('master-tasks.show', $masterTask) }}">{{ $masterTask->title }}</a></h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $masterTask->description }}</p>
                        <p class="mb-1"><strong>Status:</strong> {{ ucfirst($masterTask->status) }}</p>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small">
                                <span>Progress</span>
                                <span>{{ $masterTask->progress ?? 0 }}%</span>
                            </div>
                            <div class="progress" style="height:8px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $masterTask->progress ?? 0 }}%;" aria-valuenow="{{ $masterTask->progress ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <p><strong>Due:</strong> {{ $masterTask->due_date ? $masterTask->due_date->format('d M Y') : 'No due date' }}</p>
                        <p><strong>Assigned by:</strong> {{ $masterTask->assigner->name }}</p>
                        <p><strong>Assigned to:</strong> {{ $masterTask->assignee ? $masterTask->assignee->name : 'Not assigned' }}</p>
                    </div>
                    <div class="card-footer">
                        <div class="mt-2">
                            <a href="{{ route('master-tasks.show', $masterTask) }}#progress-form" class="btn btn-sm btn-primary">Update Progress</a>
                            <a href="{{ route('master-tasks.edit', $masterTask) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('master-tasks.destroy', $masterTask) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
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
@else
    <div class="alert alert-info">
        No master tasks assigned to you.
    </div>
@endif
@endsection
