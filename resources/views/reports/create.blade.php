@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div>
        <h1 class="m-0">Buat Laporan</h1>
        <p class="text-secondary mb-0">Karyawan melapor ke admin lokasi, lanjut ke Super Admin</p>
      </div>
      <a href="{{ route('reports.index') }}" class="btn btn-link">Kembali</a>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="card">
        <div class="card-body">
          <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(auth()->user()->hasRole('Super Admin'))
              <div class="mb-3">
                <label class="form-label">Lokasi</label>
                <select name="location_id" class="form-select" required>
                  <option value="">Pilih lokasi</option>
                  @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" @selected(old('location_id')==$loc->id)>{{ $loc->name }}</option>
                  @endforeach
                </select>
              </div>
            @else
              <div class="mb-3">
                <label class="form-label">Lokasi</label>
                <input type="text" class="form-control" value="{{ auth()->user()->location?->name ?? '-' }}" disabled>
              </div>
            @endif

            <div class="mb-3">
              <label class="form-label">Judul</label>
              <input type="text" name="title" value="{{ old('title') }}" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Deskripsi</label>
              <textarea name="description" rows="4" class="form-control" required>{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Lampiran (boleh banyak, maks 10 MB/file)</label>
              <input type="file" name="attachments[]" class="form-control" multiple>
            </div>

            <div class="d-flex gap-2">
              <a href="{{ route('reports.index') }}" class="btn btn-light">Batal</a>
              <button type="submit" class="btn btn-primary">Kirim Laporan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
