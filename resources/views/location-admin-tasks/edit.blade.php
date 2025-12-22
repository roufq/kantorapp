@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
  <div>
    <h3 class="mb-1">Edit Tugas Lokasi</h3>
    <p class="text-muted mb-0">Perbarui detail tugas. Progres dikirim lewat link pada slot.</p>
  </div>
  <a href="{{ route('location-admin-tasks.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Edit Task (Location)</h3></div>
      <div class="card-body">
        <form action="{{ route('location-admin-tasks.update', $task) }}" method="POST">
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
            <label for="due_date" class="form-label">Due Date</label>
            <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}" class="form-control">
            @error('due_date')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <p class="text-muted">Bukti progres dikirim via link pada slot, tanpa upload file.</p>
          <button type="submit" class="btn btn-primary">Update Task</button>
          <a href="{{ route('location-admin-tasks.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
