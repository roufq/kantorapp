@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Assign Jobdesk</h3>
        <p class="text-muted mb-0">Jobdesk: {{ $jobdesk->name }}</p>
    </div>
    <a href="{{ route('jobdesks.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="POST" action="{{ route('jobdesks.assignments.store', $jobdesk) }}" class="row g-3">
            @csrf
            <div class="col-md-6">
                <label class="form-label">Pilih Karyawan</label>
                <select name="employee_ids[]" class="form-select" multiple required>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->nama }} @if($emp->location) - {{ $emp->location->name ?? $emp->location->nama }} @endif</option>
                    @endforeach
                </select>
                <small class="text-muted">Gunakan Ctrl/Command untuk pilih banyak.</small>
            </div>
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
            </div>
            <div class="col-md-3 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="is_primary" value="1" id="isPrimary" checked>
                    <label class="form-check-label" for="isPrimary">Primary Jobdesk</label>
                </div>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Simpan Assignment</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Assignment Aktif</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Karyawan</th>
                    <th>Lokasi</th>
                    <th>Primary</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $assignment)
                    <tr>
                        <td>{{ $loop->iteration + ($assignments->currentPage()-1)*$assignments->perPage() }}</td>
                        <td>{{ $assignment->employee?->nama ?? '-' }}</td>
                        <td>{{ $assignment->employee?->location?->name ?? $assignment->employee?->location?->nama ?? '-' }}</td>
                        <td>{{ $assignment->is_primary ? 'Ya' : 'Tidak' }}</td>
                        <td>{{ $assignment->start_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ $assignment->end_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            <form action="{{ route('jobdesks.assignments.destroy', [$jobdesk, $assignment]) }}" method="POST" onsubmit="return confirm('Hapus assignment ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Belum ada assignment.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $assignments->links() }}
    </div>
</div>
@endsection
