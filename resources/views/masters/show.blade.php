@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Master Details</h3>
                <div class="card-tools">
                    <a href="{{ route('masters.index') }}" class="btn btn-sm btn-secondary">Back to List</a>
                    <a href="{{ route('masters.edit', $master) }}" class="btn btn-sm btn-primary">Edit</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> {{ $master->id }}</p>
                        <p><strong>Name:</strong> {{ $master->name }}</p>
                        <p><strong>Email:</strong> {{ $master->email }}</p>
                        <p><strong>Role:</strong> <span class="badge text-bg-primary">{{ ucfirst($master->role) }}</span></p>
                        <p><strong>Created:</strong> {{ $master->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
