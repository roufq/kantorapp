@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Pengguna</h3>
        <p class="text-muted mb-0">Kelola akun pengguna, peran, dan keterkaitan karyawan.</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">Tambah User</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Employees</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th style="width:50px">No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Employee</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            <tr>
                                <td>{{ $loop->iteration + ($employees->currentPage()-1)*$employees->perPage() }}</td>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>
                                  @php($roles = $employee->getRoleNames())
                                  @if($roles->isNotEmpty())
                                    <span class="badge text-bg-secondary">{{ $roles->implode(', ') }}</span>
                                  @else
                                    <span class="badge text-bg-light">-</span>
                                  @endif
                                </td>
                                <td>{{ $employee->employee ? $employee->employee->nama : '-' }}</td>
                                <td>{{ $employee->location ? $employee->location->name : '-' }}</td>
                                <td>
                                    <a href="{{ route('users.show', $employee) }}" class="btn btn-sm btn-outline-info">View</a>
                                    <a href="{{ route('users.edit', $employee) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('users.destroy', $employee) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
