@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Locations</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Locations</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Locations Management</h3>
                            <div class="card-tools">
                                <a href="{{ route('locations.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Location
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->

                        <!-- Search and Filter Form -->
                        <div class="card-body border-bottom">
                            <form method="GET" action="{{ route('locations.index') }}" class="form-inline">
                                <div class="form-group mr-3">
                                    <input type="text" name="search" class="form-control" placeholder="Search by name or code" value="{{ request('search') }}">
                                </div>
                                <div class="form-group mr-3">
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-secondary mr-2">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </form>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Address</th>
                                        <th>Timezone</th>
                                        <th>Schedule Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($locations as $location)
                                    <tr>
                                        <td>{{ $location->id }}</td>
                                        <td>{{ $location->name }}</td>
                                        <td><span class="badge badge-info">{{ $location->code }}</span></td>
                                        <td>{{ $location->address ?: '-' }}</td>
                                        <td>{{ $location->timezone }}</td>
                                        <td>
                                            @if($location->schedule_type === 'shifts')
                                                <span class="badge badge-primary">Shift-Based</span>
                                            @elseif($location->schedule_type === 'daily')
                                                <span class="badge badge-info">Daily Schedule</span>
                                            @else
                                                <span class="badge badge-secondary">Not Set</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($location->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('locations.show', $location) }}" class="btn btn-info btn-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(auth()->user()->hasRole('Super Admin'))
                                            <a href="{{ route('locations.settings', $location) }}" class="btn btn-primary btn-sm" title="Settings">
                                                <i class="bi bi-gear"></i>
                                            </a>
                                            @endif
                                            <a href="{{ route('locations.edit', $location) }}" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('location-shifts.index') }}" class="btn btn-success btn-sm" title="Manage Shifts">
                                                <i class="fas fa-clock"></i>
                                            </a>
                                            <form action="{{ route('locations.destroy', $location) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this location?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No locations found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->

                        @if($locations->hasPages())
                        <div class="card-footer">
                            {{ $locations->appends(request()->query())->links() }}
                        </div>
                        @endif
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection
