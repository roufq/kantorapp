@php use Carbon\Carbon; @endphp
@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div>
        <h3 class="mb-1">Rekap Jam Kerja Bulanan</h3>
        <p class="text-muted mb-0">Total menit dari slot tugas yang disetujui + kehadiran (jika ada).</p>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            @if(auth()->user()->hasRole('Super Admin'))
                <div class="col-md-3">
                    <label class="form-label">Lokasi</label>
                    <select name="location_id" class="form-select">
                        <option value="">Pilih lokasi</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" @selected($locationFilter == $loc->id)>{{ $loc->name ?? $loc->nama ?? 'Lokasi '.$loc->id }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-3">
                <label class="form-label">Karyawan</label>
                <select name="employee_id" class="form-select">
                    <option value="">Pilih karyawan</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected($employeeId == $emp->id)>{{ $emp->nama ?? $emp->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
            </div>
            <div class="col-12 d-flex flex-wrap align-items-center gap-2 mt-1">
                <button type="submit" class="btn btn-primary waves-effect waves-light">Terapkan</button>
                <a href="{{ route('work-recaps.index') }}" class="btn btn-outline-secondary waves-effect">Reset</a>
                @if($employeeId)
                    <button type="submit" name="export" value="1" class="btn btn-outline-success waves-effect">Export PDF</button>
                @endif
            </div>
        </form>
    </div>
</div>

@if($employeeId)
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Target (menit)</div>
                <div class="h4 mb-0">{{ $slotSummary['target_minutes'] ?? '—' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Slot Approved (menit)</div>
                <div class="h4 mb-0">{{ $slotSummary['slot_minutes'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Kehadiran (menit)</div>
                <div class="h4 mb-0">{{ $slotSummary['attendance_minutes'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Sisa (menit)</div>
                <div class="h4 mb-0">{{ $slotSummary['remaining'] ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-0">Detail Slot Approved</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Task</th>
                        <th>Slot</th>
                        <th>Menit</th>
                        <th>Approved</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($slotDetails as $slot)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $slot->task->title ?? 'Task #'.$slot->task_id }}</td>
                            <td>{{ $slot->name }}</td>
                            <td>{{ $slot->minutes }}</td>
                            <td>{{ optional($slot->approved_at)->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">Belum ada slot approved pada rentang ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Detail Kehadiran</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Tanggal</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Durasi (menit)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendanceDetails as $att)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ optional($att->check_in_time)->format('d M Y') }}</td>
                            <td>{{ optional($att->check_in_time)->format('H:i') }}</td>
                            <td>{{ optional($att->check_out_time)->format('H:i') }}</td>
                            <td>{{ $att->duration_minutes ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">Belum ada data kehadiran pada rentang ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
    <div class="alert alert-info">Pilih karyawan untuk melihat ringkasan dan detail.</div>
@endif
@endsection
