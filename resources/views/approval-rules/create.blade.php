@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Tambah Approval Rule</h3>
        <p class="text-muted mb-0">Tentukan approval berdasarkan departemen & nilai.</p>
    </div>
    <a href="{{ route('approval-rules.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('approval-rules.store') }}" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label class="form-label">Scope</label>
                <input type="text" name="scope" class="form-control" value="{{ old('scope', 'task') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Departemen (opsional)</label>
                <input type="text" name="department" class="form-control" value="{{ old('department') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Min Value</label>
                <input type="number" name="min_value" class="form-control" min="0" value="{{ old('min_value', 0) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Approval Level</label>
                <select name="approval_level" class="form-select" required>
                    <option value="location_admin" @selected(old('approval_level') === 'location_admin')>Admin Lokasi</option>
                    <option value="super_admin" @selected(old('approval_level') === 'super_admin')>Super Admin</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" checked>
                    <label class="form-check-label" for="isActive">Aktif</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('approval-rules.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
