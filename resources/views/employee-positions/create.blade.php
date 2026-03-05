@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Tambah Histori Jabatan</h3>
        <p class="text-muted mb-0">Catat jabatan karyawan.</p>
    </div>
    <a href="{{ route('employee-positions.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('employee-positions.store') }}" class="row g-3">
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
                <label class="form-label">Jabatan</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Departemen</label>
                <input type="text" name="department" class="form-control" value="{{ old('department') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Selesai</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
            </div>
            <div class="col-12">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('employee-positions.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
