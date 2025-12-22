@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h3 class="mb-1">Lokasi</h3>
                    <p class="text-muted mb-0">Kelola data lokasi beserta statusnya.</p>
                </div>
                <a href="{{ route('locations.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah Lokasi
                </a>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Locations Management</h3>
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
                                    <i class="bi bi-search"></i> Search
                                </button>
                                <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-lg"></i> Clear
                                </a>
                            </form>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover align-middle text-nowrap">
                                <thead>
                                    <tr>
                                        <th style="width:50px">No</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Address</th>
                                        <th>Timezone</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($locations as $location)
                                    <tr>
                                        <td>{{ $loop->iteration + ($locations->currentPage()-1)*$locations->perPage() }}</td>
                                        <td>{{ $location->name }}</td>
                                        <td>
                                            <span class="badge badge-light code-badge border">{{ $location->code }}</span>
                                        </td>
                                        <td>{{ $location->address ?: '-' }}</td>
                                        <td>{{ $location->timezone }}</td>
                                        <td>
                                            @if($location->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="table-actions">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-gear"></i> Actions
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('locations.show', $location) }}">
                                                            <i class="bi bi-eye mr-2"></i>View Details
                                                        </a>
                                                    </li>
                                                    @if(auth()->user()->hasRole('Super Admin'))
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('locations.settings', $location) }}">
                                                            <i class="bi bi-sliders mr-2"></i>Settings
                                                        </a>
                                                    </li>
                                                    @endif
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('locations.edit', $location) }}">
                                                            <i class="bi bi-pencil-square mr-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('location-shifts.index') }}">
                                                            <i class="bi bi-clock-history mr-2"></i>Manage Shifts
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this location?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="bi bi-trash mr-2"></i>Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
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
