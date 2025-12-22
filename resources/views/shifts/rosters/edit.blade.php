@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">
      <div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
          <h3 class="mb-1">Edit / Rolling Roster</h3>
          <p class="text-muted mb-0">{{ $roster->location->name ?? '-' }} | {{ $roster->locationShift->shift->name ?? 'Shift' }}</p>
        </div>
        <a href="{{ route('shifts.rosters.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
      </div>
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        </div>
      @endif

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Tukar Karyawan per Tanggal & Slot</h3>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('shifts.rosters.update', $roster) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Tanggal</label>
                <input type="date" name="swap_date" class="form-control" value="{{ $roster->week_start->toDateString() }}">
              </div>
              <div class="col-md-4">
                <label class="form-label">Karyawan A</label>
                <select name="swap_user_a" class="form-select">
                  @foreach($rosterUsers as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Karyawan B</label>
                <select name="swap_user_b" class="form-select">
                  @foreach($rosterUsers as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mt-3 d-flex justify-content-between">
              <a href="{{ route('shifts.rosters.show', $roster) }}" class="btn btn-outline-secondary">Kembali</a>
              <button type="submit" class="btn btn-primary">Tukar Karyawan</button>
            </div>
          </form>

          <hr>
          <h5>Roster Minggu Ini</h5>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th style="width:50px">No</th>
                  <th>Tanggal</th>
                  <th>Slot</th>
                  <th>Jam</th>
                  <th>Karyawan</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($roster->entries->sortBy(['date','slot_index']) as $e)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $e->date->toDateString() }}</td>
                    <td>{{ $e->slot_index + 1 }}</td>
                    <td>
                      @if(isset($slotMap[$e->slot_index]))
                        {{ $slotMap[$e->slot_index]['start'] ?? '?' }} - {{ $slotMap[$e->slot_index]['end'] ?? '?' }}
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                    <td>{{ $e->user->name ?? '-' }}</td>
                    <td>{{ $e->status }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
