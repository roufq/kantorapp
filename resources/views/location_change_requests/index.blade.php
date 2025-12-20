@extends('layouts.appnew')

@section('title')
<div class="row">
  <div class="col-sm-12">
    <div class="page-title-box">
      <div class="btn-group float-right">
        <ol class="breadcrumb hide-phone p-0 m-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Location Change Requests</li>
        </ol>
      </div>
      <h4 class="page-title">Location Change Requests</h4>
    </div>
  </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
                    <div>
                        <h4 class="header-title mb-0">Requests Overview</h4>
                        <small class="text-muted">Filter and manage employee location changes</small>
                    </div>
                    @if(auth()->user()->hasRole('Karyawan') || auth()->user()->hasRole('Admin Lokasi') || auth()->user()->hasRole('Super Admin'))
                    <a href="{{ route('location-change-requests.create') }}" class="btn btn-primary waves-effect waves-light">
                        <i class="mdi mdi-plus-circle-outline mr-1"></i> Create New Request
                    </a>
                    @endif
                </div>

                <form method="GET" action="{{ route('location-change-requests.index') }}" class="row align-items-end mb-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="mb-1 text-muted">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All</option>
                            <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="mb-1 text-muted">Request Date</label>
                        <input type="date" name="request_date" class="form-control" value="{{ request('request_date') }}">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="mb-1 text-muted">Original Location</label>
                        <select name="original_location_id" class="form-control">
                            <option value="">All</option>
                            @isset($locations)
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ (string)request('original_location_id')===(string)$loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="mb-1 text-muted">Target Location</label>
                        <select name="target_location_id" class="form-control">
                            <option value="">All</option>
                            @isset($locations)
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ (string)request('target_location_id')===(string)$loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <div class="col-12 d-flex gap-2 flex-wrap mt-2">
                        <button type="submit" class="btn btn-success waves-effect waves-light">
                            <i class="mdi mdi-filter-outline mr-1"></i> Apply
                        </button>
                        <a href="{{ route('location-change-requests.index') }}" class="btn btn-outline-secondary waves-effect">
                            <i class="mdi mdi-close-circle-outline mr-1"></i> Clear
                        </a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover m-0">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-uppercase small" style="width:50px">No</th>
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
                                <td>{{ $loop->iteration + ($requests->currentPage()-1)*$requests->perPage() }}</td>
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
                                    <span class="badge badge-{{ $status==='approved' ? 'success' : ($status==='rejected' ? 'danger' : 'warning') }}">{{ ucfirst($status) }}</span>
                                </td>
                                <td>
                                    @if($request->is_permanent)
                                        <span class="badge badge-primary">Permanent</span>
                                    @else
                                        <span class="badge badge-info">Temporary</span>
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
                <div class="mt-3 d-flex justify-content-center">
                    {{ $requests->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
