@extends('layouts.appnew')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Location Admin Details</h3>
        <div class="card-tools">
          <a href="{{ route('location-admins.index') }}" class="btn btn-sm btn-secondary">Back</a>
          <a href="{{ route('location-admins.edit', $admin) }}" class="btn btn-sm btn-primary">Edit</a>
        </div>
      </div>
      <div class="card-body">
        <p><strong>ID:</strong> {{ $admin->id }}</p>
        <p><strong>Name:</strong> {{ $admin->name }}</p>
        <p><strong>Email:</strong> {{ $admin->email }}</p>
        <p><strong>Location:</strong> {{ optional($admin->location)->name ?: '-' }}</p>
        <p><strong>Roles:</strong>
          @php($roles = $admin->getRoleNames())
          @if($roles->isNotEmpty())
            {{ $roles->implode(', ') }}
          @else
            -
          @endif
        </p>
        <p><strong>Created:</strong> {{ $admin->created_at->format('d M Y H:i') }}</p>
        <hr>
        <form action="{{ route('users.demote.employee', $admin) }}" method="POST" onsubmit="return confirm('Demote this admin to Employee?')">
          @csrf
          <button type="submit" class="btn btn-warning">Demote to Employee</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
