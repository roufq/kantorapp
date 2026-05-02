@extends('layouts.appnew')

@section('content')
<div class="row mb-5 align-items-center">
    <div class="col-lg-7">
        <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ __('Absence Analytics') }}
        </h1>
        <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;">{{ __('Verify workforce gaps and audit documented leaves.') }}</p>
    </div>
    <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-light text-dark border bg-white rounded-pill px-4 fw-bold shadow-soft">
            <i class="mdi mdi-view-dashboard-outline me-2"></i>{{ __('Dashboard') }}
        </a>
    </div>
</div>

<!-- Filter Control -->
<div class="card mb-4 border-light shadow-soft rounded-4">
    <div class="card-body p-4">
        <form method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-xl-3 col-md-6">
                    <label class="form-label text-muted smaller fw-bold text-uppercase mb-2 letter-spacing-1">{{ __('Audit Start') }}</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                </div>
                <div class="col-xl-3 col-md-6">
                    <label class="form-label text-muted smaller fw-bold text-uppercase mb-2 letter-spacing-1">{{ __('Audit End') }}</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                </div>

                @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Location Admin'))
                <div class="col-xl-3 col-md-6">
                    <label class="form-label text-muted smaller fw-bold text-uppercase mb-2 letter-spacing-1">{{ __('Personnel Search') }}</label>
                    <select name="user_id" class="form-select fw-bold">
                        <option value="">{{ __('Full Directory') }}</option>
                        @php
                            $uQuery = \App\Models\User::orderBy('name');
                            if(auth()->user()->hasRole('Location Admin')){ $uQuery->where('location_id', auth()->user()->location_id); }
                            $users = $uQuery->get();
                        @endphp
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" @selected(request('user_id')==$u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-xl-3 col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold flex-grow-1 shadow-soft">{{ __('Analyze') }}</button>
                    <a href="{{ route('attendance.absences') }}" class="btn btn-light border rounded-pill px-3 text-muted"><i class="mdi mdi-refresh"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Absence Table -->
<div class="card border-light shadow-sm overflow-hidden mb-5">
    <div class="card-header bg-white border-bottom border-light p-4">
        <h5 class="mb-0 text-dark fw-bold"><i class="mdi mdi-account-off-outline text-danger me-2"></i>{{ __('Gap Ledger') }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">No</th>
                        <th>{{ __('Event Date') }}</th>
                        <th>{{ __('Personnel') }}</th>
                        <th>{{ __('Associated Site') }}</th>
                        <th>{{ __('Event Category') }}</th>
                        <th class="pe-4">{{ __('Administrative Notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absences as $a)
                        <tr class="hover-row">
                            <td class="ps-4 text-muted fw-bold smaller">{{ $loop->iteration + ($absences->currentPage()-1)*$absences->perPage() }}</td>
                            <td>
                                <div class="text-dark fw-bold smaller">{{ $a->date->format('d M Y') }}</div>
                                <div class="text-primary smallest fw-bold text-uppercase letter-spacing-1">{{ $a->date->format('l') }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold smaller" style="width: 32px; height: 32px;">
                                        {{ substr($a->user?->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="fw-bold text-dark smaller">{{ optional($a->user)->name }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="smaller text-muted fw-500"><i class="mdi mdi-map-marker-outline me-1"></i>{{ optional($a->location)->name ?? 'HQ' }}</div>
                            </td>
                            <td>
                                <span class="badge badge-danger rounded-pill px-3">{{ strtoupper($a->type) }}</span>
                            </td>
                            <td class="pe-4">
                                <span class="text-muted italic smaller">{{ $a->notes ?: '—' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="mdi mdi-check-decagram-outline fs-1 text-success opacity-25 mb-2 d-block"></i>
                                <span class="text-muted smaller fw-bold">{{ __('No historical gaps detected in the record.') }}</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer border-top border-light p-4 bg-white d-flex justify-content-end">
        {{ $absences->links() }}
    </div>
</div>

@endsection
