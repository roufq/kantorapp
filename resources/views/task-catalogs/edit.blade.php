@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Edit Task Catalog</h3>
        <p class="text-muted mb-0">Jobdesk: {{ $jobdesk->name }}</p>
    </div>
    <a href="{{ route('jobdesks.catalogs.index', $jobdesk) }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('jobdesks.catalogs.update', [$jobdesk, $catalog]) }}" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-6">
                <label class="form-label">Nama Task</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $catalog->name) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Unit</label>
                <select name="unit" class="form-select">
                    <option value="points" @selected(old('unit', $catalog->unit) === 'points')>Points</option>
                    <option value="minutes" @selected(old('unit', $catalog->unit) === 'minutes')>Minutes</option>
                    <option value="weight" @selected(old('unit', $catalog->unit) === 'weight')>Weight</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Nilai</label>
                <input type="number" min="1" name="value" class="form-control" value="{{ old('value', $catalog->value) }}" required>
            </div>
            <div class="col-12">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $catalog->description) }}</textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tipe</label>
                <select name="task_type" class="form-select">
                    <option value="routine" @selected(old('task_type', $catalog->task_type) === 'routine')>Routine</option>
                    <option value="project" @selected(old('task_type', $catalog->task_type) === 'project')>Project</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" @checked(old('is_active', $catalog->is_active))>
                    <label class="form-check-label" for="isActive">Aktif</label>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('jobdesks.catalogs.index', $jobdesk) }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
