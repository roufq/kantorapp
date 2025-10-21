@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add Super Admin</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('masters.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="karyawan_id" class="form-label">Employee</label>
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
                    <button type="submit" class="btn btn-primary">Create Super Admin</button>
                    <a href="{{ route('masters.index') }}" class="btn btn-secondary">Cancel</a>
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
