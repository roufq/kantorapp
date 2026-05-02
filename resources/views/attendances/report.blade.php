@extends('layouts.appnew')

@section('content')
<div class="row mb-5 align-items-center">
    <div class="col-lg-7">
        <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ __('Attendance Ledger') }}
        </h1>
        <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;">{{ __('Complete audit trail of verified check-ins and operational compliance.') }}</p>
    </div>
    <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-light text-dark border bg-white rounded-pill px-4 fw-bold shadow-soft">
            <i class="mdi mdi-view-dashboard-outline me-2"></i>{{ __('Dashboard') }}
        </a>
    </div>
</div>

<!-- Header Insight -->
<div class="card mb-4 border-light shadow-soft rounded-4 overflow-hidden bg-white">
    <div class="card-body p-4 d-flex align-items-center">
        <i class="mdi mdi-shield-check-outline text-primary me-4 fs-2"></i>
        <div>
            <div class="text-muted smaller fw-bold text-uppercase letter-spacing-1">{{ __('Active Context') }}</div>
            <h5 class="text-dark fw-bold mb-0">
                {{ auth()->user()->hasRole('Location Admin') ? __('Location Admin Console') : __('Personnel Ledger') }} 
                <span class="text-primary mx-1">|</span> 
                {{ $effectiveLocation->name ?? 'Enterprise Global' }}
            </h5>
        </div>
    </div>
</div>

<!-- Filter Control -->
<div class="card mb-4 border-light shadow-soft rounded-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('attendance.report') }}">
            <div class="row g-3 align-items-end">
                <div class="col-xl-3 col-md-6">
                    <label class="form-label text-muted smaller fw-bold text-uppercase mb-2 letter-spacing-1">{{ __('Target Personnel') }}</label>
                    <select name="user_id" class="form-select fw-bold">
                        <option value="">{{ __('Full Directory') }}</option>
                        @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-4 col-md-6">
                    <label class="form-label text-muted smaller fw-bold text-uppercase mb-2 letter-spacing-1">{{ __('Audit Range') }}</label>
                    <div class="d-flex gap-2">
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold flex-grow-1 shadow-soft">{{ __('Execute Search') }}</button>
                    <a href="{{ route('attendance.report') }}" class="btn btn-light border rounded-pill px-4 fw-bold text-muted">{{ __('Reset') }}</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="mb-4 d-flex justify-content-end">
    <a href="{{ route('attendance.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="btn btn-sm btn-outline-light text-dark border bg-white rounded-pill px-4 fw-bold shadow-soft">
        <i class="mdi mdi-database-export-outline text-success me-1"></i> {{ __('Export Dataset') }}
    </a>
</div>

<!-- Audit Table -->
<div class="card border-light shadow-sm overflow-hidden mb-5">
    <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-dark fw-bold"><i class="mdi mdi-table-eye me-2 text-info"></i>{{ __('Verified Logs') }}</h5>
        <span class="badge badge-info">{{ $attendances->total() }} entries found</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="min-width: 900px;">
                <thead>
                    <tr>
                        <th class="ps-4">No</th>
                        <th>{{ __('Personnel') }}</th>
                        <th>{{ __('Duty Context') }}</th>
                        <th class="text-center">{{ __('Timestamp (IN/OUT)') }}</th>
                        <th class="text-center">{{ __('Compliance') }}</th>
                        <th class="pe-4 text-end">{{ __('Administrative Verify') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr class="hover-row">
                            <td class="ps-4 text-muted fw-bold smaller">{{ $loop->iteration + ($attendances->currentPage()-1)*$attendances->perPage() }}</td>
                            <td>
                                <div class="fw-bold text-dark smaller">{{ $attendance->user->name }}</div>
                                <div class="text-muted smallest fw-bold text-uppercase letter-spacing-1">{{ $attendance->user->employee->nama ?? '—' }}</div>
                            </td>
                            <td>
                                <span class="badge badge-indigo border-0 mb-1">{{ optional($attendance->shift)->name ?? 'Standard' }}</span>
                                @php
                                    $rKey = $attendance->user_id . '|' . $attendance->check_in_time->toDateString();
                                    $rosterEntries = $rosterEntries ?? collect();
                                    $rCollection = $rosterEntries[$rKey] ?? collect();
                                    $rEntry = $rCollection->firstWhere('shift_assignment_id', $attendance->shift_assignment_id) ?? $rCollection->first();
                                    $slot = $rEntry?->slot_index;
                                @endphp
                                <div class="text-muted smallest fw-bold">
                                    @if($rEntry) SLOT #{{ ($slot ?? 0)+1 }} @else <span class="text-muted italic opacity-50">UNROSTERED</span> @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-3">
                                    <div class="text-center">
                                        <div class="badge badge-success px-3">{{ $attendance->check_in_time->format('H:i') }}</div>
                                        <div class="smallest text-muted fw-bold mt-1">{{ $attendance->check_in_time->format('d M') }}</div>
                                    </div>
                                    <div class="text-center">
                                        @if($attendance->check_out_time)
                                            <div class="badge badge-warning px-3">{{ $attendance->check_out_time->format('H:i') }}</div>
                                            <div class="smallest text-muted fw-bold mt-1">{{ $attendance->check_out_time->format('d M') }}</div>
                                        @else
                                            <div class="badge badge-secondary bg-opacity-10 text-secondary border-secondary border-opacity-20 px-3">ACTIVE</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($attendance->is_late)
                                    <span class="badge badge-danger rounded-pill px-3">LATE</span>
                                @else
                                    <span class="badge badge-success rounded-pill px-3">ON TIME</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <form method="POST" action="{{ route('attendance.update.approval', $attendance->id) }}">
                                    @csrf @method('PATCH')
                                    <select name="approval_status" class="form-select form-select-sm rounded-pill border-light fw-bold smaller text-center" onchange="this.form.submit()" style="min-width: 110px;">
                                        <option value="pending" @selected($attendance->approval_status == 'pending')>{{ __('Pending') }}</option>
                                        <option value="approved" @selected($attendance->approval_status == 'approved')>{{ __('Verify') }}</option>
                                        <option value="rejected" @selected($attendance->approval_status == 'rejected')>{{ __('Void') }}</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="mdi mdi-alert-circle-outline fs-1 text-muted opacity-25 mb-2 d-block"></i>
                                <span class="text-muted smaller fw-bold">{{ __('No verified logs found for the selected period.') }}</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer border-top border-light p-4 bg-white d-flex justify-content-end">
        {{ $attendances->links() }}
    </div>
</div>

@endsection
