@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
  <div>
    <h3 class="mb-1">Admin Lokasi</h3>
    <p class="text-muted mb-0">Kelola akun admin lokasi dan penempatannya.</p>
  </div>
  <a href="{{ route('location-admins.create') }}" class="btn btn-primary btn-sm">Tambah Admin</a>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Admins</h3>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead>
            <tr>
              <th>NO</th>
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
