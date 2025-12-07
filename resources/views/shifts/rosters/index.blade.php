@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Weekly Rosters</h1>
          <p class="text-muted mb-0">Daftar roster mingguan per lokasi (factory/non-office).</p>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Rosters</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title">Roster Per Lokasi</h3>
          <a href="{{ route('shifts.rosters.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> Buat Roster</a>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Lokasi</th>
                <th>Shift</th>
                <th>Minggu</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($rosters as $r)
              <tr>
                <td>{{ $r->location->name ?? '-' }}</td>
                <td>{{ $r->locationShift->shift->name ?? 'Shift' }}</td>
                <td>{{ $r->week_start->toDateString() }} s/d {{ $r->week_end->toDateString() }}</td>
                <td>{!! $r->locked ? '<span class="badge badge-secondary">Locked</span>' : '<span class="badge badge-success">Active</span>' !!}</td>
                <td class="d-flex gap-2">
                  <a href="{{ route('shifts.rosters.show', $r) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                  <a href="{{ route('shifts.rosters.edit', $r) }}" class="btn btn-sm btn-outline-info">Edit / Rolling</a>
                  <form action="{{ route('shifts.rosters.destroy', $r) }}" method="POST" onsubmit="return confirm('Hapus roster ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                  </form>
                </td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center">Belum ada roster.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="card-footer">
          {{ $rosters->links() }}
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
