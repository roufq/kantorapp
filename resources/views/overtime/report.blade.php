@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;">{{ __('Overtime Report') }}</h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;">{{ __('Detailed recap of overtime requests based on selected filters.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="{{ route('overtime.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i>{{ __('Back') }}
          </a>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="card shadow-sm border-0 p-4 rounded-4 border border-light mb-4 shadow-sm">
          <form method="GET" action="{{ route('overtime.report') }}">
              <div class="row g-3">
                  <div class="col-xl-3 col-md-6">
                      <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2">{{ __('Search') }}</label>
                      <div class="input-group">
                          <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-magnify"></i></span>
                          <input type="text" name="search" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none" placeholder="{{ __('Reason or employee name...') }}" value="{{ request('search') }}">
                      </div>
                  </div>

                  <div class="col-xl-2 col-md-6">
                      <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2">{{ __('Status') }}</label>
                      <select name="status" class="form-select bg-dark bg-opacity-50 border-light text-white rounded-pill px-4 shadow-none">
                          <option value="">{{ __('All Status') }}</option>
                          <option value="pending" @selected(request('status') == 'pending')>{{ __('Pending') }}</option>
                          <option value="approved" @selected(request('status') == 'approved')>{{ __('Approved') }}</option>
                          <option value="rejected" @selected(request('status') == 'rejected')>{{ __('Rejected') }}</option>
                      </select>
                  </div>

                  <div class="col-xl-2 col-md-6">
                      <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2">{{ __('Start Date') }}</label>
                      <input type="date" name="start_date" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-pill px-4 shadow-none" value="{{ request('start_date') }}">
                  </div>

                  <div class="col-xl-2 col-md-6">
                      <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2">{{ __('End Date') }}</label>
                      <input type="date" name="end_date" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-pill px-4 shadow-none" value="{{ request('end_date') }}">
                  </div>

                  <div class="col-xl-3 col-md-12 d-flex align-items-end gap-2">
                      <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-lg flex-grow-1">
                          <i class="mdi mdi-filter-variant me-1"></i>{{ __('Filter') }}
                      </button>
                      <a href="{{ route('overtime.export', request()->query()) }}" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-lg flex-grow-1">
                          <i class="mdi mdi-microsoft-excel me-1"></i>{{ __('Export') }}
                      </a>
                  </div>
              </div>
          </form>
      </div>

      <!-- Data Table -->
      <div class="card shadow-sm border-0 mb-4" style="border-radius: 24px !important;">
          <div class="card-header border-bottom border-light p-4 bg-transparent">
              <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-table-clock text-info me-2 fs-4"></i>{{ __('Overtime Report') }}</h5>
          </div>
          <div class="card-body p-0">
              <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0 text-white">
                      <thead class="bg-white bg-opacity-5">
                          <tr>
                              <th class="ps-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1" style="width: 70px">{{ __('No') }}</th>
                              <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('Employee') }}</th>
                              <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('Date') }}</th>
                              <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('Time') }}</th>
                              <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1 text-center">{{ __('Duration (Minutes)') }}</th>
                              <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('Reason') }}</th>
                              <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1 text-center">{{ __('Status') }}</th>
                              <th class="pe-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('Approvals') }}</th>
                          </tr>
                      </thead>
                      <tbody>
                          @forelse($overtimes as $overtime)
                              @php
                                  $statusColor = match($overtime->status) {
                                      'approved' => 'success',
                                      'rejected' => 'danger',
                                      default => 'warning'
                                  };
                              @endphp
                              <tr class="border-bottom border-white border-opacity-5">
                                  <td class="ps-4 text-muted fw-bold">{{ $loop->iteration + (method_exists($overtimes, 'currentPage') ? ($overtimes->currentPage()-1)*$overtimes->perPage() : 0) }}</td>
                                  <td>
                                      <div class="d-flex align-items-center">
                                          <div class="avatar-sm me-3 rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                                              <i class="mdi mdi-account-outline"></i>
                                          </div>
                                          <span class="fw-bold">{{ $overtime->user->name }}</span>
                                      </div>
                                  </td>
                                  <td><span class="text-dark small fw-medium">{{ $overtime->date->format('d M Y') }}</span></td>
                                  <td><span class="text-muted small">{{ $overtime->start_time_wib }} - {{ $overtime->end_time_wib }} WIB</span></td>
                                  <td class="text-center"><span class="fw-bold text-info">{{ number_format($overtime->duration_minutes, 0) }}</span></td>
                                  <td><span class="text-muted small italic">{{ Str::limit($overtime->reason, 50) }}</span></td>
                                  <td class="text-center">
                                      <span class="badge rounded-pill bg-{{ $statusColor }} bg-opacity-25 text-{{ $statusColor }} fw-bold px-3 py-2 shadow-sm smallest">
                                          {{ strtoupper(__($overtime->status)) }}
                                      </span>
                                  </td>
                                  <td class="pe-4">
                                      <div class="d-flex flex-column gap-1">
                                          @foreach($overtime->approvals as $approval)
                                              @php
                                                  $appStatusColor = match($approval->status) {
                                                      'approved' => 'success',
                                                      'rejected' => 'danger',
                                                      default => 'secondary'
                                                  };
                                              @endphp
                                              <span class="badge rounded-pill bg-{{ $appStatusColor }} bg-opacity-10 text-{{ $appStatusColor }} border border-{{ $appStatusColor }} border-opacity-25 smallest fw-medium text-start">
                                                  {{ $approval->master->name }}: {{ ucfirst(__($approval->status)) }}
                                              </span>
                                          @endforeach
                                      </div>
                                  </td>
                              </tr>
                          @empty
                              <tr>
                                  <td colspan="8" class="text-center py-5">
                                      <div class="text-muted opacity-50 mb-3">
                                          <i class="mdi mdi-file-chart-outline fs-1"></i>
                                      </div>
                                      <span class="text-muted italic">{{ __('No reports yet.') }}</span>
                                  </td>
                              </tr>
                          @endforelse
                      </tbody>
                  </table>
              </div>
          </div>
          @if($overtimes->hasPages())
              <div class="card-footer bg-transparent border-top border-light p-4">
                  {{ $overtimes->appends(request()->query())->links() }}
              </div>
          @endif
      </div>
    </div>
  </div>
</div>

<style>
.smallest { font-size: 0.7rem; }
.italic { font-style: italic; }
.letter-spacing-1 { letter-spacing: 1px; }
.form-select option { background-color: #1a1d21; color: white; }
.table-hover tbody tr:hover { background-color: rgba(255,255,255,0.02) !important; }
</style>
@endsection
