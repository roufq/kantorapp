@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Approval Rules</h3>
        <p class="text-muted mb-0">Atur workflow approval berdasarkan departemen & nilai.</p>
    </div>
    <a href="{{ route('approval-rules.create') }}" class="btn btn-primary btn-sm">Tambah Rule</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Scope</label>
                <input type="text" name="scope" class="form-control" value="{{ request('scope', 'task') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Departemen</label>
                <input type="text" name="department" class="form-control" value="{{ request('department') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">Filter</button>
                <a href="{{ route('approval-rules.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Rules</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Scope</th>
                    <th>Departemen</th>
                    <th>Min Value</th>
                    <th>Approval Level</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rules as $rule)
                    <tr>
                        <td>{{ $loop->iteration + ($rules->currentPage()-1)*$rules->perPage() }}</td>
                        <td>{{ $rule->scope }}</td>
                        <td>{{ $rule->department ?? 'Semua' }}</td>
                        <td>{{ $rule->min_value }}</td>
                        <td>{{ $rule->approval_level === 'super_admin' ? 'Super Admin' : 'Admin Lokasi' }}</td>
                        <td>
                            <span class="badge {{ $rule->is_active ? 'badge-success' : 'badge-secondary' }}">
                                {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('approval-rules.edit', $rule) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('approval-rules.destroy', $rule) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus rule ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">Belum ada rule.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $rules->links() }}
    </div>
</div>
@endsection
