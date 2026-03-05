@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Kontrak Kerja</h3>
        <p class="text-muted mb-0">Riwayat kontrak karyawan.</p>
    </div>
    <a href="{{ route('employee-contracts.create') }}" class="btn btn-primary btn-sm">Tambah Kontrak</a>
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
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="ended" @selected(request('status') === 'ended')>Ended</option>
                    <option value="terminated" @selected(request('status') === 'terminated')>Terminated</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">Filter</button>
                <a href="{{ route('employee-contracts.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">Daftar Kontrak</h3></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Karyawan</th>
                    <th>Jenis</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                    <tr>
                        <td>{{ $loop->iteration + ($contracts->currentPage()-1)*$contracts->perPage() }}</td>
                        <td>{{ $contract->employee?->nama ?? '-' }}</td>
                        <td>{{ $contract->contract_type }}</td>
                        <td>{{ $contract->start_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ $contract->end_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ ucfirst($contract->status) }}</td>
                        <td>
                            <a href="{{ route('employee-contracts.edit', $contract) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('employee-contracts.destroy', $contract) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus kontrak ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">Belum ada kontrak.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $contracts->links() }}</div>
</div>
@endsection
