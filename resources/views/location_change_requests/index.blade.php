@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Location Change Requests</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Location Change Requests</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div>
                                <h3 class="card-title mb-0">Requests Overview</h3>
                                <p class="text-muted small mb-0">Filter and manage employee location changes</p>
                            </div>
                            @if(auth()->user()->hasRole('Karyawan') || auth()->user()->hasRole('Admin Lokasi') || auth()->user()->hasRole('Super Admin'))
                            <a href="{{ route('location-change-requests.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Create New Request
                            </a>
                            @endif
                        </div>

                        <!-- Filters -->
                        <div class="card-body border-bottom">
                            <form method="GET" action="{{ route('location-change-requests.index') }}" class="row g-3 align-items-end">
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label mb-1">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All</option>
                                        <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label mb-1">Request Date</label>
                                    <input type="date" name="request_date" class="form-control" value="{{ request('request_date') }}">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label mb-1">Original Location</label>
                                    <select name="original_location_id" class="form-select">
                                        <option value="">All</option>
                                        @isset($locations)
                                            @foreach($locations as $loc)
                                                <option value="{{ $loc->id }}" {{ (string)request('original_location_id')===(string)$loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label mb-1">Target Location</label>
                                    <select name="target_location_id" class="form-select">
                                        <option value="">All</option>
                                        @isset($locations)
                                            @foreach($locations as $loc)
                                                <option value="{{ $loc->id }}" {{ (string)request('target_location_id')===(string)$loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                                <div class="col-12 d-flex gap-2 flex-wrap">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-funnel me-1"></i> Apply
                                    </button>
                                    <a href="{{ route('location-change-requests.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i> Clear
                                    </a>
                                </div>
                            </form>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover align-middle text-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-uppercase small">Date</th>
                                        @if(auth()->user()->hasRole('Admin Lokasi') || auth()->user()->hasRole('Super Admin'))
                                            <th class="text-uppercase small">Employee</th>
                                        @endif
                                        <th class="text-uppercase small">Original Location</th>
                                        <th class="text-uppercase small">Target Location</th>
                                        <th class="text-uppercase small">Reason</th>
                                        <th class="text-uppercase small">Status</th>
                                        <th class="text-uppercase small">Type</th>
                                        <th class="text-uppercase small text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($requests as $request)
                                    <tr>
                                        <td>{{ \Illuminate\Support\Carbon::parse($request->request_date)->format('Y-m-d') }}</td>
                                        @if(auth()->user()->hasRole('Admin Lokasi') || auth()->user()->hasRole('Super Admin'))
                                            <td>{{ optional($request->user)->name ?? '-' }}</td>
                                        @endif
                                        <td>{{ optional($request->originalLocation)->name ?? '-' }}</td>
                                        <td>{{ optional($request->targetLocation)->name ?? '-' }}</td>
                                        <td class="text-truncate" style="max-width: 220px;" title="{{ $request->reason }}">
                                            {{ $request->reason ?: '-' }}
                                        </td>
                                        <td>
                                            @php($status = $request->status)
                                            <span class="badge text-bg-{{ $status==='approved' ? 'success' : ($status==='rejected' ? 'danger' : 'warning') }}">{{ ucfirst($status) }}</span>
                                        </td>
                                        <td>
                                            @if($request->is_permanent)
                                                <span class="badge text-bg-primary">Permanent</span>
                                            @else
                                                <span class="badge text-bg-info">Temporary</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if((auth()->user()->hasRole('Admin Lokasi') || auth()->user()->hasRole('Super Admin')) && $request->status === 'pending')
                                                <div class="d-inline-flex gap-1 flex-wrap justify-content-center">
                                                    <form action="{{ route('location-change-requests.updateStatus', $request) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                                    </form>
                                                    <form action="{{ route('location-change-requests.updateStatus', $request) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-muted">&mdash;</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->hasRole('Admin Lokasi') || auth()->user()->hasRole('Super Admin') ? 8 : 7 }}" class="text-center py-4 text-muted">
                                            No location change requests found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(method_exists($requests, 'links') && $requests->hasPages())
                        <div class="card-footer d-flex justify-content-center">
                            {{ $requests->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
