@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ __('Job Descriptions') }}
          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;">{{ __('Define roles, responsibilities, and task catalogs for team members.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <a href="{{ route('jobdesks.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
            <i class="mdi mdi-plus-circle-outline me-2"></i>{{ __('Add New Jobdesk') }}
          </a>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="card mb-5 border-0 shadow-sm rounded-4">
          <div class="card-body p-4">
              <form method="GET" action="{{ route('jobdesks.index') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                  <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Location Filter') }}</label>
                  <select name="location_id" class="form-select rounded-pill px-4 border-light shadow-none fw-bold">
                    <option value="">{{ __('All Locations') }}</option>
                    @foreach($locations as $loc)
                      <option value="{{ $loc->id }}" @selected(request('location_id') == $loc->id)>{{ $loc->name ?? $loc->nama ?? 'Location '.$loc->id }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Active Status') }}</label>
                  <select name="status" class="form-select rounded-pill px-4 border-light shadow-none fw-bold">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="active" @selected(request('status') === 'active')>{{ __('Active') }}</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>{{ __('Inactive') }}</option>
                  </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                  <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold flex-grow-1">{{ __('Filter') }}</button>
                  <a href="{{ route('jobdesks.index') }}" class="btn btn-light border rounded-pill px-4 fw-bold text-muted flex-grow-1">{{ __('Reset') }}</a>
                </div>
              </form>
          </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-clipboard-text-outline text-info me-2"></i>{{ __('Role Repository') }}</h5>
                <span class="text-muted small fw-bold">{{ $jobdesks->total() }} entries</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No</th>
                                <th>{{ __('Role Name') }}</th>
                                <th>{{ __('Location') }}</th>
                                <th>{{ __('Role Scope') }}</th>
                                <th class="text-center">{{ __('Status') }}</th>
                                <th class="pe-4 text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobdesks as $jobdesk)
                                <tr>
                                    <td class="ps-4 fw-bold text-muted" style="width: 60px;">{{ $loop->iteration + ($jobdesks->currentPage()-1)*$jobdesks->perPage() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3 bg-light border rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px;">
                                                <i class="mdi mdi-briefcase-outline"></i>
                                            </div>
                                            <span class="fw-bold text-dark fs-6">{{ $jobdesk->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info bg-opacity-10 text-info border-info border-opacity-25 border px-3">
                                            <i class="mdi mdi-map-marker-outline me-1"></i>{{ $jobdesk->location?->name ?? $jobdesk->location?->nama ?? __('Global') }}
                                        </span>
                                    </td>
                                    <td><span class="text-muted small fw-semibold">{{ $jobdesk->role_scope ?? '-' }}</span></td>
                                    <td class="text-center">
                                        <span class="badge {{ $jobdesk->is_active ? 'badge-success' : 'badge-danger' }}">
                                            {{ $jobdesk->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border rounded-pill px-3 shadow-none fw-bold" type="button" data-bs-toggle="dropdown">
                                                Options <i class="mdi mdi-chevron-down ms-1"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2">
                                                <li><a class="dropdown-item fw-bold rounded-2" href="{{ route('jobdesks.show', $jobdesk) }}"><i class="mdi mdi-eye-outline me-2 text-info"></i> Details</a></li>
                                                <li><a class="dropdown-item fw-bold rounded-2" href="{{ route('jobdesks.edit', $jobdesk) }}"><i class="mdi mdi-pencil-outline me-2 text-primary"></i> Edit</a></li>
                                                <li><a class="dropdown-item fw-bold rounded-2" href="{{ route('jobdesks.catalogs.index', $jobdesk) }}"><i class="mdi mdi-format-list-bulleted me-2 text-warning"></i> Catalog</a></li>
                                                <li><a class="dropdown-item fw-bold rounded-2" href="{{ route('jobdesks.assignments.index', $jobdesk) }}"><i class="mdi mdi-account-group-outline me-2 text-success"></i> Team</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                  <form action="{{ route('jobdesks.destroy', $jobdesk) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                      @csrf @method('DELETE')
                                                      <button type="submit" class="dropdown-item fw-bold text-danger rounded-2"><i class="mdi mdi-trash-can-outline me-2"></i> Delete</button>
                                                  </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="py-5 opacity-25">
                                            <i class="mdi mdi-clipboard-off-outline fs-1 d-block mb-3"></i>
                                            <p class="mb-0">{{ __('No roles found.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($jobdesks->hasPages())
                <div class="card-footer border-top border-light p-4 bg-transparent d-flex justify-content-end">
                    {{ $jobdesks->links() }}
                </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
    .dropdown-item:active { background-color: var(--primary) !important; color: white !important; }
</style>
@endsection
