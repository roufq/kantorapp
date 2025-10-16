@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Division Details</h3>
                <div class="card-tools">
                    <a href="{{ route('divisions.index') }}" class="btn btn-sm btn-secondary">Back to List</a>
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
