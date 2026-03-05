@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Target Output Jobdesk</h3>
        <p class="text-muted mb-0">Tetapkan target output per jobdesk/karyawan per bulan.</p>
    </div>
    <a href="{{ route('jobdesk-targets.create') }}" class="btn btn-primary btn-sm">Tambah Target</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Jobdesk</label>
                <select name="jobdesk_id" class="form-select">
                    <option value="">Semua</option>
                    @foreach($jobdesks as $jobdesk)
                        <option value="{{ $jobdesk->id }}" @selected(request('jobdesk_id') == $jobdesk->id)>{{ $jobdesk->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Karyawan</label>
                <select name="employee_id" class="form-select">
                    <option value="">Semua</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Bulan</label>
                <input type="month" name="month" class="form-control" value="{{ request('month', $monthParam) }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">Filter</button>
                <a href="{{ route('jobdesk-targets.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Target Output</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jobdesk</th>
                    <th>Karyawan</th>
                    <th>Bulan</th>
                    <th>Unit</th>
                    <th>Target</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($targets as $target)
                    <tr>
                        <td>{{ $loop->iteration + ($targets->currentPage()-1)*$targets->perPage() }}</td>
                        <td>{{ $target->jobdesk?->name ?? '-' }}</td>
                        <td>{{ $target->employee?->nama ?? 'Semua Karyawan' }}</td>
                        <td>{{ sprintf('%02d-%04d', $target->month, $target->year) }}</td>
                        <td>{{ ucfirst($target->unit) }}</td>
                        <td>{{ $target->target_value }}</td>
                        <td>
                            <a href="{{ route('jobdesk-targets.edit', $target) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('jobdesk-targets.destroy', $target) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus target ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">Belum ada target output.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $targets->links() }}
    </div>
</div>
@endsection
