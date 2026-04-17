@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ __('Shift Architect') }}
          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;">{{ __('Automate complex rotation schedules for field and onsite teams.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <a href="{{ route('dashboard') }}" class="btn btn-light border rounded-pill px-4 fw-bold text-muted shadow-sm">
            <i class="mdi mdi-arrow-left me-2"></i>{{ __('Dashboard') }}
          </a>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-soft-mint border-0 rounded-4 p-3 mb-4 d-flex align-items-center fw-bold">
            <i class="mdi mdi-check-circle-outline fs-4 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
      @endif
      
      @if($errors->any())
        <div class="alert alert-soft-rose border-0 rounded-4 p-3 mb-4">
            <div class="fw-bold mb-2 small text-uppercase letter-spacing-1"><i class="mdi mdi-alert-circle-outline me-1"></i>{{ __('Configuration Error') }}</div>
            <ul class="mb-0 smaller fw-bold">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
      @endif

      <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-header border-bottom border-light p-4 bg-white d-flex align-items-center">
          <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
             <i class="mdi mdi-calendar-sync-outline text-primary fs-4"></i>
          </div>
          <h5 class="card-title mb-0 text-dark fw-bold">{{ __('Dynamic Schedule Generator') }}</h5>
        </div>
        <form method="POST" action="{{ route('shifts.scheduler.generate') }}">
          @csrf
          <div class="card-body p-4 p-lg-5">
            <div class="row g-4 mb-5">
              <div class="col-lg-4">
                <label class="form-label text-muted small fw-bold text-uppercase mb-3">{{ __('Site Deployment') }}</label>
                <select name="location_id" class="form-select rounded-pill px-4 border-light shadow-none fw-bold" onchange="this.form.submit()">
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" @selected($locationId==$loc->id)>{{ $loc->name }}</option>
                    @endforeach
                </select>
              </div>
              <div class="col-lg-4">
                <label class="form-label text-muted small fw-bold text-uppercase mb-3">{{ __('Campaign Period') }}</label>
                <div class="d-flex gap-2">
                    <input type="date" name="date_start" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" value="{{ old('date_start') ?? now()->toDateString() }}">
                    <input type="date" name="date_end" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" value="{{ old('date_end') ?? now()->addWeek()->toDateString() }}">
                </div>
              </div>
              <div class="col-lg-4">
                <label class="form-label text-muted small fw-bold text-uppercase mb-3">{{ __('Cycle Rules') }}</label>
                <div class="input-group">
                    <input type="number" min="0" name="weekly_off_every" class="form-control rounded-pill-start border-light shadow-none fw-bold text-center" style="width: 80px;" value="{{ old('weekly_off_every', 7) }}">
                    <span class="input-group-text border-light bg-transparent font-monospace smaller px-2">DAYS</span>
                    <input type="text" name="weekly_off_label" class="form-control rounded-pill-end border-light shadow-none fw-bold text-center" value="{{ old('weekly_off_label','OFF') }}">
                </div>
                <small class="text-muted smallest italic d-block mt-2 px-2">{{ __('Define "OFF" frequency for rotating shifts.') }}</small>
              </div>
            </div>

            <div class="row g-4">
              <div class="col-md-6">
                 <div class="p-4 bg-light rounded-4 h-100">
                    <label class="form-label text-dark fw-bold mb-4 d-flex align-items-center">
                        <i class="mdi mdi-account-group-outline me-2 text-info"></i>{{ __('Target Team') }}
                    </label>
                    <div class="list-group list-group-flush rounded-4 overflow-hidden border border-light">
                        @forelse($users as $u)
                            <label class="list-group-item list-group-item-action d-flex align-items-center py-3 border-light">
                                <input class="form-check-input me-3" type="checkbox" name="user_ids[]" value="{{ $u->id }}">
                                <div>
                                    <div class="fw-bold text-dark small">{{ $u->name }}</div>
                                    <div class="text-muted smallest">{{ $u->email }}</div>
                                </div>
                            </label>
                        @empty
                            <div class="p-4 text-center opacity-50 italic smallest">{{ __('No deployable members at this site.') }}</div>
                        @endforelse
                    </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="p-4 bg-light rounded-4 h-100">
                    <label class="form-label text-dark fw-bold mb-4 d-flex align-items-center">
                        <i class="mdi mdi-clock-outline me-2 text-warning"></i>{{ __('Reference Shift') }}
                    </label>
                    @forelse($locationShifts as $ls)
                        <label class="card mb-2 border border-light rounded-4 hover-lift transition-all cursor-pointer">
                            <div class="card-body p-3 d-flex align-items-center">
                                <input type="radio" name="location_shift_id" value="{{ $ls->id }}" class="form-check-input me-3" required>
                                <div>
                                    <div class="fw-bold text-dark small">{{ $ls->shift->name ?? 'Default Shift' }}</div>
                                    <div class="badge badge-info mt-1">{{ $ls->shift->category ?? 'Regular' }}</div>
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="p-4 text-center opacity-50 italic smallest">{{ __('No active shifts defined for this site.') }}</div>
                    @endforelse
                    <div class="mt-4 p-3 soft-card-mint rounded-4">
                        <i class="mdi mdi-information-outline me-2"></i>
                        <span class="smallest italic">{{ __('Configuration will use location-specific timings.') }}</span>
                    </div>
                </div>
              </div>
            </div>

            <div class="mt-5 pt-4 border-top border-light d-flex justify-content-end gap-3">
                <button type="submit" class="btn btn-dark rounded-pill px-5 py-3 fw-bold shadow-lg">
                  <i class="mdi mdi-auto-fix me-2"></i>{{ __('Analyze & Generate Roster') }}
                </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
    .smallest { font-size: 0.7rem; }
    .cursor-pointer { cursor: pointer; }
</style>
@endsection
