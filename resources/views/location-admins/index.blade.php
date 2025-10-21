@extends('layouts.app')
@section('title')
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-6"><h3 class="mb-0">Location Admins</h3></div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-end">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Location Admins</li>
      </ol>
    </div>
  </div>
</div>
@endsection
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Admins</h3>
        <div class="card-tools">
          <a href="{{ route('location-admins.create') }}" class="btn btn-sm btn-primary">Add Admin</a>
        </div>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Location</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($admins as $admin)
              <tr>
                <td>{{ $loop->iteration + ($admins->currentPage()-1)*$admins->perPage() }}</td>
                <td>{{ $admin->name }}</td>
                <td>{{ $admin->email }}</td>
                <td>{{ optional($admin->location)->name ?: '-' }}</td>
                <td>
                  <a href="{{ route('location-admins.show', $admin) }}" class="btn btn-sm btn-outline-info">View</a>
                  <a href="{{ route('location-admins.edit', $admin) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                  <form action="{{ route('users.demote.employee', $admin) }}" method="POST" style="display:inline" onsubmit="return confirm('Demote this admin to Employee?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-warning">Demote to Employee</button>
                  </form>
                  <form action="{{ route('location-admins.destroy', $admin) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center">No admins found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-footer">
        {{ $admins->links() }}
      </div>
    </div>
  </div>
  </div>
@endsection
