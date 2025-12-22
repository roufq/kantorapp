@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border">
  <div>
    <h3 class="mb-1">Hari Libur</h3>
    <p class="text-muted mb-0">Kelola daftar hari libur nasional maupun lokal.</p>
  </div>
</div>
<div class="row">
  <div class="col-12 mb-3">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Tambah Holiday</h3>
        <form method="GET" action="{{ route('holidays.index') }}" class="d-flex gap-2">
          <select name="type" class="form-select form-select-sm" style="width:auto">
            <option value="">Jenis: Semua</option>
            <option value="national" @selected(request('type')==='national')>Nasional</option>
            <option value="local" @selected(request('type')==='local')>Lokal</option>
          </select>
          @if(auth()->user()->hasRole('Super Admin'))
          <select name="location_id" class="form-select form-select-sm" style="width:auto">
            <option value="">Lokasi: Semua</option>
            @foreach(\App\Models\Location::orderBy('name')->get() as $loc)
              <option value="{{ $loc->id }}" @selected(request('location_id')==$loc->id)>{{ $loc->name }}</option>
            @endforeach
          </select>
          @endif
          <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm" style="width:auto"/>
          <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm" style="width:auto"/>
          <button class="btn btn-sm btn-outline-primary" type="submit">Filter</button>
        </form>
      </div>
      <div class="card-body">
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        @if (session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <form method="POST" action="{{ route('holidays.store') }}" class="row g-2">
          @csrf
          <div class="col-md-4">
            <label class="form-label">Tanggal</label>
            <input type="date" name="date" class="form-control" required />
          </div>
          <div class="col-md-8">
            <label class="form-label">Nama Libur</label>
            <input type="text" name="name" class="form-control" placeholder="Contoh: Hari Kemerdekaan" required />
          </div>
          @if(auth()->user()->hasRole('Super Admin'))
          <div class="col-md-4">
            <div class="form-check mt-4">
              <input class="form-check-input" type="checkbox" name="is_national" id="is_national" />
              <label class="form-check-label" for="is_national">Libur Nasional</label>
            </div>
          </div>
          <div class="col-md-8">
            <label class="form-label">Location (Opsional untuk non-nasional)</label>
            <select name="location_id" id="location_id" class="form-select">
              <option value="">- none - (nasional)</option>
              @foreach(\App\Models\Location::orderBy('name')->get() as $loc)
                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
              @endforeach
            </select>
          </div>
          @else
            <div class="col-md-12 small text-muted">Admin Lokasi: Holiday akan tersimpan untuk lokasi Anda.</div>
          @endif
          <div class="col-12">
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Daftar Holiday</h3></div>
      <div class="card-body table-responsive">
        <table class="table table-sm table-bordered">
          <thead>
            <tr>
              <th style="width:50px">No</th><th>Tanggal</th><th>Nama</th><th>Jenis</th><th>Lokasi</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($holidays as $h)
            <tr>
              <td>{{ $loop->iteration + (method_exists($holidays,'currentPage') ? ($holidays->currentPage()-1)*$holidays->perPage() : 0) }}</td>
              <td>{{ $h->date->format('Y-m-d') }}</td>
              <td>{{ $h->name }}</td>
              <td>{{ $h->is_national ? 'Nasional' : 'Lokal' }}</td>
              <td>{{ $h->is_national ? 'Semua Lokasi' : (optional(\App\Models\Location::find($h->location_id))->name ?? '-') }}</td>
              <td>
                <form method="POST" action="{{ route('holidays.destroy', $h) }}" onsubmit="return confirm('Hapus holiday ini?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
              </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">Belum ada data</td></tr>
            @endforelse
          </tbody>
        </table>
        @if(method_exists($holidays, 'links'))
          <div class="mt-2">{{ $holidays->links() }}</div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function(){
    const national = document.getElementById('is_national');
    const loc = document.getElementById('location_id');
    if (national && loc) {
      function toggle(){
        if (national.checked) {
          loc.value = '';
          loc.setAttribute('disabled', 'disabled');
        } else {
          loc.removeAttribute('disabled');
        }
      }
      national.addEventListener('change', toggle);
      toggle();
    }
  });
  </script>
@endsection
