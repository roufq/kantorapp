@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Buat Roster Mingguan</h1>
          <p class="text-muted mb-0">Pilih lokasi, shift lokasi, karyawan, dan pola off.</p>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shifts.rosters.index') }}">Rosters</a></li>
            <li class="breadcrumb-item active">Create</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        </div>
      @endif
      <div class="card">
        <div class="card-body">
          {{-- Form kecil GET untuk ganti lokasi tanpa memicu validasi POST --}}
          <form method="GET" action="{{ route('shifts.rosters.create') }}" class="mb-3">
            <div class="row g-3 align-items-end">
              <div class="col-md-4">
                <label class="form-label">Lokasi</label>
                <select name="location_id" class="form-control" onchange="this.form.submit()">
                  @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" @selected($locationId==$loc->id)>{{ $loc->name }} ({{ $loc->code }})</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Minggu (mulai)</label>
                <input type="date" name="week_start" class="form-control" value="{{ $weekStart->toDateString() }}">
              </div>
              <div class="col-md-4">
                <button class="btn btn-outline-primary" type="submit">Muat Lokasi</button>
              </div>
            </div>
          </form>

          @php
            $defaultLocationShiftId = old('location_shift_id') ?? ($locationShifts->first()->id ?? null);
            $oldUsers = collect(old('user_ids', []))->map(fn($v)=> (int)$v)->toArray();
            if (empty($oldUsers) && $users->count()) {
                $oldUsers = $users->pluck('id')->toArray(); // auto-select semua karyawan jika belum ada pilihan
            }
          @endphp
          <form method="POST" action="{{ route('shifts.rosters.store') }}">
            @csrf
            <input type="hidden" name="location_id" value="{{ $locationId }}">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Minggu</label>
                <div class="d-flex gap-2">
                  <input type="date" name="week_start" class="form-control" value="{{ $weekStart->toDateString() }}">
                  <input type="date" name="week_end" class="form-control" value="{{ $weekEnd->toDateString() }}">
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label">Weekly Off</label>
                <div class="d-flex gap-2">
                  <input type="number" min="1" name="weekly_off_every" class="form-control" value="{{ old('weekly_off_every', 6) }}">
                  <input type="text" name="weekly_off_label" class="form-control" value="{{ old('weekly_off_label','OFF') }}">
                </div>
                <small class="text-muted">Contoh: 6 berarti 1 hari off setiap 6 hari kerja.</small>
              </div>
            </div>

            <hr>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Shift (Location Shift)</label>
                <select name="location_shift_id" class="form-control" size="8" required>
                  @forelse($locationShifts as $ls)
                    <option value="{{ $ls->id }}" @selected($defaultLocationShiftId==$ls->id)>{{ $ls->shift->name ?? 'Shift' }} ({{ $ls->id }}) - {{ $ls->shift->category ?? '-' }}</option>
                  @empty
                    <option disabled>Belum ada shift di lokasi ini</option>
                  @endforelse
                </select>
                <small class="text-muted d-block mt-1">Hanya shift kategori Non Office (Factory) yang ditampilkan.</small>
              </div>
              <div class="col-md-6">
                <label class="form-label">Karyawan (lokasi ini)</label>
                <select name="user_ids[]" class="form-control" multiple size="8" required>
                  @forelse($users as $u)
                    <option value="{{ $u->id }}" @selected(in_array($u->id, $oldUsers))>{{ $u->name }} ({{ $u->email }})</option>
                  @empty
                    <option disabled>Belum ada karyawan di lokasi ini</option>
                  @endforelse
                </select>
                <small class="text-muted d-block mt-1">Jika belum dipilih, semua karyawan lokasi ini otomatis dipilih.</small>
              </div>
            </div>

            <div class="mt-3 d-flex justify-content-between">
              <a href="{{ route('shifts.rosters.index') }}" class="btn btn-outline-secondary">Kembali</a>
              <button class="btn btn-primary" type="submit"><i class="fas fa-magic me-1"></i> Generate Roster</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
