@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ __('Organization Sites') }}
          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;">{{ __('Manage physical locations and operational branches.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <a href="{{ route('locations.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
            <i class="mdi mdi-plus-circle-outline me-2"></i>{{ __('Add Location') }}
          </a>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="card mb-5 border-0 shadow-sm rounded-4">
          <div class="card-body p-4">
              <form method="GET" action="{{ route('locations.index') }}">
                  <div class="row g-3">
                      <div class="col-md-5">
                          <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Search Site') }}</label>
                          <input type="text" name="search" class="form-control rounded-pill px-4 border-light shadow-none" placeholder="{{ __('Search by name or code') }}" value="{{ request('search') }}">
                      </div>

                      <div class="col-md-3">
                          <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Status') }}</label>
                          <select name="status" class="form-select rounded-pill px-4 border-light shadow-none fw-bold">
                              <option value="">{{ __('All Status') }}</option>
                              <option value="active" @selected(request('status') === 'active')>{{ __('Active') }}</option>
                              <option value="inactive" @selected(request('status') === 'inactive')>{{ __('Inactive') }}</option>
                          </select>
                      </div>

                      <div class="col-md-4 d-flex align-items-end gap-2">
                          <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold flex-grow-1">
                              <i class="mdi mdi-filter-variant me-1"></i>{{ __('Filter') }}
                          </button>
                          <a href="{{ route('locations.index') }}" class="btn btn-light border rounded-pill px-4 fw-bold text-muted flex-grow-1">
                              <i class="mdi mdi-refresh me-1"></i>{{ __('Reset') }}
                          </a>
                      </div>
                  </div>
              </form>
          </div>
      </div>

      <!-- Data Table -->
      <div class="card border-0 shadow-sm overflow-hidden">
          <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-map-marker-radius text-info me-2"></i>{{ __('Global Site List') }}</h5>
              <span class="text-muted small fw-bold">{{ $locations->total() }} Locations</span>
          </div>
          <div class="card-body p-0">
              <div class="table-responsive">
                  <table class="table align-middle mb-0">
                      <thead>
                          <tr>
                              <th class="ps-4">No</th>
                              <th>{{ __('Site') }}</th>
                              <th>{{ __('Code') }}</th>
                              <th>{{ __('Timezone') }}</th>
                              <th class="text-center">{{ __('Status') }}</th>
                              <th class="pe-4 text-end">{{ __('Actions') }}</th>
                          </tr>
                      </thead>
                      <tbody>
                          @forelse($locations as $location)
                              <tr>
                                  <td class="ps-4 text-muted fw-bold" style="width: 60px;">{{ $loop->iteration + ($locations->currentPage()-1)*$locations->perPage() }}</td>
                                  <td>
                                      <div class="d-flex align-items-center">
                                          <div class="avatar-sm me-3 bg-light border rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 42px; height: 42px;">
                                              <i class="mdi mdi-office-building-marker"></i>
                                          </div>
                                          <div>
                                              <div class="fw-bold text-dark">{{ $location->name }}</div>
                                              <div class="text-muted smaller">{{ Str::limit($location->address ?: '-', 40) }}</div>
                                          </div>
                                      </div>
                                  </td>
                                  <td><span class="badge badge-info">{{ $location->code }}</span></td>
                                  <td><span class="text-dark small fw-medium">{{ $location->timezone }}</span></td>
                                  <td class="text-center">
                                      <span class="badge {{ $location->is_active ? 'badge-success' : 'badge-danger' }}">
                                          {{ strtoupper($location->is_active ? __('Active') : __('Inactive')) }}
                                      </span>
                                  </td>
                                  <td class="pe-4 text-end">
                                      <div class="dropdown">
                                          <button class="btn btn-sm btn-light border rounded-pill px-3 shadow-none fw-bold" type="button" data-bs-toggle="dropdown">
                                              Options <i class="mdi mdi-chevron-down ms-1"></i>
                                          </button>
                                          <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2">
                                              <li><a class="dropdown-item fw-bold rounded-2" href="{{ route('locations.show', $location) }}"><i class="mdi mdi-eye-outline me-2 text-info"></i> Details</a></li>
                                              @if(auth()->user()->hasRole('Super Admin'))
                                                <li><a class="dropdown-item fw-bold rounded-2" href="{{ route('locations.settings', $location) }}"><i class="mdi mdi-cog-outline me-2 text-warning"></i> Settings</a></li>
                                              @endif
                                              <li><a class="dropdown-item fw-bold rounded-2" href="{{ route('locations.edit', $location) }}"><i class="mdi mdi-pencil-outline me-2 text-primary"></i> Edit</a></li>
                                              <li><hr class="dropdown-divider"></li>
                                              <li>
                                                  <form action="{{ route('locations.destroy', $location) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                      @csrf @method('DELETE')
                                                      <button type="submit" class="dropdown-item fw-bold text-danger rounded-2">
                                                          <i class="mdi mdi-trash-can-outline me-2"></i> Delete
                                                      </button>
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
                                          <i class="mdi mdi-map-marker-off fs-1 d-block mb-3"></i>
                                          <p class="mb-0">{{ __('No locations found.') }}</p>
                                      </div>
                                  </td>
                              </tr>
                          @endforelse
                      </tbody>
                  </table>
              </div>
          </div>
          @if($locations->hasPages())
              <div class="card-footer bg-transparent border-top border-light p-4 d-flex justify-content-end">
                  {{ $locations->appends(request()->query())->links() }}
              </div>
          @endif
      </div>
    </div>
  </div>
</div>

<style>
    .smaller { font-size: 0.75rem; }
    .dropdown-item:active { background-color: var(--primary) !important; color: white !important; }
</style>
@endsection
