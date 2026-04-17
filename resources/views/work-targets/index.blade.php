@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ __('Performance Benchmarks') }}
          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;">{{ __('Define monthly productivity goals for each operational site.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <a href="{{ route('work-targets.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
            <i class="mdi mdi-plus-circle-outline me-2"></i>{{ __('Define New Target') }}
          </a>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="card mb-5 border-0 shadow-sm rounded-4">
          <div class="card-body p-4">
              <form method="GET">
                  <div class="row g-3 align-items-end">
                      <div class="col-xl-3 col-md-4">
                          <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Target Month') }}</label>
                          <input type="month" name="month" value="{{ $monthParam }}" class="form-control rounded-pill px-4 border-light shadow-none fw-bold">
                      </div>

                      @if(auth()->user()->hasRole('Super Admin'))
                      <div class="col-xl-3 col-md-4">
                          <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Site Location') }}</label>
                          <select name="location_id" class="form-select rounded-pill px-4 border-light shadow-none fw-bold">
                              <option value="">{{ __('All Sites') }}</option>
                              @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" @selected(request('location_id') == $loc->id)>{{ $loc->name ?? $loc->nama ?? __('Location').' '.$loc->id }}</option>
                              @endforeach
                          </select>
                      </div>
                      @endif

                      <div class="col-xl-3 col-md-4">
                          <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Team Member') }}</label>
                          <select name="employee_id" class="form-select rounded-pill px-4 border-light shadow-none fw-bold">
                              <option value="">{{ __('All Members') }}</option>
                              @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->nama ?? $emp->id }}</option>
                              @endforeach
                          </select>
                      </div>

                      <div class="col-xl-3 col-md-12 d-flex gap-2">
                          <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold flex-grow-1 shadow-sm">
                              {{ __('Apply Filter') }}
                          </button>
                          <a href="{{ route('work-targets.index') }}" class="btn btn-light border rounded-pill px-4 fw-bold text-muted">
                              <i class="mdi mdi-refresh"></i>
                          </a>
                      </div>
                  </div>
              </form>
          </div>
      </div>

      <!-- Data Table -->
      <div class="card border-0 shadow-sm overflow-hidden mb-5">
          <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-bullseye-arrow text-info me-2"></i>{{ __('Quota Repository') }}</h5>
              <span class="text-muted small fw-bold">{{ $targets->total() }} benchmarks defined</span>
          </div>
          <div class="card-body p-0">
              <div class="table-responsive">
                  <table class="table align-middle mb-0">
                      <thead>
                          <tr>
                              <th class="ps-4">No</th>
                              <th>{{ __('Site') }}</th>
                              <th>{{ __('Target Holder') }}</th>
                              <th>{{ __('Benchmark Period') }}</th>
                              <th class="text-center">{{ __('Target (Min)') }}</th>
                              <th class="pe-4 text-end">{{ __('Actions') }}</th>
                          </tr>
                      </thead>
                      <tbody>
                          @forelse($targets as $target)
                              <tr>
                                  <td class="ps-4 text-muted fw-bold" style="width: 60px;">{{ $loop->iteration }}</td>
                                  <td>
                                      <div class="d-flex align-items-center">
                                          <div class="avatar-sm me-3 bg-light border rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 38px; height: 38px;">
                                              <i class="mdi mdi-map-marker-outline"></i>
                                          </div>
                                          <span class="fw-bold text-dark">{{ $target->location->name ?? $target->location->nama ?? __('Global') }}</span>
                                      </div>
                                  </td>
                                  <td>
                                      @if($target->employee)
                                          <div class="d-flex align-items-center">
                                              <div class="avatar-sm me-3 border rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                                  {{ substr($target->employee->nama, 0, 1) }}
                                              </div>
                                              <span class="small fw-bold text-dark">{{ $target->employee->nama }}</span>
                                          </div>
                                      @else
                                          <span class="text-muted smallest fw-bold text-uppercase opacity-50">{{ __('Site Global') }}</span>
                                      @endif
                                  </td>
                                  <td>
                                      <span class="badge badge-info px-3 py-2">
                                          {{ date("F", mktime(0, 0, 0, $target->month, 10)) }} {{ $target->year }}
                                      </span>
                                  </td>
                                  <td class="text-center"><span class="fw-bold text-dark fs-5">{{ number_format($target->target_minutes) }}</span></td>
                                  <td class="pe-4 text-end">
                                      <div class="d-flex justify-content-end gap-2">
                                          <a href="{{ route('work-targets.edit', $target) }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-primary shadow-none">
                                              <i class="mdi mdi-pencil-outline"></i>
                                          </a>
                                          <form action="{{ route('work-targets.destroy', $target) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                              @csrf @method('DELETE')
                                              <button type="submit" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-danger shadow-none">
                                                  <i class="mdi mdi-delete-outline"></i>
                                              </button>
                                          </form>
                                      </div>
                                  </td>
                              </tr>
                          @empty
                              <tr>
                                  <td colspan="6" class="text-center py-5">
                                      <div class="py-5 opacity-25">
                                          <i class="mdi mdi-bullseye-arrow fs-1 d-block mb-3"></i>
                                          <p class="mb-0">{{ __('No benchmarks defined for this selection.') }}</p>
                                      </div>
                                  </td>
                              </tr>
                          @endforelse
                      </tbody>
                  </table>
              </div>
          </div>
          @if($targets->hasPages())
              <div class="card-footer bg-transparent border-top border-light p-4 d-flex justify-content-end">
                  {{ $targets->links() }}
              </div>
          @endif
      </div>
    </div>
  </div>
</div>
@endsection
