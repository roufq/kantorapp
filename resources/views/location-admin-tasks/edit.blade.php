@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">Edit Tugas Lokasi</h1>
      <p class="text-muted mb-0">Perbarui detail atau status tugas di lokasi Anda.</p>
    </div>
    <div>
      <a href="{{ route('location-admin-tasks.index') }}" class="text-decoration-none">Kembali</a>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Edit Task (Location)</h3></div>
      <div class="card-body">
        <form action="{{ route('location-admin-tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PATCH')
          <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title', $task->title) }}" class="form-control" required>
            @error('title')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $task->description) }}</textarea>
            @error('description')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="assigned_to" class="form-label">Assign To (Employees in my location)</label>
            <select name="assigned_to" id="assigned_to" class="form-select" required>
              @foreach($users as $u)
                <option value="{{ $u->id }}" {{ old('assigned_to', $task->assigned_to) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
              @endforeach
            </select>
            @error('assigned_to')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required>
              <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
              <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            @error('status')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="due_date" class="form-label">Due Date</label>
            <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}" class="form-control">
            @error('due_date')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="photo" class="form-label">Photo (Optional)</label>
            <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
            @if($task->photo_path)
              <div class="mt-2"><small>Current photo: <a href="{{ route('location-admin-tasks.download.photo', $task) }}" target="_blank">Download</a></small></div>
            @endif
            @error('photo')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="document" class="form-label">Document (Optional)</label>
            <input type="file" name="document" id="document" class="form-control" accept=".pdf,.doc,.docx,.txt">
            @if($task->document_path)
              <div class="mt-2"><small>Current document: <a href="{{ route('location-admin-tasks.download.document', $task) }}" target="_blank">Download</a></small></div>
            @endif
            @error('document')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <button type="submit" class="btn btn-primary">Update Task</button>
          <a href="{{ route('location-admin-tasks.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
