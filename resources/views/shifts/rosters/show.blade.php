@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Detail Roster</h1>
          <p class="text-muted mb-0">{{ $roster->location->name ?? '-' }} | {{ $roster->locationShift->shift->name ?? 'Shift' }}</p>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shifts.rosters.index') }}">Rosters</a></li>
            <li class="breadcrumb-item active">Detail</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Minggu {{ $roster->week_start->toDateString() }} s/d {{ $roster->week_end->toDateString() }}</h3>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-bordered align-middle">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Slot</th>
                <th>Jam</th>
                <th>Karyawan</th>
                <th>Status</th>
                <th>Catatan</th>
              </tr>
            </thead>
            <tbody>
              @forelse($roster->entries->sortBy(['date','slot_index']) as $e)
                <tr>
                  <td>{{ $e->date->toDateString() }}</td>
                  <td>
                    @if($e->status === 'off')
                      <span class="text-muted">OFF</span>
                    @else
                      {{ $e->slot_index + 1 }}
                    @endif
                  </td>
                  <td>
                    @if($e->status === 'off')
                      <span class="text-muted">Hari libur</span>
                    @elseif(isset($slotMap[$e->slot_index]))
                      {{ $slotMap[$e->slot_index]['start'] ?? '?' }} - {{ $slotMap[$e->slot_index]['end'] ?? '?' }}
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>{{ $e->user->name ?? '-' }}</td>
                  <td>
                    @if($e->status === 'off')
                      <span class="badge badge-secondary">OFF</span>
                    @else
                      <span class="badge badge-success">Scheduled</span>
                    @endif
                  </td>
                  <td>{{ $e->notes }}</td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center text-muted">Belum ada entri untuk roster ini.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="card-footer d-flex justify-content-between">
          <a href="{{ route('shifts.rosters.index') }}" class="btn btn-outline-secondary">Kembali</a>
          <a href="{{ route('shifts.rosters.edit', $roster) }}" class="btn btn-info">Edit / Rolling</a>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
