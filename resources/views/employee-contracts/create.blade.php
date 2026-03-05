@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Tambah Kontrak</h3>
        <p class="text-muted mb-0">Catat kontrak kerja karyawan.</p>
    </div>
    <a href="{{ route('employee-contracts.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('employee-contracts.store') }}" class="row g-3">
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
            <div class="col-md-6">
                <label class="form-label">Jenis Kontrak</label>
                <input type="text" name="contract_type" class="form-control" value="{{ old('contract_type') }}" required placeholder="PKWT / PKWTT">
            </div>
            <div class="col-md-3">
                <label class="form-label">Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Selesai</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="active" @selected(old('status') === 'active')>Active</option>
                    <option value="ended" @selected(old('status') === 'ended')>Ended</option>
                    <option value="terminated" @selected(old('status') === 'terminated')>Terminated</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('employee-contracts.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
