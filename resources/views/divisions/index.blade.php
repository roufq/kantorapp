@extends('layouts.app')
@section('title')
<div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Division</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Division</li>
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
                <h3 class="card-title">Divisions</h3>
                <div class="card-tools">
                    <a href="{{ route('divisions.create') }}" class="btn btn-sm btn-primary">Add Division</a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($divisions as $division)
                            <tr>
                                <td>{{ $division->id }}</td>
                                <td>{{ $division->nama }}</td>
                                <td>
                                    <a href="{{ route('divisions.show', $division) }}" class="btn btn-sm btn-outline-info">View</a>
                                    <a href="{{ route('divisions.edit', $division) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('divisions.destroy', $division) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
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
