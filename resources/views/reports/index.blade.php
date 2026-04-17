@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ __('Insight Reports') }}
          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;">{{ __('Monitor field operations and verify documented activities.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <div class="d-flex justify-content-lg-end gap-3 flex-wrap">
            @if(auth()->user()->hasAnyRole(['Super Admin','Location Admin','Employee']))
            <a href="{{ route('reports.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
              <i class="mdi mdi-plus-circle-outline me-2"></i>{{ __('New Report') }}
            </a>
            <a href="{{ route('reports.employee-performance') }}" class="btn btn-outline-light text-dark border bg-white rounded-pill px-4 fw-bold shadow-sm">
              <i class="mdi mdi-chart-box-outline me-2"></i>{{ __('KPI Performance') }}
            </a>
            @endif
          </div>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="card mb-4 border-light shadow-soft rounded-4">
          <div class="card-body p-4">
              <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                  <label class="form-label text-muted smaller fw-bold text-uppercase mb-2">{{ __('Search Reports') }}</label>
                  <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Ticket ID, title or keyword...') }}">
                </div>
                <div class="col-md-3">
                  <label class="form-label text-muted smaller fw-bold text-uppercase mb-2">{{ __('Status Type') }}</label>
                  <select name="status" class="form-select fw-bold">
                    <option value="">{{ __('All Status') }}</option>
                    @foreach(['pending'=>__('Pending'),'approved'=>__('Approved'),'rejected'=>__('Rejected')] as $key=>$label)
                      <option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-4 d-flex gap-2 text-end">
                  <button class="btn btn-primary rounded-pill px-4 fw-bold flex-grow-1 shadow-soft" type="submit">{{ __('Filter') }}</button>
                  <a href="{{ route('reports.index') }}" class="btn btn-light border rounded-pill px-4 fw-bold text-muted">{{ __('Reset') }}</a>
                </div>
              </form>
          </div>
      </div>

      <!-- Table Section -->
      <div class="card border-light shadow-sm overflow-hidden">
        <div class="card-header border-bottom border-light p-4 bg-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-clipboard-text-clock-outline text-info me-2"></i>{{ __('Verified Documentation') }}</h5>
            <span class="text-muted smaller fw-bold">{{ $reports->total() }} entries</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead>
                <tr>
                  <th class="ps-4">No</th>
                  <th>{{ __('Reference / Subject') }}</th>
                  <th>{{ __('Originator') }}</th>
                  <th>{{ __('Location') }}</th>
                  <th>{{ __('Current Status') }}</th>
                  <th class="pe-4 text-end">{{ __('Action') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($reports as $report)
                  <tr onclick="window.location='{{ route('reports.show', $report) }}'" style="cursor: pointer;" class="hover-row">
                    <td class="ps-4 fw-bold text-muted smaller">{{ $loop->iteration + ($reports->currentPage()-1)*$reports->perPage() }}</td>
                    <td>
                        <div class="badge badge-secondary mb-1" style="font-family: 'JetBrains Mono', monospace; font-size: 0.65rem !important;">#{{ $report->ticket_number }}</div>
                        <div class="text-dark fw-bold smaller text-truncate" style="max-width: 240px;">{{ $report->title }}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold smaller" style="width: 32px; height: 32px;">
                                {{ substr($report->reporter?->name ?? '?', 0, 1) }}
                            </div>
                            <span class="text-dark smaller fw-bold">{{ $report->reporter?->name ?? '-' }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center smaller text-muted fw-500">
                             <i class="mdi mdi-map-marker-outline me-1"></i>{{ $report->location?->name ?? 'HQ' }}
                        </div>
                    </td>
                    <td>
                      @php
                          $statusBadge = match($report->status) {
                            'approved' => 'badge-success',
                            'rejected' => 'badge-danger',
                            default => 'badge-warning'
                          };
                      @endphp
                      <span class="badge {{ $statusBadge }}">
                        {{ strtoupper(__($report->status)) }}
                      </span>
                    </td>
                    <td class="pe-4 text-end">
                       <i class="mdi mdi-chevron-right text-muted opacity-50"></i>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center py-5">
                       <div class="p-3 bg-light rounded-circle d-inline-flex mb-3">
                          <i class="mdi mdi-file-question-outline fs-2 text-muted"></i>
                       </div>
                       <p class="text-muted smaller fw-bold mb-0">{{ __('Project documentation is currently empty.') }}</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
        @if($reports->hasPages())
           <div class="card-footer border-top border-light p-4 bg-white d-flex justify-content-end">
                {{ $reports->appends(request()->query())->links() }}
           </div>
        @endif
      </div>
    </div>
  </div>
</div>

<style>
    .smaller { font-size: 0.75rem; }
    .shadow-soft { box-shadow: 0 10px 30px rgba(0,0,0,0.03) !important; }
    .hover-row:hover { background-color: #fcfdfe !important; }
</style>
@endsection
