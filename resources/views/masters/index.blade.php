@extends('layouts.app')
@section('title')
<div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Master</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Master</li>
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
                <h3 class="card-title">Masters</h3>
                <div class="card-tools">
                    <a href="{{ route('masters.create') }}" class="btn btn-sm btn-primary">Add Master</a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Karyawan</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($masters as $master)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $master->name }}</td>
                                <td>{{ $master->email }}</td>
                                <td>{{ $master->karyawan ? $master->karyawan->nama : 'N/A' }}</td>
                                <td><span class="badge text-bg-primary">{{ ucfirst($master->role) }}</span></td>
                                <td>
                                    <a href="{{ route('masters.show', $master) }}" class="btn btn-sm btn-outline-info">View</a>
                                    <a href="{{ route('masters.edit', $master) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('masters.destroy', $master) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
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
