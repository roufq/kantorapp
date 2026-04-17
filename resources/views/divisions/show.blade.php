@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Detail Divisi</h3>
        <p class="text-muted mb-0">{{ $division->nama }}</p>
    </div>
    <a href="{{ route('divisions.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Division Details</h3>
                <div class="card-tools">
                    <a href="{{ route('divisions.edit', $division) }}" class="btn btn-sm btn-primary">Edit</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> {{ $division->id }}</p>
                        <p><strong>Nama:</strong> {{ $division->nama }}</p>
                        <p><strong>Created:</strong> {{ $division->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
