@extends('layouts.app')

@section('title')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6">
            <h3 class="mb-0">Update Progress: {{ $task->title }}</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">Tasks</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tasks.show', $task) }}">{{ $task->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Update Progress</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Update Progress for "{{ $task->title }}"</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.progress.store', $task) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="progress" class="form-label">Progress Percentage</label>
                        <input type="number" class="form-control @error('progress') is-invalid @enderror" id="progress" name="progress" value="{{ old('progress', $task->progress) }}" required min="0" max="100">
                        @error('progress')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="4">{{ old('note') }}</textarea>
                        @error('note')
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
                        <input type="file" class="form-control @error('document') is-invalid @enderror" id="document" name="document" accept=".pdf,.doc,.docx,.txt,.xls,.xlsx">
                        @error('document')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-info">
                        You must provide a note, or upload a photo/document to submit progress.
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('tasks.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit Progress</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Riwayat Progres & Approval</h5>
            </div>
            <div class="card-body">
                @forelse($progressUpdates as $update)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold">Progress {{ $update->progress }}%</div>
                                <div class="small text-muted">Dikirim oleh {{ $update->user->name }} • {{ $update->created_at->format('d M Y H:i') }}</div>
                                <div class="mt-1">
                                    <span class="badge text-bg-{{ $update->approval_status === 'approved' ? 'success' : ($update->approval_status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($update->approval_status) }}
                                    </span>
                                    @if($update->approval_level !== 'none')
                                        <span class="badge text-bg-light text-muted">Target: {{ ucfirst(str_replace('_', ' ', $update->approval_level)) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($update->note)
                            <div class="mt-2 small"><strong>Catatan:</strong> {{ $update->note }}</div>
                        @endif
                        <div class="mt-2 d-flex flex-wrap gap-2">
                            @if($update->photo_path)
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('tasks.progress.download', [$update, 'photo']) }}" target="_blank">Download Photo</a>
                            @endif
                            @if($update->document_path)
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('tasks.progress.download', [$update, 'document']) }}" target="_blank">Download Document</a>
                            @endif
                        </div>
                        @if($update->approval_status === 'rejected' && $update->rejection_reason)
                            <div class="mt-2 text-danger small"><strong>Alasan penolakan:</strong> {{ $update->rejection_reason }}</div>
                        @endif
                        @if($update->approver)
                            <div class="mt-1 small text-muted">Diproses oleh {{ $update->approver->name }} @ {{ optional($update->approved_at)->format('d M Y H:i') }}</div>
                        @endif
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada riwayat progres.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
