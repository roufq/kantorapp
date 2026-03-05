@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Jobdesk</h3>
        <p class="text-muted mb-0">Kelola jobdesk dan scope tugas per perusahaan/lokasi.</p>
    </div>
    <a href="{{ route('jobdesks.create') }}" class="btn btn-primary btn-sm">Tambah Jobdesk</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Lokasi</label>
                <select name="location_id" class="form-select">
                    <option value="">Semua Lokasi</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" @selected(request('location_id') == $loc->id)>{{ $loc->name ?? $loc->nama ?? 'Lokasi '.$loc->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('jobdesks.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Jobdesk</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th style="width:50px">No</th>
                            <th>Nama</th>
                            <th>Lokasi</th>
                            <th>Role Scope</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobdesks as $jobdesk)
                            <tr>
                                <td>{{ $loop->iteration + ($jobdesks->currentPage()-1)*$jobdesks->perPage() }}</td>
                                <td>{{ $jobdesk->name }}</td>
                                <td>{{ $jobdesk->location?->name ?? $jobdesk->location?->nama ?? 'Global' }}</td>
                                <td>{{ $jobdesk->role_scope ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $jobdesk->is_active ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $jobdesk->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('jobdesks.show', $jobdesk) }}" class="btn btn-sm btn-outline-info">View</a>
                                    <a href="{{ route('jobdesks.edit', $jobdesk) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="{{ route('jobdesks.catalogs.index', $jobdesk) }}" class="btn btn-sm btn-outline-secondary">Task Catalog</a>
                                    <a href="{{ route('jobdesks.assignments.index', $jobdesk) }}" class="btn btn-sm btn-outline-success">Assignments</a>
                                    <form action="{{ route('jobdesks.destroy', $jobdesk) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus jobdesk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada jobdesk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $jobdesks->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
