@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Histori Jabatan</h3>
        <p class="text-muted mb-0">Riwayat jabatan & departemen karyawan.</p>
    </div>
    <a href="{{ route('employee-positions.create') }}" class="btn btn-primary btn-sm">Tambah Histori</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Karyawan</label>
                <select name="employee_id" class="form-select">
                    <option value="">Semua</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">Filter</button>
                <a href="{{ route('employee-positions.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">Daftar Histori</h3></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Karyawan</th>
                    <th>Jabatan</th>
                    <th>Departemen</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($histories as $history)
                    <tr>
                        <td>{{ $loop->iteration + ($histories->currentPage()-1)*$histories->perPage() }}</td>
                        <td>{{ $history->employee?->nama ?? '-' }}</td>
                        <td>{{ $history->title }}</td>
                        <td>{{ $history->department ?? '-' }}</td>
                        <td>{{ $history->start_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ $history->end_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            <a href="{{ route('employee-positions.edit', $history) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('employee-positions.destroy', $history) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus histori ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">Belum ada histori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $histories->links() }}</div>
</div>
@endsection
