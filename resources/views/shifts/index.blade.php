@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 d-flex flex-wrap align-items-center gap-2">
                    <h1 class="m-0">Shifts</h1>
                    <div class="btn-group ms-2">
                        <a href="{{ route('shifts.index', array_merge(request()->query(), ['category' => 'office'])) }}" class="btn btn-sm {{ request('category') === 'office' ? 'btn-primary' : 'btn-outline-primary' }}">Office</a>
                        <a href="{{ route('shifts.index', array_merge(request()->query(), ['category' => 'non_office'])) }}" class="btn btn-sm {{ request('category') === 'non_office' ? 'btn-primary' : 'btn-outline-primary' }}">Non Office</a>
                        <a href="{{ route('shifts.index', array_merge(request()->query(), ['category' => null])) }}" class="btn btn-sm {{ request('category') ? 'btn-outline-secondary' : 'btn-secondary' }}">Semua</a>
                    </div>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Shifts</li>
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
                            <h3 class="card-title">Shifts Management</h3>
                            <div class="card-tools d-flex gap-2">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-plus"></i> Add Shift
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('shifts.create', ['category' => 'office']) }}">Office Shift</a></li>
                                        <li><a class="dropdown-item" href="{{ route('shifts.create', ['category' => 'non_office']) }}">Non Office Shift</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="{{ route('shifts.create') }}">Tanpa preset</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->

                        <!-- Search and Filter Form -->
                        <div class="card-body border-bottom">
                            <form method="GET" action="{{ route('shifts.index') }}" class="form-inline">
                                <div class="form-group mr-3">
                                    <select name="location_id" class="form-control">
                                        <option value="">All Locations</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mr-3">
                                    <input type="text" name="search" class="form-control" placeholder="Search by name or code" value="{{ request('search') }}">
                                </div>
                                <div class="form-group mr-3">
                                    <select name="category" class="form-control">
                                        <option value="">Kategori: Semua</option>
                                        <option value="office" {{ request('category') === 'office' ? 'selected' : '' }}>Office</option>
                                        <option value="non_office" {{ request('category') === 'non_office' ? 'selected' : '' }}>Non Office</option>
                                    </select>
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
                                <a href="{{ route('shifts.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </form>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover align-middle text-nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Locations</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Category</th>
                                        <th>Day</th>
                                        <th>Time Slot</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($shifts as $shift)
                                    <tr>
                                        <td>{{ $shift->id }}</td>
                                        @php $locs = $shift->locations; $filterLoc = request('location_id'); @endphp
                                        <td>
                                            @if($locs->isEmpty())
                                                <span class="text-muted">-</span>
                                            @else
                                                <div class="d-flex flex-column gap-1">
                                                    @foreach($locs as $loc)
                                                        @if(!$filterLoc || (string)$loc->id === (string)$filterLoc)
                                                            <span class="badge text-bg-light border align-self-start">{{ $loc->name }}</span>
                                                        @endif
                                                    @endforeach
                                                    @if($filterLoc && !$locs->pluck('id')->contains((int) $filterLoc))
                                                        <span class="badge text-bg-secondary align-self-start">Tidak di lokasi ini</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $shift->name }}</td>
                                        <td><span class="badge badge-light code-badge border">{{ $shift->code }}</span></td>
                                        <td><span class="badge {{ $shift->category === 'office' ? 'badge-primary' : 'badge-info' }}">{{ ucfirst(str_replace('_',' ', $shift->category)) }}</span></td>
                                        <td>{{ $shift->getDayName() }}</td>
                                        <td>{{ $shift->getFormattedSchedule() }}</td>
                                        <td>
                                            @if($shift->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="table-actions">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-gear"></i> Actions
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('shifts.show', $shift) }}">
                                                            <i class="bi bi-eye me-2"></i>View Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('shifts.edit', $shift) }}">
                                                            <i class="bi bi-pencil-square me-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('shifts.destroy', $shift) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this shift?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="bi bi-trash me-2"></i>Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No shifts found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->

                        @if($shifts->hasPages())
                        <div class="card-footer">
                            {{ $shifts->appends(request()->query())->links() }}
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
