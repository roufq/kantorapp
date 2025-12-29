@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
          <h3 class="mb-1">Laporan</h3>
          <p class="text-muted mb-0">Pantau laporan karyawan dan approval</p>
        </div>
        <div class="d-flex gap-2">
          @if(auth()->user()->hasRole(['Super Admin','Admin Lokasi','Karyawan']))
          <a href="{{ route('reports.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Buat Laporan
          </a>
          @endif
          @if(auth()->user()->hasRole(['Super Admin','Admin Lokasi','Karyawan']))
          <a href="{{ route('reports.employee-performance') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-graph-up me-1"></i>Performa Karyawan
          </a>
          @endif
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <form method="GET" class="row g-2 mb-3">
            <div class="col-md-5">
              <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari tiket / judul / deskripsi">
            </div>
            <div class="col-md-3">
              <select name="status" class="form-select">
                <option value="">Semua Status</option>
                @foreach(['pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $key=>$label)
                  <option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
              <button class="btn btn-secondary" type="submit"><i class="bi bi-search me-1"></i>Filter</button>
              <a href="{{ route('reports.index') }}" class="btn btn-light">Reset</a>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-striped align-middle">
              <thead>
                <tr>
                  <th style="width:50px">No</th>
                  <th>Tiket</th>
                  <th>Judul</th>
                  <th>Pelapor</th>
                  <th>Lokasi</th>
                  <th>Status</th>
                  <th>Dibuat</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($reports as $report)
                  <tr>
                    <td>{{ $loop->iteration + ($reports->currentPage()-1)*$reports->perPage() }}</td>
                    <td class="fw-semibold">{{ $report->ticket_number }}</td>
                    <td>{{ $report->title }}</td>
                    <td>{{ $report->reporter?->name ?? '-' }}</td>
                    <td>{{ $report->location?->name ?? '-' }}</td>
                    <td>
                      <span class="badge text-bg-{{ $report->status === 'approved' ? 'success' : ($report->status === 'rejected' ? 'danger' : 'warning') }}">
                        {{ ucfirst($report->status) }}
                      </span>
                    </td>
                    <td>{{ $report->created_at->format('d M Y H:i') }}</td>
                    <td class="text-end">
                      <a class="btn btn-sm btn-outline-primary" href="{{ route('reports.show', $report) }}">Lihat</a>
                      @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi'))
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('reports.edit', $report) }}">Edit</a>
                        <form action="{{ route('reports.destroy', $report) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus laporan ini?')">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                        </form>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center text-secondary">Belum ada laporan.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div>
            {{ $reports->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
