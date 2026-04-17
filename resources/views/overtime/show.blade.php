@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;">{{ __('Overtime Detail') }}</h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;">{{ __('Overtime request for date') }} {{ $overtime->date->format('d M Y') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="{{ route('overtime.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i>{{ __('Back') }}
          </a>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-8">
          <div class="card shadow-sm border-0 mb-4" style="border-radius: 24px !important;">
            <div class="card-header border-bottom border-light p-4 d-flex justify-content-between align-items-center" style="background: #f8fafc;">
                 <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-information-outline me-2 text-info"></i>{{ __('Overtime Request Details') }}</h5>
                 @if(auth()->user()->hasRole('Employee') && $overtime->user_id === auth()->id() && $overtime->status === 'pending')
                    <a href="{{ route('overtime.edit', $overtime) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold">
                        <i class="mdi mdi-pencil-outline me-1"></i>{{ __('Edit') }}
                    </a>
                 @endif
            </div>
            <div class="card-body p-4">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted smaller fw-bold text-uppercase letter-spacing-1">{{ __('Employee:') }}</label>
                            <div class="text-dark fw-bold fs-5">{{ $overtime->user->name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted smaller fw-bold text-uppercase letter-spacing-1">{{ __('Date:') }}</label>
                            <div class="text-dark"><i class="mdi mdi-calendar-outline me-1 text-info"></i>{{ $overtime->date->format('d M Y') }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted smaller fw-bold text-uppercase letter-spacing-1">{{ __('Time:') }}</label>
                            <div class="text-dark"><i class="mdi mdi-clock-outline me-1 text-warning"></i>{{ $overtime->start_time_wib }} - {{ $overtime->end_time_wib }} WIB</div>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted smaller fw-bold text-uppercase letter-spacing-1">{{ __('Duration:') }}</label>
                            <div class="text-dark fw-bold">{{ number_format($overtime->duration_minutes, 0) }} {{ __('minutes') }}</div>
                        </div>
                    </div>
                    @php
                        $statusColor = match($overtime->status) {
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default => 'warning'
                        };
                    @endphp
                    <div class="col-md-6 border-start border-light ps-md-4">
                        <div class="mb-3">
                            <label class="text-muted smaller fw-bold text-uppercase letter-spacing-1">{{ __('Status:') }}</label>
                            <div class="mt-1">
                                <span class="badge rounded-pill bg-{{ $statusColor }} bg-opacity-25 text-{{ $statusColor }} fw-bold px-3 py-2 shadow-sm">
                                    <i class="mdi mdi-circle-medium me-1"></i>{{ strtoupper(__($overtime->status)) }}
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted smaller fw-bold text-uppercase letter-spacing-1">{{ __('Created:') }}</label>
                            <div class="text-muted small">{{ $overtime->created_at->format('d M Y H:i') }}</div>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted smaller fw-bold text-uppercase letter-spacing-1">{{ __('Updated:') }}</label>
                            <div class="text-muted small">{{ $overtime->updated_at->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="text-muted smaller fw-bold text-uppercase letter-spacing-1 mb-2 d-block">{{ __('Reason:') }}</label>
                    <div class="p-3 bg-dark bg-opacity-25 rounded-3 border border-white border-opacity-5">
                        <p class="text-dark mb-0 italic" style="line-height: 1.6;">"{{ $overtime->reason }}"</p>
                    </div>
                </div>
            </div>
          </div>

          @if(auth()->user()->hasAnyRole(['Super Admin', 'Location Admin']) && in_array(auth()->id(), $overtime->selected_masters))
            @php
                $userApproval = $overtime->approvals->where('master_id', auth()->id())->first();
            @endphp
            @if($userApproval && $userApproval->status === 'pending')
                <div class="card shadow-sm border-0" style="border-radius: 24px !important;">
                    <div class="card-header border-bottom border-light p-4" style="background: #f8fafc;">
                        <h5 class="mb-0 text-dark fw-bold"><i class="mdi mdi-check-circle-outline me-2 text-success"></i>{{ __('Make Decision') }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('overtime.approve', $overtime) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('Decision') }}</label>
                                    <select name="status" class="form-select rounded-pill px-4 shadow-sm" required>
                                        <option value="approved">{{ __('Approve') }}</option>
                                        <option value="rejected">{{ __('Reject') }}</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('Notes (Optional)') }}</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('Add any notes...') }}" style="border-radius: 15px !important;"></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-lg">
                                        <i class="mdi mdi-check-all me-2"></i>{{ __('Submit Decision') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
          @endif
        </div>

        <div class="col-lg-4">
          <div class="card shadow-sm border-0 h-100" style="border-radius: 24px !important;">
            <div class="card-header border-bottom border-light p-4 d-flex align-items-center" style="background: #f8fafc;">
                <h5 class="mb-0 text-dark fw-bold"><i class="mdi mdi-shield-check-outline me-2 text-warning"></i>{{ __('Approval Status') }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-column gap-3">
                    @foreach($overtime->selected_masters as $masterId)
                        @php
                            $master = \App\Models\User::find($masterId);
                            $approval = $overtime->approvals->where('master_id', $masterId)->first();
                            $appStatusColor = match($approval?->status) {
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'secondary'
                            };
                        @endphp
                        <div class="card shadow-sm border-0 p-3 rounded-4 border border-light shadow-sm transition-all hover-lift">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="text-dark fw-bold small"><i class="mdi mdi-account-circle-outline me-1 text-info"></i>{{ $master->name }}</div>
                                <span class="badge rounded-pill bg-{{ $appStatusColor }} bg-opacity-25 text-{{ $appStatusColor }} fw-bold px-2 py-1 smaller">
                                    {{ strtoupper($approval ? __($approval->status) : __('Pending')) }}
                                </span>
                            </div>
                            
                            @if($approval && $approval->approved_at)
                                <div class="smaller text-muted mt-2 border-top border-white border-opacity-5 pt-2">
                                    <i class="mdi mdi-clock-check-outline me-1"></i>{{ __('Approved at:') }} {{ $approval->approved_at->format('d M Y H:i') }}
                                </div>
                            @endif
                            
                            @if($approval && $approval->notes)
                                <div class="mt-2 text-info smaller italic border-start border-info border-2 ps-2">
                                    <i class="mdi mdi-comment-outline me-1"></i>"{{ $approval->notes }}"
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.italic { font-style: italic; }
.smaller { font-size: 0.8rem; }
.letter-spacing-1 { letter-spacing: 1px; }
.hover-lift:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.4) !important; }
</style>
@endsection
