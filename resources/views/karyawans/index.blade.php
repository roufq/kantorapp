@extends('layouts.app')
@section('title')
<div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Employee</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Employee</li>
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
                <h3 class="card-title">Employees</h3>
                <div class="card-tools">
                    <a href="{{ route('karyawans.create') }}" class="btn btn-sm btn-primary">Add Employee</a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Jabatan</th>
                            <th>Divisi</th>
                            <th>Lokasi</th>

                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                       
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $employee->nama }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->jabatan }}</td>
                                <td>{{ $employee->division->nama ?? 'N/A' }}</td>
                                <td>{{ $employee->location->name ?? 'N/A' }}</td>

                                <td>
                                    <a href="{{ route('karyawans.show', $employee) }}" class="btn btn-sm btn-outline-info">View</a>
                                    <a href="{{ route('karyawans.edit', $employee) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('karyawans.destroy', $employee) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
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
