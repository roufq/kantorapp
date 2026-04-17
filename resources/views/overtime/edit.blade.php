@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;">{{ __('Edit Overtime Request') }}</h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;">{{ __('Adjust overtime request details before approval.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="{{ route('overtime.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i>{{ __('Back') }}
          </a>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 24px !important;">
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 p-3 mb-4 shadow-sm border-0 bg-danger bg-opacity-25 text-white">
                            <div class="fw-bold mb-2"><i class="mdi mdi-alert-circle-outline me-1"></i>{{ __('Validation failed:') }}</div>
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('overtime.update', $overtime) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-4 mb-4">
                            <div class="col-lg-6">
                                <div class="card shadow-sm border-0 p-4 rounded-4 border border-light h-100">
                                    <h5 class="text-dark fw-bold mb-4 d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-clock-edit-outline me-2 text-info"></i>{{ __('Request Overtime Detail') }}
                                        </div>
                                        <a href="{{ route('overtime.show', $overtime) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 smaller fw-bold">{{ __('Back to Detail') }}</a>
                                    </h5>
                                    
                                    <div class="mb-4">
                                        <label for="date" class="form-label text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('Date') }}</label>
                                        <input type="date" name="date" class="form-control rounded-pill px-4 shadow-sm" id="date" required value="{{ old('date', $overtime->date->format('Y-m-d')) }}">
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="start_time" class="form-label text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('Start Time (WIB)') }}</label>
                                            <input type="time" name="start_time" class="form-control rounded-pill px-4 shadow-sm time-24" id="start_time" value="{{ old('start_time', $overtime->start_time_wib) }}" required step="60" pattern="[0-9]{2}:[0-9]{2}" lang="id-ID" inputmode="numeric" placeholder="HH:MM">
                                            <small class="text-muted smaller italic mt-1 d-block">{{ __('Format: HH:MM (24-hour)') }}</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="end_time" class="form-label text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('End Time (WIB)') }}</label>
                                            <input type="time" name="end_time" class="form-control rounded-pill px-4 shadow-sm time-24" id="end_time" value="{{ old('end_time', $overtime->end_time_wib) }}" required step="60" pattern="[0-9]{2}:[0-9]{2}" lang="id-ID" inputmode="numeric" placeholder="HH:MM">
                                            <small class="text-muted smaller italic mt-1 d-block">{{ __('Format: HH:MM (24-hour)') }}</small>
                                        </div>
                                    </div>

                                    @if(isset($limits))
                                    <div class="alert alert-info border-0 bg-info bg-opacity-10 text-white rounded-3 mt-4 mb-0 smaller">
                                        <i class="mdi mdi-information-outline me-1"></i>
                                        {{ __('Overtime limits:') }} <strong>{{ $limits['daily'] }} {{ __('hours/day') }}</strong>, <strong>{{ $limits['weekly'] }} {{ __('hours/week') }}</strong>.
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div class="card shadow-sm border-0 p-4 rounded-4 border border-light h-100">
                                    <h5 class="text-dark fw-bold mb-4 d-flex align-items-center">
                                        <i class="mdi mdi-message-text-outline me-2 text-warning"></i>{{ __('Reason for Overtime') }}
                                    </h5>
                                    <textarea name="reason" class="form-control mb-4" id="reason" rows="6" required placeholder="{{ __('Please explain why you need to work overtime...') }}" style="border-radius: 15px !important;">{{ old('reason', $overtime->reason) }}</textarea>

                                    @php $isEmployee = auth()->user()->hasRole('Employee'); @endphp

                                    @if($isEmployee)
                                        <div class="p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-5">
                                            <label class="text-dark fw-bold small mb-2 d-flex align-items-center">
                                                <i class="mdi mdi-account-group-outline me-2 text-info"></i>{{ __('Approvers') }}
                                            </label>
                                            <p class="text-muted smaller mb-2 italic">{{ __('Your request will be sent to:') }}</p>
                                            <div class="d-flex flex-column gap-2">
                                                @foreach(($autoApprovers->count() ? $autoApprovers : collect(\App\Models\User::whereIn('id', $overtime->selected_masters ?? [])->get())->map(fn($u) => ['user' => $u, 'level' => 1])) as $entry)
                                                    @php($approver = $entry['user'])
                                                    <div class="d-flex align-items-center justify-content-between p-2 px-3 bg-dark bg-opacity-25 rounded-pill border border-white border-opacity-5">
                                                        <span class="text-dark small fw-bold"><span class="text-muted fw-normal">{{ __('Level') }} {{ $entry['level'] }}:</span> {{ $approver->name }}</span>
                                                        <span class="badge rounded-pill bg-{{ $approver->hasRole('Location Admin') ? 'info' : 'primary' }} bg-opacity-25 text-{{ $approver->hasRole('Location Admin') ? 'info' : 'primary' }} smaller">
                                                            {{ $approver->hasRole('Location Admin') ? __('Location Admin') : __('Super Admin') }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <small class="text-muted italic smaller mt-2 d-block">{{ __('If no location admin exists, request goes to Super Admin.') }}</small>
                                        </div>
                                    @else
                                        <div class="p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-5">
                                            <label class="text-dark fw-bold small mb-3 d-flex align-items-center">
                                                <i class="mdi mdi-account-multiple-check-outline me-2 text-info"></i>{{ __('Select up to 2 Super Admins for Approval') }}
                                            </label>
                                            <div class="row g-2">
                                                @foreach($masters as $master)
                                                    <div class="col-md-6">
                                                        <div class="form-check custom-check">
                                                            <input class="form-check-input master-checkbox" type="checkbox" name="selected_masters[]" value="{{ $master->id }}" id="master{{ $master->id }}"
                                                                {{ in_array($master->id, old('selected_masters', $overtime->selected_masters ?? [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label text-dark small" for="master{{ $master->id }}">
                                                                {{ $master->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <small class="text-muted italic smaller mt-3 d-block border-top border-white border-opacity-5 pt-2">{{ __('Select at least 1 approver (max 2).') }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-5 pt-4 border-top border-white border-opacity-5 d-flex gap-3 justify-content-end align-items-center">
                            <a href="{{ route('overtime.show', $overtime) }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-lg py-2" id="submitBtn" @if(!$isEmployee && count(old('selected_masters', $overtime->selected_masters ?? [])) === 0) disabled @endif>
                                <i class="mdi mdi-content-save-check-outline me-2"></i>{{ __('Update Request') }}
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 pt-4 border-top border-white border-opacity-5">
                        <form action="{{ route('overtime.destroy', $overtime) }}" method="POST" onsubmit="return confirm('{{ __('Cancel this overtime request?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger text-decoration-none p-0 fw-bold smaller">
                                <i class="mdi mdi-trash-can-outline me-1"></i>{{ __('Cancel Request') }}
                            </button>
                        </form>
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
.custom-check .form-check-input { background-color: #f1f5f9; border-color: rgba(255,255,255,0.1); cursor: pointer; }
.custom-check .form-check-input:checked { background-color: #06b6d4; border-color: #06b6d4; }
.custom-check .form-check-label { cursor: pointer; }
</style>

@if(!auth()->user()->hasRole('Employee'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.master-checkbox');
    const submitBtn = document.getElementById('submitBtn');

    function updateSubmitButton() {
        const checkedCount = document.querySelectorAll('.master-checkbox:checked').length;
        submitBtn.disabled = checkedCount === 0 || checkedCount > 2;
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.master-checkbox:checked').length;
            if (checkedCount > 2) {
                this.checked = false;
                alert("{{ __('Select at most 2 approvers.') }}");
            }
            updateSubmitButton();
        });
    });
    updateSubmitButton();
});
</script>
@endif
@endsection
