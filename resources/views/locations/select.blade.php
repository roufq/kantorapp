@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
  <div>
    <h3 class="mb-1">Pilih Lokasi Aktif</h3>
    <p class="text-muted mb-0">Super Admin dapat memilih lokasi untuk men-scope dashboard dan data.</p>
  </div>
  <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
 </div>

<div class="card">
  <div class="card-body">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('location-selection.store') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Lokasi</label>
        <select name="location_id" class="form-control" required>
          <option value="">Pilih lokasi</option>
          @foreach($locations as $loc)
            <option value="{{ $loc->id }}" @selected($current==$loc->id)>{{ $loc->name }} ({{ $loc->code }})</option>
          @endforeach
        </select>
        @error('location_id')
          <div class="text-danger small">{{ $message }}</div>
        @enderror
      </div>
      <button class="btn btn-primary">Set Lokasi</button>
    </form>
  </div>
</div>
@endsection
