@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Tambah Divisi</h1>
            <p class="text-muted mb-0">Buat data divisi baru agar struktur organisasi tetap rapi.</p>
        </div>
        <div>
            <a href="{{ route('divisions.index') }}" class="text-decoration-none">Kembali</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add Division</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('divisions.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" id="nama" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Division</button>
                    <a href="{{ route('divisions.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
