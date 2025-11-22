@extends('layouts.app')
@section('title')
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-6"><h3 class="mb-0">Weekly Offs</h3></div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-end">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Weekly Offs</li>
      </ol>
    </div>
  </div>
</div>
@endsection
@section('content')
<div class="row">
  <div class="col-md-5">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Tambah Weekly Off</h3>
        <form method="GET" action="{{ route('weekly-offs.index') }}" class="d-flex gap-2">
          <select name="day_of_week" class="form-select form-select-sm" style="width:auto">
            <option value="">Hari: Semua</option>
            @foreach(['sunday','monday','tuesday','wednesday','thursday','friday','saturday'] as $d)
              <option value="{{ $d }}" @selected(request('day_of_week')===$d)>{{ ucfirst($d) }}</option>
            @endforeach
          </select>
          <button class="btn btn-sm btn-outline-primary" type="submit">Filter</button>
        </form>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('weekly-offs.store') }}" class="row g-2">
          @csrf
          <div class="col-md-6">
            <label class="form-label">Hari</label>
            <select class="form-select" name="day_of_week" required>
              @foreach(['sunday','monday','tuesday','wednesday','thursday','friday','saturday'] as $d)
                <option value="{{ $d }}">{{ ucfirst($d) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Untuk User (opsional)</label>
            @php
              $usersQuery = \App\Models\User::orderBy('name');
              if (auth()->user()->hasRole('Admin Lokasi')) {
                $usersQuery->where('location_id', auth()->user()->location_id);
              }
              $allUsers = $usersQuery->get();
            @endphp
            <select class="form-select" name="user_id" id="weekly_off_user_id">
              <option value="">- semua user (lokasi) -</option>
              @foreach($allUsers as $u)
                <option value="{{ $u->id }}" data-user-location="{{ $u->location_id }}">{{ $u->name }}</option>
              @endforeach
            </select>
          </div>
          @if(auth()->user()->hasRole('Super Admin'))
          <div class="col-12">
            <label class="form-label">Lokasi (untuk off level lokasi)</label>
            <select class="form-select" name="location_id" id="weekly_off_location_id">
              <option value="">- none -</option>
              @foreach(\App\Models\Location::orderBy('name')->get() as $loc)
                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
              @endforeach
            </select>
          </div>
          @endif
          <div class="col-12">
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Daftar Weekly Off</h3></div>
      <div class="card-body table-responsive">
        <table class="table table-sm table-bordered">
          <thead>
            <tr>
              <th>Hari</th><th>User</th><th>Lokasi</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($offs as $o)
            <tr>
              <td>{{ ucfirst($o->day_of_week) }}</td>
              <td>{{ $o->user_id ? optional(\App\Models\User::find($o->user_id))->name : '-' }}</td>
              <td>{{ $o->location_id ? optional(\App\Models\Location::find($o->location_id))->name : (auth()->user()->hasRole('Admin Lokasi') ? 'Lokasi Saya' : '-') }}</td>
              <td>
                <form method="POST" action="{{ route('weekly-offs.destroy', $o) }}" onsubmit="return confirm('Hapus weekly off ini?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
              </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Belum ada data</td></tr>
            @endforelse
          </tbody>
        </table>
        @if(method_exists($offs, 'links'))
          <div class="mt-2">{{ $offs->links() }}</div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function(){
    const locSelect = document.getElementById('weekly_off_location_id');
    const userSelect = document.getElementById('weekly_off_user_id');
    if (!locSelect || !userSelect) return;
    const originalOptions = Array.from(userSelect.options);
    function filterUsers(){
      const locId = locSelect.value;
      // Preserve first option (placeholder)
      const placeholder = originalOptions[0].cloneNode(true);
      userSelect.innerHTML = '';
      userSelect.appendChild(placeholder);
      originalOptions.slice(1).forEach(opt => {
        const userLoc = opt.getAttribute('data-user-location');
        if (!locId || userLoc === locId) {
          userSelect.appendChild(opt.cloneNode(true));
        }
      });
      userSelect.value = '';
    }
    locSelect.addEventListener('change', filterUsers);
    // Initialize on load
    filterUsers();
  });
  </script>
@endsection
