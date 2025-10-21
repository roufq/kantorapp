@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Employee Details</h3>
                <div class="card-tools">
                    <a href="{{ route('karyawans.index') }}" class="btn btn-sm btn-secondary">Back to List</a>
                    <a href="{{ route('karyawans.edit', $karyawan) }}" class="btn btn-sm btn-primary">Edit</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> {{ $karyawan->id }}</p>
                        <p><strong>Nama:</strong> {{ $karyawan->nama }}</p>
                        <p><strong>Email:</strong> {{ $karyawan->email }}</p>
                        <p><strong>Telepon:</strong> {{ $karyawan->telepon }}</p>
                        <p><strong>Jabatan:</strong> {{ $karyawan->jabatan }}</p>
                        <p><strong>Departemen:</strong> {{ $karyawan->departemen }}</p>
                        <p><strong>Tanggal Lahir:</strong> {{ $karyawan->tanggal_lahir ? $karyawan->tanggal_lahir->format('d M Y') : '-' }}</p>
                        <p><strong>Divisi:</strong> {{ $karyawan->division->nama }}</p>
                        <p><strong>Lokasi:</strong> {{ $karyawan->location->name ?? 'N/A' }}</p>

                        <p><strong>Super Admin:</strong> {{ $karyawan->master ? $karyawan->master->name : 'N/A' }}</p>
                        <p><strong>Created:</strong> {{ $karyawan->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Alamat:</strong></p>
                        <p>{{ $karyawan->alamat ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
