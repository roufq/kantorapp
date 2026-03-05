@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Detail Jobdesk</h3>
        <p class="text-muted mb-0">{{ $jobdesk->name }}</p>
    </div>
    <a href="{{ route('jobdesks.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <p><strong>Nama:</strong> {{ $jobdesk->name }}</p>
        <p><strong>Deskripsi:</strong> {{ $jobdesk->description ?? '-' }}</p>
        <p><strong>Role Scope:</strong> {{ $jobdesk->role_scope ?? '-' }}</p>
        <p><strong>Lokasi:</strong> {{ $jobdesk->location?->name ?? $jobdesk->location?->nama ?? 'Global' }}</p>
        <p><strong>Status:</strong> {{ $jobdesk->is_active ? 'Aktif' : 'Nonaktif' }}</p>
        <p><strong>Minimal Hadir (menit):</strong> {{ $jobdesk->min_attendance_minutes ?? '-' }}</p>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Task Catalog</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('jobdesks.assignments.index', $jobdesk) }}" class="btn btn-sm btn-outline-success">Assignments</a>
            <a href="{{ route('jobdesks.catalogs.index', $jobdesk) }}" class="btn btn-sm btn-outline-primary">Kelola Catalog</a>
        </div>
    </div>
    <div class="card-body">
        @if($jobdesk->taskCatalogs->isEmpty())
            <div class="text-muted">Belum ada task catalog.</div>
        @else
            <ul class="mb-0">
                @foreach($jobdesk->taskCatalogs as $catalog)
                    <li>{{ $catalog->name }} ({{ $catalog->unit }} {{ $catalog->value }})</li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
