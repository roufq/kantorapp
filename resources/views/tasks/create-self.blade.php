@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Buat Tugas untuk Diri Sendiri</h1>
            <p class="text-muted mb-0">Catat tugas pribadi dan pantau progresnya.</p>
        </div>
        <div>
            <a href="{{ route('tasks.index') }}" class="text-decoration-none">Kembali</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Task for Yourself</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.store.self') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" id="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" id="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" id="due_date">
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo (Optional)</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Document (Optional)</label>
                        <input type="file" class="form-control" id="document" name="document" accept=".pdf,.doc,.docx,.txt">
                    </div>
                    <button type="submit" class="btn btn-primary">Create Task</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
