@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Tambah Mutasi</h3>
        <p class="text-muted mb-0">Catat perpindahan lokasi karyawan.</p>
    </div>
    <a href="{{ route('employee-transfers.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('employee-transfers.store') }}" class="row g-3">
            @csrf
            <div class="col-md-6">
                <label class="form-label">Karyawan</label>
                <select name="employee_id" class="form-select" required>
                    <option value="" disabled selected>-- Pilih Karyawan --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Dari Lokasi</label>
                <select name="from_location_id" class="form-select">
                    <option value="">-</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" @selected(old('from_location_id') == $loc->id)>{{ $loc->name ?? $loc->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Ke Lokasi</label>
                <select name="to_location_id" class="form-select">
                    <option value="">-</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" @selected(old('to_location_id') == $loc->id)>{{ $loc->name ?? $loc->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Efektif</label>
                <input type="date" name="effective_date" class="form-control" value="{{ old('effective_date') }}">
            </div>
            <div class="col-12">
                <label class="form-label">Alasan</label>
                <textarea name="reason" class="form-control" rows="3">{{ old('reason') }}</textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('employee-transfers.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
