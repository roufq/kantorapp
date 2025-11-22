@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Task</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi'))
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text"  class="form-control" id="title" name="title" value="{{ old('title', $task->title) }}" >
                        @error('title')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control"  id="description" name="description" rows="3">{{ old('description', $task->description) }}</textarea>
                        @error('description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign To</label>
                        <input type="text" id="userFilter" class="form-control mb-2" placeholder="Filter user...">
                        <select class="form-select" id="assigned_to" name="assigned_to" required size="8">
                            <option value="{{ auth()->id() }}">— Assign to Myself ({{ auth()->user()->name }}) —</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }} @ {{ $user->email }}</option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
                        @error('due_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo (Optional)</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        @if($task->photo_path)
                            <div class="mt-2">
                                <small class="text-muted">Current photo: <a href="{{ route('tasks.download.photo', $task) }}" target="_blank">Download</a></small>
                            </div>
                        @endif
                        @error('photo')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Document (Optional)</label>
                        <input type="file" class="form-control" id="document" name="document" accept=".pdf,.doc,.docx,.txt">
                        @if($task->document_path)
                            <div class="mt-2">
                                <small class="text-muted">Current document: <a href="{{ route('tasks.download.document', $task) }}" target="_blank">Download</a></small>
                            </div>
                        @endif
                        @error('document')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Update Task</button>
                    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
  const filterInput2 = document.getElementById('userFilter');
  const selectEl2 = document.getElementById('assigned_to');
  if (filterInput2 && selectEl2) {
    filterInput2.addEventListener('input', function() {
      const term = this.value.toLowerCase();
      for (const opt of selectEl2.options) {
        if (!opt.value) continue;
        const txt = opt.textContent.toLowerCase();
        opt.hidden = term && !txt.includes(term);
      }
    });
  }
</script>
@endsection
