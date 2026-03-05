@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Edit Kontrak</h3>
        <p class="text-muted mb-0">{{ $employee_contract->employee?->nama ?? '-' }}</p>
    </div>
    <a href="{{ route('employee-contracts.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('employee-contracts.update', $employee_contract) }}" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-6">
                <label class="form-label">Karyawan</label>
                <select name="employee_id" class="form-select" required>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(old('employee_id', $employee_contract->employee_id) == $emp->id)>{{ $emp->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Jenis Kontrak</label>
                <input type="text" name="contract_type" class="form-control" value="{{ old('contract_type', $employee_contract->contract_type) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', optional($employee_contract->start_date)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Selesai</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', optional($employee_contract->end_date)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="active" @selected(old('status', $employee_contract->status) === 'active')>Active</option>
                    <option value="ended" @selected(old('status', $employee_contract->status) === 'ended')>Ended</option>
                    <option value="terminated" @selected(old('status', $employee_contract->status) === 'terminated')>Terminated</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $employee_contract->notes) }}</textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('employee-contracts.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
