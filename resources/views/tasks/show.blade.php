@extends('layouts.app')

@section('title')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">Task Detail</h3></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">Tasks</a></li>
                <li class="breadcrumb-item active" aria-current="page">Task Detail</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                 <h4 class="card-title mb-0">{{ $task->title }}</h4>
                 <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body">
                <p><strong>Description:</strong></p>
                <p>{{ $task->description }}</p>
                <hr>
                <p class="mb-1"><strong>Status:</strong> <span class="badge text-bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'info' : 'secondary') }}">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span></p>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small">
                        <span>Progress</span>
                        <span>{{ $task->progress ?? 0 }}%</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $task->progress ?? 0 }}%;" aria-valuenow="{{ $task->progress ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <p><strong>Assigned by:</strong><br>{{ $task->assigner->name }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>Assigned to:</strong><br>{{ optional($task->assignee)->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>Created Date:</strong><br>{{ $task->created_at->format('d M Y') }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>Due Date:</strong><br>{{ $task->due_date ? $task->due_date->format('d M Y') : 'No due date' }}</p>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                @php($me = auth()->user())
                @if($me->hasRole('Super Admin') || $task->assigned_to === $me->id || ($me->hasRole('Admin Lokasi') && optional($task->assignee)->location_id === $me->location_id))
                    <a href="{{ route('tasks.progress.create', $task) }}" class="btn btn-primary me-2">Update Progress</a>
                @endif
                @can('update', $task)
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-primary">Edit Task</a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection