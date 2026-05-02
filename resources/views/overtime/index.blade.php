@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ __('Overtime Logic') }}
          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;">{{ __('Efficiently manage extra hours and approval workflows.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
            <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                @if(auth()->user()->hasRole('Super Admin'))
                    <a href="{{ route('overtime.report') }}" class="btn btn-outline-light text-dark border bg-white rounded-pill px-4 fw-bold shadow-sm">
                        <i class="mdi mdi-file-chart-outline me-2"></i>{{ __('Analytics') }}
                    </a>
                @endif
                @if(auth()->user()->hasRole('Employee'))
                    <a href="{{ route('overtime.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
                        <i class="mdi mdi-plus-circle-outline me-2"></i>{{ __('New Request') }}
                    </a>
                @endif
            </div>
        </div>
      </div>

      <div class="card mb-5 border-0 shadow-sm rounded-4">
          <div class="card-body p-4">
              <form method="GET" action="{{ route('overtime.index') }}" class="row g-3">
                  <div class="col-md-9">
                      <div class="position-relative">
                          <i class="mdi mdi-magnify position-absolute text-muted" style="top: 10px; left: 15px;"></i>
                          <input type="text" name="search" class="form-control rounded-pill ps-5 border-light shadow-none" placeholder="{{ __('Search employee or reason...') }}" value="{{ request('search') }}">
                      </div>
                  </div>
                  <div class="col-md-3">
                      <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold w-100">{{ __('Search') }}</button>
                  </div>
              </form>
          </div>
      </div>

      <div class="row g-4">
        @foreach($overtimes as $overtime)
          @php
              $statusBadge = match($overtime->status) {
                  'approved' => 'badge-success',
                  'rejected' => 'badge-danger',
                  default => 'badge-warning'
              };
          @endphp
          <div class="col-xl-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-lift transition-all">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="badge {{ $statusBadge }}">
                            {{ strtoupper(__($overtime->status)) }}
                        </div>
                        <div class="text-muted small fw-bold"><i class="mdi mdi-calendar-outline me-1"></i>{{ $overtime->date->format('d M Y') }}</div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar-sm me-3 bg-light border rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 48px; height: 48px;">
                            {{ substr($overtime->user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="text-muted smaller fw-bold mb-1">{{ __('Employee') }}</div>
                            <div class="text-dark fw-bold h6 mb-0">{{ $overtime->user->name }}</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="p-2 soft-card-celeste rounded-3 text-center border-0">
                                <div class="text-muted smallest fw-bold">{{ __('Duration') }}</div>
                                <div class="text-dark fw-bold small">{{ number_format($overtime->duration_minutes, 0) }} min</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 soft-card-celeste rounded-3 text-center border-0">
                                <div class="text-muted smallest fw-bold">{{ __('Start Time') }}</div>
                                <div class="text-dark fw-bold small">{{ $overtime->start_time_wib }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-4 mb-4">
                        <div class="text-muted smaller fw-bold mb-2">{{ __('Business Reason') }}</div>
                        <p class="text-dark small mb-0 italic" style="line-height: 1.4;">"{{ Str::limit($overtime->reason, 120) }}"</p>
                    </div>

                    @if(count($overtime->approvals) > 0)
                        <div class="mb-4">
                            <div class="text-muted smaller fw-bold mb-2 opacity-75">{{ __('Workflows') }}</div>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($overtime->approvals as $approval)
                                    <span class="badge badge-info bg-opacity-10 text-info border border-info border-opacity-10 smallest">{{ $approval->master->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-4 pb-4 mt-auto">
                    <div class="d-flex gap-2 pt-3 border-top border-light">
                        <a href="{{ route('overtime.show', $overtime) }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold flex-grow-1">Details</a>

                        @if(auth()->user()->hasAnyRole(['Super Admin', 'Location Admin']) && in_array(auth()->id(), $overtime->selected_masters))
                            @php $userApproval = $overtime->approvals->where('master_id', auth()->id())->first(); @endphp
                            @if($userApproval && $userApproval->status === 'pending')
                                <button class="btn btn-sm btn-dark rounded-pill px-3 fw-bold flex-grow-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#approveModal{{ $overtime->id }}">
                                    Approve
                                </button>
                            @endif
                        @endif

                        @if(auth()->user()->hasRole('Employee') && $overtime->user_id === auth()->id() && $overtime->status === 'pending')
                            <form action="{{ route('overtime.destroy', $overtime) }}" method="POST" class="d-inline flex-grow-1" onsubmit="return confirm('{{ __('Cancel this?') }}');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold w-100">Cancel</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
          </div>

          <!-- Approval Modal -->
          @if(auth()->user()->hasAnyRole(['Super Admin', 'Location Admin']) && in_array(auth()->id(), $overtime->selected_masters))
              @php $userApproval = $overtime->approvals->where('master_id', auth()->id())->first(); @endphp
              @if($userApproval && $userApproval->status === 'pending')
                  <div class="modal fade" id="approveModal{{ $overtime->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content border-0 shadow-lg rounded-4">
                              <div class="modal-header border-bottom border-light p-4">
                                  <h5 class="modal-title text-dark fw-bold"><i class="mdi mdi-check-decagram-outline me-2 text-primary"></i>Decision Workflow</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>
                              <form action="{{ route('overtime.approve', $overtime) }}" method="POST">
                                  @csrf @method('PATCH')
                                  <div class="modal-body p-4">
                                      <div class="mb-4">
                                          <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Your Decision') }}</label>
                                          <select name="status" class="form-select rounded-pill px-4 border-light shadow-none fw-bold" required>
                                              <option value="approved">{{ __('Approve Request') }}</option>
                                              <option value="rejected">{{ __('Decline Request') }}</option>
                                          </select>
                                      </div>
                                      <div class="mb-0">
                                          <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Review Comment') }}</label>
                                          <textarea name="notes" class="form-control rounded-4 border-light shadow-none" rows="3" placeholder="{{ __('Why is this decision being made?') }}"></textarea>
                                      </div>
                                  </div>
                                  <div class="p-4 border-top border-light d-flex gap-2">
                                      <button type="button" class="btn btn-light border rounded-pill px-4 fw-bold flex-grow-1" data-bs-dismiss="modal">Close</button>
                                      <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold flex-grow-1">Confirm Decision</button>
                                  </div>
                              </form>
                          </div>
                      </div>
                  </div>
              @endif
          @endif
        @endforeach
      </div>

      <!-- Pagination -->
      @if($overtimes->hasPages())
          <div class="d-flex justify-content-end mt-5">
              {{ $overtimes->appends(request()->query())->links() }}
          </div>
      @endif
    </div>
  </div>
</div>

@endsection
