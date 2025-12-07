@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Shift Scheduler (Non-Office)</h1>
          <p class="text-muted mb-0">Buat jadwal cepat untuk karyawan non-office, termasuk libur mingguan bergilir.</p>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shifts.scheduler') }}">Shifts</a></li>
            <li class="breadcrumb-item active">Scheduler</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Generator Jadwal Non-Office</h3>
        </div>
        <form method="POST" action="{{ route('shifts.scheduler.generate') }}">
          @csrf
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Pilih Lokasi</label>
                <select name="location_id" class="form-control" onchange="this.form.submit()">
                  @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" @selected($locationId==$loc->id)>{{ $loc->name }} ({{ $loc->code }})</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Rentang Tanggal</label>
                <div class="d-flex gap-2">
                  <input type="date" name="date_start" class="form-control" value="{{ old('date_start') ?? now()->toDateString() }}">
                  <input type="date" name="date_end" class="form-control" value="{{ old('date_end') ?? now()->addWeek()->toDateString() }}">
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label">Weekly Off (opsional)</label>
                <div class="d-flex gap-2">
                  <input type="number" min="0" name="weekly_off_every" class="form-control" placeholder="Setiap n hari" value="{{ old('weekly_off_every', 7) }}">
                  <input type="text" name="weekly_off_label" class="form-control" placeholder="Label OFF" value="{{ old('weekly_off_label','OFF') }}">
                </div>
                <small class="text-muted">0 = tidak ada libur otomatis. Contoh: 6 → 1 hari off setiap 6 hari.</small>
              </div>
            </div>

            <hr>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Pilih Karyawan (lokasi ini)</label>
                <select name="user_ids[]" class="form-control" multiple size="8">
                  @forelse($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                  @empty
                    <option disabled>Belum ada karyawan di lokasi ini</option>
                  @endforelse
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Pilih Shift (Location Shift)</label>
                <select name="location_shift_id" class="form-control" size="8">
                  @forelse($locationShifts as $ls)
                    <option value="{{ $ls->id }}">
                      {{ $ls->shift->name ?? 'Shift' }} ({{ $ls->id }}) - {{ $ls->shift->category ?? '-' }}
                    </option>
                  @empty
                    <option disabled>Belum ada shift terhubung ke lokasi ini</option>
                  @endforelse
                </select>
                <small class="text-muted d-block mt-2">Shift diambil dari Location Shifts agar slot/jam sudah sesuai lokasi.</small>
              </div>
            </div>
          </div>
          <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('shifts.rosters.index') }}" class="btn btn-outline-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-magic me-1"></i> Generate Jadwal
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
@endsection
