@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Tambah User</h3>
        <p class="text-muted mb-0">Buat akun login untuk karyawan yang sudah terdaftar.</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add Employee</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="karyawan_id" class="form-label">Select Employee</label>
                        <select name="karyawan_id" class="form-control" id="karyawan_id" required>
                            <option value="">Select Employee</option>
                            @foreach($karyawans as $karyawan)
                                <option value="{{ $karyawan->id }}" data-name="{{ $karyawan->nama }}" data-email="{{ $karyawan->email }}">{{ $karyawan->nama }} - {{ $karyawan->email }}</option>
                            @endforeach
                        </select>
                        @error('karyawan_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" id="name" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="password" required>
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
                    </div>
                    <div class="mb-3">
                        <label for="location_id" class="form-label">Location</label>
                        @if(auth()->user()->hasRole('Admin Lokasi'))
                            <input type="text" class="form-control" value="{{ optional(auth()->user()->location)->name }}" disabled>
                            <input type="hidden" name="location_id" value="{{ auth()->user()->location_id }}">
                        @else
                            <select name="location_id" class="form-control" id="location_id">
                                <option value="">Select Location</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary">Create Employee</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                </form>

                <script>
                    document.getElementById('karyawan_id').addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        document.getElementById('name').value = selectedOption.getAttribute('data-name') || '';
                        document.getElementById('email').value = selectedOption.getAttribute('data-email') || '';
                    });
                </script>
            </div>
        </div>
    </div>
</div>
@endsection
