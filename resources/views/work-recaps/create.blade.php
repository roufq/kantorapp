@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <h1 class="h4 mb-1">Tambah Rekap Jam Kerja</h1>
    <p class="text-muted mb-0">Masukkan rekap manual untuk karyawan per lokasi dan bulan.</p>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('work-recaps.store') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label class="form-label">Bulan</label>
                <input type="month" name="month" class="form-control" value="{{ old('month', $monthParam) }}" required>
            </div>
            @if(auth()->user()->hasRole('Super Admin'))
                <div class="col-md-4">
                    <label class="form-label">Lokasi</label>
                    <select name="location_id" class="form-select" required>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" @selected(old('location_id') == $loc->id)>{{ $loc->name ?? $loc->nama ?? 'Lokasi '.$loc->id }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <input type="hidden" name="location_id" value="{{ auth()->user()->location_id }}">
            @endif
            <div class="col-md-4">
                <label class="form-label">Karyawan</label>
                <select name="employee_id" class="form-select" required>
                    <option value="">Pilih karyawan</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->nama ?? $emp->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Slot Approved (menit)</label>
                <input type="number" min="0" name="slot_minutes_approved" class="form-control" value="{{ old('slot_minutes_approved', 0) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Kehadiran (menit)</label>
                <input type="number" min="0" name="attendance_minutes" class="form-control" value="{{ old('attendance_minutes', 0) }}" required>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('work-recaps.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
