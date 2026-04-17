@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ __('Time Off') }}
          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;">{{ __('Manage leave requests and attendance exceptions.') }}</p>
        </div>
      </div>

      @if(isset($balanceSummary))
        <div class="card p-4 soft-card-mint border-0 mb-5">
            <div class="d-flex align-items-center">
                <div class="avatar-sm me-4 rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="mdi mdi-calendar-check fs-2 text-success"></i>
                </div>
                <div>
                    <h5 class="text-dark fw-bold mb-1">{{ __('Yearly Leave Balance') }} ({{ $balanceSummary['year'] }})</h5>
                    <div class="d-flex gap-3 mt-1">
                        <span class="badge bg-white text-info border px-3">Total: {{ $balanceSummary['quota'] + $balanceSummary['carry_over'] }} d</span>
                        <span class="badge bg-white text-danger border px-3">Used: {{ $balanceSummary['used'] }} d</span>
                        <span class="badge bg-white text-success border px-3">Left: {{ $balanceSummary['remaining'] }} d</span>
                    </div>
                </div>
            </div>
        </div>
      @endif

      <div class="row g-4">
        <!-- Leave Request Form -->
        <div class="col-xl-4 col-lg-5">
          <div class="card p-4 h-100 border-0 shadow-sm">
            <h5 class="text-dark fw-bold mb-4 pb-2 border-bottom border-light d-flex align-items-center">
              <i class="mdi mdi-file-document-edit-outline me-2 text-primary"></i>{{ __('Request Form') }}
            </h5>

            <form method="POST" action="{{ route('leaves.store') }}">
              @csrf
              <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Start Date') }}</label>
                    <input type="date" name="start_date" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" required />
                  </div>

                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('End Date') }}</label>
                    <input type="date" name="end_date" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" required />
                  </div>

                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Type of Absence') }}</label>
                    <select name="type" class="form-select rounded-pill px-4 border-light shadow-none fw-bold" required>
                      <option value="sick">{{ __('Sick Leave') }}</option>
                      <option value="annual">{{ __('Annual Leave') }}</option>
                      <option value="unpaid">{{ __('Unpaid') }}</option>
                      <option value="other">{{ __('Other') }}</option>
                    </select>
                  </div>

                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Short Reason') }}</label>
                    <input type="text" name="reason" class="form-control rounded-pill px-4 border-light shadow-none" placeholder="{{ __('e.g. Health checkup') }}" />
                  </div>

                  <div class="col-12 mt-4 pt-2">
                    <button type="submit" class="btn btn-primary rounded-pill w-100 py-3 fw-bold shadow-lg">
                      <i class="mdi mdi-send-check me-2"></i>{{ __('SUBMIT REQUEST') }}
                    </button>
                  </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Leave List -->
        <div class="col-xl-8 col-lg-7">
          <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-history text-info me-2"></i>{{ __('History') }}</h5>
                <span class="text-muted small fw-bold">{{ $leaves->total() }} Records</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">{{ __('No') }}</th>
                                <th>{{ __('Employee') }}</th>
                                <th>{{ __('Duration') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="pe-4 text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $lv)
                              @php
                                $statusBadge = match($lv->status) {
                                  'approved' => 'badge-success',
                                  'rejected' => 'badge-danger',
                                  default => 'badge-warning'
                                };
                              @endphp
                                <tr>
                                    <td class="ps-4 text-muted fw-bold" style="width: 50px;">{{ $loop->iteration + ($leaves->currentPage()-1)*$leaves->perPage() }}</td>
                                    <td>
                                      <div class="d-flex align-items-center">
                                          <div class="avatar-xs me-2 rounded-circle bg-light border d-flex align-items-center justify-content-center fw-bold text-primary" style="width:30px; height:30px; font-size: 0.7rem;">
                                              {{ substr(optional($lv->user)->name, 0, 1) }}
                                          </div>
                                          <span class="fw-bold text-dark">{{ optional($lv->user)->name }}</span>
                                      </div>
                                    </td>
                                    <td>
                                        <div class="text-dark fw-semibold small">{{ $lv->start_date->format('d M') }} — {{ $lv->end_date->format('d M Y') }}</div>
                                        <div class="text-muted smallest">{{ $lv->start_date->diffInDays($lv->end_date) + 1 }} Days</div>
                                    </td>
                                    <td><span class="badge badge-info">{{ strtoupper($lv->type) }}</span></td>
                                    <td><span class="badge {{ $statusBadge }}">{{ strtoupper($lv->status) }}</span></td>
                                    <td class="pe-4 text-end">
                                      @if(auth()->user()->hasRole('Super Admin') || (auth()->user()->hasRole('Location Admin') && auth()->user()->location_id === $lv->location_id))
                                        @if($lv->status === 'pending')
                                          <div class="d-flex gap-2 justify-content-end">
                                            <form method="POST" action="{{ route('leaves.updateStatus', $lv) }}" class="d-inline">
                                              @csrf @method('PATCH')
                                              <input type="hidden" name="status" value="approved" />
                                              <button class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-success shadow-none"><i class="mdi mdi-check"></i></button>
                                            </form>
                                            <form method="POST" action="{{ route('leaves.updateStatus', $lv) }}" class="d-inline">
                                              @csrf @method('PATCH')
                                              <input type="hidden" name="status" value="rejected" />
                                              <button class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-danger shadow-none"><i class="mdi mdi-close"></i></button>
                                            </form>
                                          </div>
                                        @endif
                                      @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted py-5 text-center">
                                            <i class="mdi mdi-file-document-outline fs-1 d-block mb-3 opacity-25"></i>
                                            <p class="mb-0">{{ __('No entries found.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($leaves->hasPages())
                <div class="card-footer bg-transparent border-top border-light p-4 d-flex justify-content-end">
                    {{ $leaves->links() }}
                </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
    .smallest { font-size: 0.65rem; }
    .avatar-xs { width: 30px; height: 30px; }
</style>
@endsection
