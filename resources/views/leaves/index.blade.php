@extends('layouts.appnew')
@section('title')
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-6"><h3 class="mb-0">Leaves</h3></div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-end">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Leaves</li>
      </ol>
    </div>
  </div>
</div>
@endsection
@section('content')
<div class="row">
  <div class="col-md-5">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Ajukan Izin/Cuti</h3></div>
      <div class="card-body">
        <form method="POST" action="{{ route('leaves.store') }}" class="row g-2">
          @csrf
          <div class="col-md-6">
            <label class="form-label">Mulai</label>
            <input type="date" name="start_date" class="form-control" required />
          </div>
          <div class="col-md-6">
            <label class="form-label">Selesai</label>
            <input type="date" name="end_date" class="form-control" required />
          </div>
          <div class="col-md-6">
            <label class="form-label">Tipe</label>
            <select name="type" class="form-select" required>
              <option value="sick">Sakit</option>
              <option value="annual">Cuti Tahunan</option>
              <option value="unpaid">Unpaid</option>
              <option value="other">Lainnya</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Alasan (opsional)</label>
            <input type="text" name="reason" class="form-control" />
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary">Kirim</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Daftar Izin/Cuti</h3></div>
      <div class="card-body table-responsive">
        <table class="table table-sm table-bordered">
          <thead>
            <tr>
              <th style="width:50px">No</th><th>Karyawan</th><th>Rentang</th><th>Tipe</th><th>Status</th><th>Alasan</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($leaves as $lv)
            <tr>
              <td>{{ $loop->iteration + ($leaves->currentPage()-1)*$leaves->perPage() }}</td>
              <td>{{ optional($lv->user)->name }}</td>
              <td>{{ $lv->start_date->format('Y-m-d') }} s/d {{ $lv->end_date->format('Y-m-d') }}</td>
              <td>{{ ucfirst($lv->type) }}</td>
              <td>
                @switch($lv->status)
                  @case('pending') <span class="badge text-bg-warning">Pending</span> @break
                  @case('approved') <span class="badge text-bg-success">Approved</span> @break
                  @case('rejected') <span class="badge text-bg-danger">Rejected</span> @break
                @endswitch
              </td>
              <td>{{ $lv->reason }}</td>
              <td>
                @if(auth()->user()->hasRole('Super Admin') || (auth()->user()->hasRole('Admin Lokasi') && auth()->user()->location_id === $lv->location_id))
                  <div class="d-flex gap-1">
                    <form method="POST" action="{{ route('leaves.updateStatus', $lv) }}">
                      @csrf @method('PATCH')
                      <input type="hidden" name="status" value="approved" />
                      <button class="btn btn-sm btn-success">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('leaves.updateStatus', $lv) }}">
                      @csrf @method('PATCH')
                      <input type="hidden" name="status" value="rejected" />
                      <button class="btn btn-sm btn-danger">Reject</button>
                    </form>
                  </div>
                @endif
              </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">Belum ada data</td></tr>
            @endforelse
          </tbody>
        </table>
        {{ $leaves->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
