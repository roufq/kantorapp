@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
  <div>
    <h3 class="mb-1">Location Task Details</h3>
    <p class="text-muted mb-0">{{ $task->title }}</p>
  </div>
  <a href="{{ route('location-admin-tasks.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Task Details (Location)</h3>
        <div class="card-tools">
          <a href="{{ route('location-admin-tasks.edit', $task) }}" class="btn btn-sm btn-primary">Edit</a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p><strong>Title:</strong> {{ $task->title }}</p>
            <p><strong>Description:</strong> {{ $task->description ?: 'No description' }}</p>
            <p><strong>Status:</strong> <span class="badge text-bg-secondary">{{ ucfirst($task->status) }}</span></p>
            <div class="mb-2">
              <div class="d-flex justify-content-between small">
                <span>Progress</span>
                <span>{{ $task->progress ?? 0 }}%</span>
              </div>
              <div class="progress" style="height:10px;">
                <div class="progress-bar" role="progressbar" style="width: {{ $task->progress ?? 0 }}%;" aria-valuenow="{{ $task->progress ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
            <p><strong>Due Date:</strong> {{ $task->due_date ? $task->due_date->format('d M Y') : 'No due date' }}</p>
            <p><strong>Assigned By:</strong> {{ optional($task->assigner)->name }}</p>
            <p><strong>Assigned To:</strong> {{ optional($task->assignee)->name }}</p>
            <p class="text-muted mt-2">Employee progress approval process via <a href="{{ route('tasks.progress.approvals') }}">Approval Progress</a> page. Progress evidence is sent via link.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
