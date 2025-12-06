@extends('layouts.app')
@section('title')
<div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">Task Details</h3></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('master-tasks.index') }}">Tasks</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $masterTask->title }}</li>
            </ol>
        </div>
    </div>
    <!--end::Row-->
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $masterTask->title }}</h3>
                <div class="card-tools">
                    <a href="{{ route('master-tasks.index') }}" class="btn btn-secondary btn-sm">Back</a>
                    <a href="{{ route('master-tasks.edit', $masterTask) }}" class="btn btn-primary btn-sm">Edit</a>
                    <form action="{{ route('master-tasks.destroy', $masterTask) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this task?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Description</h5>
                        <p>{{ $masterTask->description }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Status & Progress</h5>
                        <p class="mb-1"><span class="badge text-bg-secondary">{{ ucfirst($masterTask->status) }}</span></p>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small">
                                <span>Progress</span>
                                <span>{{ $masterTask->progress ?? 0 }}%</span>
                            </div>
                            <div class="progress" style="height:10px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $masterTask->progress ?? 0 }}%;" aria-valuenow="{{ $masterTask->progress ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted">Update progres 0-100% dengan lampiran foto/dokumen.</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Due Date</h5>
                        <p>{{ $masterTask->due_date ? $masterTask->due_date->format('d M Y') : 'No due date' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Assigned By</h5>
                        <p>{{ $masterTask->assigner->name }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Assigned To</h5>
                        <p>{{ $masterTask->assignee ? $masterTask->assignee->name : 'Not assigned' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Created At</h5>
                        <p>{{ $masterTask->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                @if($masterTask->updated_at != $masterTask->created_at)
                <div class="row">
                    <div class="col-md-6">
                        <h5>Last Updated</h5>
                        <p>{{ $masterTask->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                @endif
                @if($masterTask->photo_path)
                <div class="row">
                    <div class="col-md-6">
                        <h5>Photo</h5>
                        <p><a href="{{ route('master-tasks.download.photo', $masterTask) }}" target="_blank" class="btn btn-sm btn-outline-primary">Download Photo</a></p>
                    </div>
                </div>
                @endif
                @if($masterTask->document_path)
                <div class="row">
                    <div class="col-md-6">
                        <h5>Document</h5>
                        <p><a href="{{ route('master-tasks.download.document', $masterTask) }}" target="_blank" class="btn btn-sm btn-outline-primary">Download Document</a></p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-3" id="progress-form">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Update Progress (Super Admin)</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('master-tasks.update', $masterTask) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    @method('PATCH')
                    <div class="col-md-4">
                        <label class="form-label">Progress (%)</label>
                        <input type="number" name="progress" class="form-control" min="0" max="100" value="{{ old('progress', $masterTask->progress) }}" required>
                        <small class="text-muted">Status otomatis menyesuaikan progres.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Foto Bukti</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Dokumen Bukti</label>
                        <input type="file" name="document" class="form-control" accept=".pdf,.doc,.docx,.txt,.xls,.xlsx">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Simpan Progress</button>
                        <small class="text-muted ms-2">Wajib melampirkan minimal satu file.</small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
