@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;">{{ __('Location Shift Details') }}</h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;">{{ __('View details of shifts assigned to this location.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="{{ route('location-shifts.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i>{{ __('Back') }}
          </a>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-4">
          <div class="card shadow-sm border-0 p-4 rounded-4 border border-light shadow-sm h-100">
            <h5 class="text-dark fw-bold mb-4 d-flex align-items-center">
              <i class="mdi mdi-map-marker-radius text-info me-2 fs-4"></i>{{ __('Location Details') }}
            </h5>
            
            <div class="mb-3 p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-5">
              <label class="text-muted small fw-bold text-uppercase letter-spacing-1 d-block mb-1">{{ __('Name') }}</label>
              <div class="text-dark fw-bold">{{ $location->name }}</div>
            </div>

            <div class="mb-3 p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-5">
                <label class="text-muted small fw-bold text-uppercase letter-spacing-1 d-block mb-1">{{ __('Code') }}</label>
                <span class="badge rounded-pill bg-dark border border-light px-3">{{ $location->code }}</span>
            </div>

            <div class="mb-3 p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-5">
                <label class="text-muted small fw-bold text-uppercase letter-spacing-1 d-block mb-1">{{ __('Address') }}</label>
                <div class="text-muted smaller">{{ $location->address }}</div>
            </div>

            <div class="mb-3 p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-5">
                <label class="text-muted small fw-bold text-uppercase letter-spacing-1 d-block mb-1">{{ __('Timezone:') }}</label>
                <div class="text-dark smaller">{{ $location->timezone }}</div>
            </div>

            <div class="p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-5">
                <label class="text-muted small fw-bold text-uppercase letter-spacing-1 d-block mb-1">{{ __('Status:') }}</label>
                @if($location->is_active)
                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3">{{ __('Active') }}</span>
                @else
                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3">{{ __('Inactive') }}</span>
                @endif
            </div>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="card shadow-sm border-0 h-100" style="border-radius: 24px !important;">
            <div class="card-header border-bottom border-light p-4 d-flex justify-content-between align-items-center" style="background: #f8fafc;">
                <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-clock-check-outline me-2 text-info"></i>{{ __('Location Shift Configuration') }}</h5>
                <a href="{{ route('location-shifts.edit', $location) }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="mdi mdi-pencil-box-multiple-outline me-2"></i>{{ __('Manage Shifts') }}
                </a>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-0">{{ __('Shift Information') }}</h6>
                    <div class="text-muted smaller">{{ __('Total Assigned Shifts:') }} <span class="text-dark fw-bold ms-1">{{ $location->shifts->count() }}</span></div>
                </div>

                @if($location->shifts->count() > 0)
                    <div class="row g-3">
                        @foreach($location->shifts as $shift)
                            @php
                                $slots = $shift->pivot->time_slots ?? [];
                                if (isset($slots['start'], $slots['end'])) { $slots = [ $slots ]; }
                                $dayNames = [
                                    'monday' => __('Monday'), 'tuesday' => __('Tuesday'), 'wednesday' => __('Wednesday'),
                                    'thursday' => __('Thursday'), 'friday' => __('Friday'), 'saturday' => __('Saturday'), 'sunday' => __('Sunday')
                                ];
                            @endphp
                            <div class="col-md-6">
                                <div class="card shadow-sm border-0 p-3 rounded-4 border border-white border-opacity-5 h-100 transition-all shift-detail-card">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="text-dark fw-bold mb-1">{{ $shift->name }}</h6>
                                            <span class="badge rounded-pill bg-dark border border-light smaller px-2">{{ $shift->code }}</span>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-25 smaller px-2 mb-1">{{ strtoupper(str_replace('_',' ', $shift->pivot->category ?? $shift->category)) }}</span>
                                            <div class="smaller text-muted">{{ ucfirst($shift->shift_type) }}</div>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <label class="text-muted smaller fw-bold mb-1"><i class="mdi mdi-calendar-clock me-1"></i>{{ __('Schedule:') }}</label>
                                        <div class="text-dark smaller ps-3">{{ $shift->getFormattedSchedule() }}</div>
                                    </div>

                                    @if(!empty($slots))
                                        <div class="mb-2 mt-3">
                                            <label class="text-muted smaller fw-bold mb-1"><i class="mdi mdi-layers-outline me-1"></i>{{ __('Daily Details:') }}</label>
                                            <ul class="list-unstyled mb-0 ps-3">
                                                @foreach($slots as $slot)
                                                    @php
                                                        $days = isset($slot['days']) && is_array($slot['days']) && count($slot['days']) > 0
                                                            ? collect($slot['days'])->map(fn($d) => $dayNames[strtolower($d)] ?? ucfirst($d))->implode(', ')
                                                            : __('All days');
                                                    @endphp
                                                    <li class="smaller text-muted mb-1 d-flex justify-content-between">
                                                        <span>{{ $days }}</span>
                                                        <span class="text-dark fw-bold">{{ $slot['start'] ?? '?' }} - {{ $slot['end'] ?? '?' }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <div class="mt-3 pt-3 border-top border-white border-opacity-5 d-flex justify-content-between align-items-center">
                                        <div class="smaller">
                                            @if($shift->is_active)
                                                <i class="mdi mdi-check-decagram text-success me-1"></i><span class="text-success">{{ __('Active') }}</span>
                                            @else
                                                <i class="mdi mdi-alert-circle text-danger me-1"></i><span class="text-danger">{{ __('Inactive') }}</span>
                                            @endif
                                        </div>
                                        @if($shift->day)
                                            <div class="smaller text-muted"><i class="mdi mdi-calendar-today me-1"></i>{{ $shift->getDayName() }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 card shadow-sm border-0 rounded-4">
                        <i class="mdi mdi-clock-alert-outline fs-1 text-muted d-block mb-2"></i>
                        <span class="text-muted">{{ __('No shifts are currently assigned to this location.') }}</span>
                        <div class="mt-3">
                            <a href="{{ route('location-shifts.edit', $location) }}" class="btn btn-info btn-sm rounded-pill px-4 fw-bold shadow-sm">
                                <i class="mdi mdi-plus-circle-outline me-1"></i>{{ __('Assign Shifts') }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            <div class="card-footer border-top border-light p-4 bg-transparent">
                <a href="{{ route('location-shifts.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                    <i class="mdi mdi-arrow-left me-1"></i>{{ __('Back to List') }}
                </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.smaller { font-size: 0.8rem; }
.letter-spacing-1 { letter-spacing: 1px; }
.transition-all { transition: all 0.3s ease; }
.shift-detail-card:hover { border-color: rgba(6, 182, 212, 0.3); background: rgba(255,255,255,0.02); transform: translateY(-3px); }
</style>
@endsection
