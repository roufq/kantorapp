@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Tambah Karyawan</h3>
        <p class="text-muted mb-0">Isi data karyawan baru untuk keperluan HR dan penugasan.</p>
    </div>
    <a href="{{ route('karyawans.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add Employee</h3>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('karyawans.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" id="nama" value="{{ old('nama') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telepon" class="form-label">Telepon</label>
                            <input type="text" name="telepon" class="form-control" id="telepon" value="{{ old('telepon') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" id="jabatan" value="{{ old('jabatan') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="departemen" class="form-label">Departemen</label>
                            <input type="text" name="departemen" class="form-control" id="departemen" value="{{ old('departemen') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_masuk_kerja" class="form-label">Tanggal Masuk Kerja</label>
                            <input type="date" name="tanggal_masuk_kerja" class="form-control" id="tanggal_masuk_kerja" value="{{ old('tanggal_masuk_kerja') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="divisi_id" class="form-label">Divisi</label>
                        <select name="divisi_id" class="form-control" id="divisi_id" required>
                            <option value="">Pilih Divisi</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ old('divisi_id') == $division->id ? 'selected' : '' }}>{{ $division->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="location_id" class="form-label">Lokasi</label>
                            @if(auth()->user()->hasRole('Admin Lokasi'))
                                <input type="text" class="form-control" value="{{ optional(auth()->user()->location)->name }}" disabled>
                                <input type="hidden" name="location_id" value="{{ auth()->user()->location_id }}">
                            @else
                                <select name="location_id" class="form-control" id="location_id" required>
                                    <option value="">Pilih Lokasi</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" id="alamat" rows="3">{{ old('alamat') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Employee</button>
                    <a href="{{ route('karyawans.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
