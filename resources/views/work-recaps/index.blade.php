@php use Carbon\Carbon; @endphp
@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h4 mb-1">Rekap Jam Kerja Bulanan</h1>
            <p class="text-muted mb-0">Total menit dari slot tugas yang disetujui + kehadiran (jika ada).</p>
        </div>
        <div>
            <a href="{{ route('tasks.index') }}" class="text-decoration-none">Kembali ke Tasks</a>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Bulan</label>
                <input type="month" name="month" value="{{ $monthParam }}" class="form-control">
            </div>
            @if(auth()->user()->hasRole('Super Admin'))
                <div class="col-md-3">
                    <label class="form-label">Lokasi</label>
                    <select name="location_id" class="form-select">
                        <option value="">Semua</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" @selected(request('location_id') == $loc->id)>{{ $loc->name ?? $loc->nama ?? 'Lokasi '.$loc->id }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-3">
                <label class="form-label">Karyawan</label>
                <select name="employee_id" class="form-select">
                    <option value="">Semua</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->nama ?? $emp->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Terapkan</button>
                <a href="{{ route('work-recaps.index') }}" class="btn btn-outline-secondary">Reset</a>
                <a href="{{ route('work-recaps.create') }}" class="btn btn-outline-primary ms-auto">Tambah Rekap</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Hasil Rekap</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Karyawan</th>
                        <th>Lokasi</th>
                        <th>Bulan</th>
                        <th>Slot Approved (menit)</th>
                        <th>Kehadiran (menit)</th>
                        <th>Total (menit)</th>
                        <th>Update</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recaps as $recap)
                        <tr>
                            <td>{{ $recap->employee->nama ?? 'Emp #'.$recap->employee_id }}</td>
                            <td>{{ $recap->location->name ?? $recap->location->nama ?? 'Lokasi #'.$recap->location_id }}</td>
                            <td>{{ sprintf('%02d', $recap->month) }}-{{ $recap->year }}</td>
                            <td>{{ $recap->slot_minutes_approved }}</td>
                            <td>{{ $recap->attendance_minutes }}</td>
                            <td><strong>{{ $recap->total_minutes }}</strong></td>
                            <td class="text-muted small">{{ optional($recap->updated_at)->format('d M Y H:i') }}</td>
                            <td class="d-flex gap-2">
                                <a href="{{ route('work-recaps.edit', $recap) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('work-recaps.destroy', $recap) }}" method="POST" onsubmit="return confirm('Hapus rekap ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada data untuk filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($recaps->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $recaps->links() }}
        </div>
    @endif
</div>
@endsection
