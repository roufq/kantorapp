@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Karyawan</h3>
        <p class="text-muted mb-0">Kelola data karyawan, jabatan, dan lokasi kerja.</p>
    </div>
    <a href="{{ route('karyawans.create') }}" class="btn btn-primary btn-sm">Tambah Karyawan</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Employees</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Jabatan</th>
                            <th>Divisi</th>
                            <th>Tgl Masuk</th>
                            <th>Lokasi</th>

                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                       
                            <tr>
                                <td>{{ $loop->iteration + ($employees->currentPage()-1)*$employees->perPage() }}</td>
                                <td>{{ $employee->nama }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->jabatan }}</td>
                                <td>{{ $employee->division->nama ?? 'N/A' }}</td>
                                <td>{{ $employee->tanggal_masuk_kerja ? $employee->tanggal_masuk_kerja->format('d M Y') : '-' }}</td>
                                <td>{{ $employee->location->name ?? 'N/A' }}</td>

                                <td>
                                    <a href="{{ route('karyawans.show', $employee) }}" class="btn btn-sm btn-outline-info">View</a>
                                    <a href="{{ route('karyawans.edit', $employee) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('karyawans.destroy', $employee) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
