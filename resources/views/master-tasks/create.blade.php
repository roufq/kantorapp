@extends('layouts.app')
@section('title')
<div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Assign Task</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="{{ route('master-tasks.index') }}">Super Admin Tasks</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Assign Task</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
@endsection
@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Assign Master Task</h1>
            <p class="text-muted mb-0">Super Admin dapat menetapkan tugas ke Super Admin lain atau karyawan.</p>
        </div>
        <div>
            <a href="{{ route('master-tasks.index') }}" class="text-decoration-none">Kembali</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Assign Task to Employee or Super Admin</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('master-tasks.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Task Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign to Employee or Super Admin (Optional)</label>
                        <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                            <option value="">Select Employee or Super Admin (Optional)</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}">
                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo (Optional)</label>
                        <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Document (Optional)</label>
                        <input type="file" class="form-control @error('document') is-invalid @enderror" id="document" name="document" accept=".pdf,.doc,.docx,.txt">
                        @error('document')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Assign Task</button>
                    <a href="{{ route('master-tasks.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
