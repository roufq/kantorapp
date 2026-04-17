@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Edit Master Task</h3>
        <p class="text-muted mb-0">Update details and progress (0-100%) with photo/document attachments.</p>
    </div>
    <a href="{{ route('master-tasks.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Edit Super Admin Task</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('master-tasks.update', $masterTask) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label for="title" class="form-label">Task Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $masterTask->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $masterTask->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign to Employee or Super Admin (Optional)</label>
                        <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                            <option value="">Select Employee or Super Admin (Optional)</option>
                            @foreach($users as $user)
                                @php($roles = $user->getRoleNames())
                                <option value="{{ $user->id }}" {{ old('assigned_to', $masterTask->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }} @if($roles->isNotEmpty()) ({{ $roles->implode(', ') }}) @endif</option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="progress" class="form-label">Progress (%)</label>
                        <input type="number" class="form-control @error('progress') is-invalid @enderror" id="progress" name="progress" value="{{ old('progress', $masterTask->progress) }}" min="0" max="100" required>
                        <small class="text-muted">Status will follow progress (0 = pending, 100 = completed).</small>
                        @error('progress')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date', $masterTask->due_date ? $masterTask->due_date->format('Y-m-d') : '') }}">
                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo (Must select one: photo or document)</label>
                        <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                        @if($masterTask->photo_path)
                            <div class="mt-2">
                                <small class="text-muted">Current photo: <a href="{{ route('master-tasks.download.photo', $masterTask) }}" target="_blank">Download</a></small>
                            </div>
                        @endif
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Document (optional if photo already uploaded)</label>
                        <input type="file" class="form-control @error('document') is-invalid @enderror" id="document" name="document" accept=".pdf,.doc,.docx,.txt,.xls,.xlsx">
                        @if($masterTask->document_path)
                            <div class="mt-2">
                                <small class="text-muted">Current document: <a href="{{ route('master-tasks.download.document', $masterTask) }}" target="_blank">Download</a></small>
                            </div>
                        @endif
                        @error('document')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Update Task</button>
                    <a href="{{ route('master-tasks.show', $masterTask) }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
