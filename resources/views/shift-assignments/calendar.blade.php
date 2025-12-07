@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1 class="h4 mb-1">Kalender Shift (Mingguan)</h1>
    <p class="text-muted mb-0">Lihat jadwal masuk per lokasi dalam satu minggu.</p>
  </div>
  <a href="{{ route('shift-assignments.index') }}" class="text-decoration-none">Kembali ke daftar</a>
</div>

<div class="card">
  <div class="card-body">
    <form method="GET" action="{{ route('shift-assignments.calendar') }}" class="row g-2 align-items-end">
      @if($locations->count() > 0)
        <div class="col-md-4">
          <label class="form-label">Lokasi</label>
          <select name="location_id" class="form-control" onchange="this.form.submit()">
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" @selected($loc->id == $locationId)>{{ $loc->name }} ({{ $loc->code }})</option>
            @endforeach
          </select>
        </div>
      @endif
      <div class="col-md-4">
        <label class="form-label">Mulai Minggu</label>
        <input type="date" name="week_start" class="form-control" value="{{ $weekStart->toDateString() }}" onchange="this.form.submit()">
      </div>
    </form>

    <div class="table-responsive mt-3">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th style="width:140px">Tanggal</th>
            <th>Jadwal & Karyawan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($weekData as $date => $rows)
            <tr>
              <td class="fw-semibold">{{ \Carbon\Carbon::parse($date)->format('D, d M') }}</td>
              <td>
                @if($rows->isEmpty())
                  <span class="text-muted">Tidak ada jadwal.</span>
                @else
                  <div class="d-flex flex-column gap-1">
                    @foreach($rows as $row)
                      <div class="border rounded p-2 bg-light">
                        <div class="d-flex justify-content-between flex-wrap">
                          <div>
                            @if($row['status'] === 'missing')
                              <div class="fw-semibold text-danger">{{ $row['time'] }}</div>
                            @else
                              <div class="fw-semibold">{{ $row['user']->name ?? '-' }}</div>
                              <div class="small text-muted">{{ $row['time'] }}</div>
                            @endif
                          </div>
                          <div>
                            @if($row['status'] === 'off')
                              <span class="badge bg-secondary">OFF</span>
                            @elseif($row['status'] === 'leave')
                              <span class="badge bg-danger">Leave</span>
                            @elseif($row['status'] === 'missing')
                              <span class="badge bg-warning text-dark">Belum ada jadwal</span>
                            @else
                              <span class="badge bg-success text-uppercase">{{ $row['status'] ?? 'scheduled' }}</span>
                            @endif
                          </div>
                        </div>
                        @if(!empty($row['notes']))
                          <div class="small mt-1">{{ $row['notes'] }}</div>
                        @endif
                      </div>
                    @endforeach
                  </div>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="2" class="text-center text-muted">Tidak ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
