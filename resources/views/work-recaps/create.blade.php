@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;">{{ __('Add Work Hours Recap') }}</h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;">{{ __('Enter manual recap for employees per location and month.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="{{ route('work-recaps.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i>{{ __('Back') }}
          </a>
        </div>
      </div>

      <div class="row">
        <div class="col-xxl-8 col-xl-10 mx-auto">
          <div class="card shadow-sm border-0 p-4 p-md-5 rounded-4 border border-light shadow-lg">
            <form action="{{ route('work-recaps.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2">{{ __('Month') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-calendar-month"></i></span>
                            <input type="month" name="month" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none" value="{{ old('month', $monthParam) }}" required>
                        </div>
                    </div>

                    @if(auth()->user()->hasRole('Super Admin'))
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2">{{ __('Location') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-map-marker-outline"></i></span>
                            <select name="location_id" class="form-select bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none" required>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" @selected(old('location_id') == $loc->id)>{{ $loc->name ?? $loc->nama ?? __('Location').' '.$loc->id }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @else
                        <input type="hidden" name="location_id" value="{{ auth()->user()->location_id }}">
                    @endif

                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2">{{ __('Employee') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-account-outline"></i></span>
                            <select name="employee_id" class="form-select bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none" required>
                                <option value="">{{ __('Choose employee') }}</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->nama ?? $emp->id }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 mt-5">
                        <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2">{{ __('Slot Approved (minutes)') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-checkbox-marked-circle-outline"></i></span>
                            <input type="number" min="0" name="slot_minutes_approved" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none font-monospace" value="{{ old('slot_minutes_approved', 0) }}" required>
                        </div>
                    </div>

                    <div class="col-md-6 mt-5">
                        <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2">{{ __('Attendance (minutes)') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-clock-outline"></i></span>
                            <input type="number" min="0" name="attendance_minutes" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none font-monospace" value="{{ old('attendance_minutes', 0) }}" required>
                        </div>
                    </div>

                    <div class="col-12 mt-5 d-flex justify-content-end gap-3">
                        <a href="{{ route('work-recaps.index') }}" class="btn btn-outline-secondary rounded-pill px-5 py-2 fw-bold shadow-sm">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-lg">
                            <i class="mdi mdi-check-circle-outline me-2"></i>{{ __('Save') }}
                        </button>
                    </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.letter-spacing-1 { letter-spacing: 1px; }
.form-select option { background-color: #1a1d21; color: white; }
</style>
@endsection
