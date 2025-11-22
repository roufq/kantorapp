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
        <div class="col-lg-4">
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
        <div class="col-lg-8">
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
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
