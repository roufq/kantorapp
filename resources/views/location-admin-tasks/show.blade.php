@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Task Details (Location)</h3>
        <div class="card-tools">
          <a href="{{ route('location-admin-tasks.index') }}" class="btn btn-sm btn-secondary">Back</a>
          <a href="{{ route('location-admin-tasks.edit', $task) }}" class="btn btn-sm btn-primary">Edit</a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p><strong>Title:</strong> {{ $task->title }}</p>
            <p><strong>Description:</strong> {{ $task->description ?: 'No description' }}</p>
            <p><strong>Status:</strong> <span class="badge text-bg-secondary">{{ ucfirst($task->status) }}</span></p>
            <p><strong>Due Date:</strong> {{ $task->due_date ? $task->due_date->format('d M Y') : 'No due date' }}</p>
            <p><strong>Assigned By:</strong> {{ optional($task->assigner)->name }}</p>
            <p><strong>Assigned To:</strong> {{ optional($task->assignee)->name }}</p>
            @if($task->photo_path)
              <p><strong>Photo:</strong> <a href="{{ route('location-admin-tasks.download.photo', $task) }}" target="_blank" class="btn btn-sm btn-outline-primary">Download Photo</a></p>
            @endif
            @if($task->document_path)
              <p><strong>Document:</strong> <a href="{{ route('location-admin-tasks.download.document', $task) }}" target="_blank" class="btn btn-sm btn-outline-primary">Download Document</a></p>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

