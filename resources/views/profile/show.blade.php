@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div>
        <h1 class="m-0">Profil Saya</h1>
        <p class="text-secondary mb-0">Detail akun dan informasi lokasi</p>
      </div>
    </div>
  </div>

      <div class="content">
        <div class="container-fluid">
          <div class="row g-3">
        <div class="col-12">
          <div class="card text-center">
            <div class="card-body">
              @php
                $avatarPath = $user->profile_photo_path;
                $avatarUrl = asset('assets/img/user2-160x160.jpg');
                if ($avatarPath) {
                  $normalized = str_replace('\\','/',$avatarPath);
                  if (\Illuminate\Support\Facades\Storage::disk('public')->exists($normalized)) {
                    $avatarUrl = asset('storage/' . ltrim($normalized, '/'));
                  }
                }
              @endphp
              <div class="mb-3">
                <img src="{{ $avatarUrl }}" class="rounded-circle shadow" alt="User avatar" width="120" height="120">
              </div>
              <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
              <div class="text-secondary mb-2">{{ $roles->implode(', ') }}</div>
              <div class="small text-secondary">Member since {{ $user->created_at->format('M. Y') }}</div>
              <div class="mt-3">
                @if(session('success'))
                  <div class="alert alert-success py-2">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                  <div class="alert alert-danger py-2">
                    <ul class="mb-0">
                      @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif
                <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="mb-2">
                    <input type="file" name="photo" class="form-control" required>
                  </div>
                  <button class="btn btn-primary w-100" type="submit">Update Foto</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">Informasi Akun</h5>
            </div>
            <div class="card-body">
              <dl class="row mb-0">
                <dt class="col-sm-4">Nama</dt>
                <dd class="col-sm-8">{{ $user->name }}</dd>

                <dt class="col-sm-4">Email</dt>
                <dd class="col-sm-8">{{ $user->email }}</dd>

                <dt class="col-sm-4">Role</dt>
                <dd class="col-sm-8">{{ $roles->implode(', ') }}</dd>

                <dt class="col-sm-4">Lokasi</dt>
                <dd class="col-sm-8">{{ $user->location?->name ?? '-' }}</dd>

                <dt class="col-sm-4">Telepon</dt>
                <dd class="col-sm-8">{{ $user->phone_number ?? '-' }}</dd>

                <dt class="col-sm-4">Karyawan</dt>
                <dd class="col-sm-8">{{ $user->karyawan?->nama ?? '-' }}</dd>
              </dl>
            </div>
          </div>

          <div class="card mt-3">
            <div class="card-header">
              <h5 class="mb-0">Ubah Password</h5>
            </div>
            <div class="card-body">
              <form action="{{ route('profile.password.update') }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label for="current_password" class="form-label">Password Saat Ini</label>
                  <input type="password" name="current_password" id="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label">Password Baru</label>
                  <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                  <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Password</button>
              </form>
            </div>
          </div>

          <div class="card mt-3">
            <div class="card-header">
              <h5 class="mb-0">Sesi Browser</h5>
            </div>
            <div class="card-body">
              <p class="text-secondary">Kelola dan logout dari sesi aktif Anda di browser dan perangkat lain.</p>
              @if (count($sessions) > 0)
                <div class="list-group list-group-flush">
                  @foreach ($sessions as $session)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                      <div>
                        <div class="fw-bold">
                          {{ $session->ip_address }}
                        </div>
                        <div class="text-secondary" style="font-size: 0.9rem; max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                          {{ $session->user_agent }}
                        </div>
                        <div class="text-secondary">
                          Last active: {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                          @if ($session->id === request()->session()->getId())
                            <span class="badge bg-success ms-2">Sesi ini</span>
                          @endif
                        </div>
                      </div>
                      @if ($session->id !== request()->session()->getId())
                        <form action="{{ route('profile.session.logout', $session->id) }}" method="POST">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
                        </form>
                      @endif
                    </div>
                  @endforeach
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
