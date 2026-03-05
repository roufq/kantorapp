@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Tambah Target Output</h3>
        <p class="text-muted mb-0">Tentukan target output per jobdesk/karyawan per bulan.</p>
    </div>
    <a href="{{ route('jobdesk-targets.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('jobdesk-targets.store') }}" class="row g-3">
            @csrf
            <div class="col-md-6">
                <label class="form-label">Jobdesk</label>
                <select name="jobdesk_id" class="form-select" required>
                    <option value="" disabled selected>-- Pilih Jobdesk --</option>
                    @foreach($jobdesks as $jobdesk)
                        <option value="{{ $jobdesk->id }}" @selected(old('jobdesk_id') == $jobdesk->id)>{{ $jobdesk->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Karyawan (opsional)</label>
                <select name="employee_id" class="form-select">
                    <option value="">Semua Karyawan</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Bulan</label>
                <input type="month" name="month" class="form-control" value="{{ old('month', $monthParam) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Unit</label>
                <select name="unit" class="form-select" required>
                    <option value="points" @selected(old('unit') === 'points')>Points</option>
                    <option value="minutes" @selected(old('unit') === 'minutes')>Minutes</option>
                    <option value="weight" @selected(old('unit') === 'weight')>Weight</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Target</label>
                <input type="number" name="target_value" class="form-control" min="0" value="{{ old('target_value', 0) }}" required>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('jobdesk-targets.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
