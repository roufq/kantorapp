@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Edit Laporan</h1>
            <p class="text-muted mb-0">Perbarui isi laporan sebelum dikirim atau direview.</p>
        </div>
        <div>
            <a href="{{ route('reports.index') }}" class="text-decoration-none">Kembali</a>
        </div>
    </div>
</div>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div>
        <h1 class="m-0">Edit Laporan</h1>
        <p class="text-secondary mb-0">Tiket: {{ $report->ticket_number }}</p>
      </div>
      <a href="{{ route('reports.show', $report) }}" class="btn btn-link">Kembali</a>
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
          <form action="{{ route('reports.update', $report) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <label class="form-label">Lokasi</label>
              <input type="text" class="form-control" value="{{ $report->location?->name ?? '-' }}" disabled>
            </div>

            <div class="mb-3">
              <label class="form-label">Judul</label>
              <input type="text" name="title" value="{{ old('title', $report->title) }}" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Deskripsi</label>
              <textarea name="description" rows="4" class="form-control" required>{{ old('description', $report->description) }}</textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Tambah Lampiran (opsional, 10 MB/file)</label>
              <input type="file" name="attachments[]" class="form-control" multiple>
              @if($report->attachments->isNotEmpty())
                <div class="text-secondary small mt-2">Lampiran yang sudah ada tetap tersimpan.</div>
              @endif
            </div>

            <div class="d-flex gap-2">
              <a href="{{ route('reports.show', $report) }}" class="btn btn-light">Batal</a>
              <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
