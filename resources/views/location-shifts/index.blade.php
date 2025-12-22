@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h3 class="mb-1">Location Shifts</h3>
                    <p class="text-muted mb-0">Kelola shift yang ditetapkan ke setiap lokasi.</p>
                </div>
                <a href="{{ route('location-shifts.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Assign Shifts to Location
                </a>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Location Shifts</h3>
                        </div>
                        <!-- /.card-header -->

                        <!-- Filter Form -->
                        <div class="card-body border-bottom">
                            <form method="GET" action="{{ route('location-shifts.index') }}" class="form-inline">
                                <div class="form-group mr-3">
                                    <label for="location_id" class="mr-2">Filter by Location:</label>
                                    <select name="location_id" id="location_id" class="form-control form-control-sm">
                                        <option value="">All Locations</option>
                                        @foreach($allLocations as $location)
                                            <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }} ({{ $location->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mr-3">
                                    <label for="search" class="mr-2">Search:</label>
                                    <input type="text" name="search" id="search" class="form-control form-control-sm"
                                           value="{{ request('search') }}" placeholder="Location name or code">
                                </div>
                                <button type="submit" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('location-shifts.index') }}" class="btn btn-outline-secondary btn-sm ml-2">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </form>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th style="width:50px">No</th>
                                        <th>Location</th>
                                        <th>Code</th>
                                        <th>Assigned Shifts</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($locations as $location)
                                        <tr>
                                            <td>{{ $loop->iteration + ($locations->currentPage()-1)*$locations->perPage() }}</td>
                                            <td>{{ $location->name }}</td>
                                            <td>{{ $location->code }}</td>
                                            <td>
                                                @if($location->shifts->count() > 0)
                                                    <div class="d-flex flex-wrap">
                                                        @foreach($location->shifts as $shift)
                                                            <span class="badge badge-info mr-1 mb-1">
                                                                {{ $shift->name }}
                                                                <a href="{{ route('location-shifts.detach-shift', [$location, $shift]) }}"
                                                                   class="text-white ml-1"
                                                                   onclick="return confirm('Remove this shift from location?')">
                                                                    <i class="fas fa-times"></i>
                                                                </a>
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">No shifts assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('location-shifts.edit', $location) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i> Manage Shifts
                                                </a>
                                                <a href="{{ route('location-shifts.show', $location) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No locations found.</td>
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
