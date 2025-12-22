@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
          <h3 class="mb-1">{{ $report->title }}</h3>
          <p class="text-muted mb-0">Tiket: {{ $report->ticket_number }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
          @if(auth()->user()->hasRole(['Super Admin','Admin Lokasi','Karyawan']))
          <a href="{{ route('reports.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Buat Laporan</a>
          @endif
          @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi'))
          <a href="{{ route('reports.edit', $report) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
          <form action="{{ route('reports.destroy', $report) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus laporan ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
          </form>
          @endif
        </div>
      </div>
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="row g-3">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                  <div class="text-secondary small">Status</div>
                  <span class="badge text-bg-{{ $report->status === 'approved' ? 'success' : ($report->status === 'rejected' ? 'danger' : 'warning') }}">
                    {{ ucfirst($report->status) }}
                  </span>
                </div>
                <div class="text-end small text-secondary">
                  Dibuat: {{ $report->created_at->format('d M Y H:i') }}<br>
                  @if($report->finalized_at)
                    Final: {{ $report->finalized_at->format('d M Y H:i') }}
                  @endif
                </div>
              </div>

              <div class="mb-3">
                <div class="text-secondary small">Pelapor</div>
                <div>{{ $report->reporter?->name }} ({{ $report->reporter?->getRoleNames()->first() }})</div>
              </div>
              <div class="mb-3">
                <div class="text-secondary small">Lokasi</div>
                <div>{{ $report->location?->name ?? '-' }}</div>
              </div>
              <div class="mb-3">
                <div class="text-secondary small">Admin Lokasi Assigned</div>
                <div>{{ $report->assignedAdmin?->name ?? 'Tidak ada / langsung ke Super Admin' }}</div>
              </div>

              <div class="mb-3">
                <div class="text-secondary small">Deskripsi</div>
                <p class="mb-0">{{ $report->description }}</p>
              </div>

              <div class="mb-3">
                <div class="text-secondary small mb-1">Lampiran</div>
                @if($report->attachments->isEmpty())
                  <div class="text-secondary">Tidak ada lampiran.</div>
                @else
                  <div class="row g-2">
                    @foreach($report->attachments as $att)
                      @php
                        $normalized = ltrim(str_replace('\\','/',$att->file_path), '/');
                        $url = asset('storage/' . $normalized);
                        $isImage = \Illuminate\Support\Str::startsWith($att->mime_type ?? '', 'image/');
                      @endphp
                      <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                          <div>
                            <i class="bi bi-paperclip me-1"></i>{{ $att->original_name }}
                            <span class="text-secondary small ms-2">{{ number_format($att->file_size / 1024, 1) }} KB</span>
                          </div>
                          <a class="btn btn-sm btn-outline-primary" href="{{ route('reports.attachments.download', $att) }}"><i class="bi bi-download"></i></a>
                        </div>
                        @if($isImage)
                          <div class="mb-3">
                            <img src="{{ $url }}" alt="{{ $att->original_name }}" class="img-fluid rounded shadow-sm">
                          </div>
                        @endif
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>

              @if($pendingApproval && auth()->user()->hasRole('Admin Lokasi') && $pendingApproval->approver_role === 'admin_lokasi')
                @include('reports.partials._approval_form')
              @elseif($pendingApproval && auth()->user()->hasRole('Super Admin') && $pendingApproval->approver_role === 'super_admin')
                @include('reports.partials._approval_form')
              @endif
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h6 class="mb-0">Log Approval</h6>
            </div>
            <div class="card-body">
              <div class="timeline">
                @foreach($report->approvals->sortBy('step_order') as $appr)
                  <div class="timeline-item mb-3">
                    <div class="d-flex justify-content-between">
                      <strong>{{ strtoupper($appr->approver_role) }}</strong>
                      <span class="badge text-bg-{{ $appr->status === 'approved' ? 'success' : ($appr->status === 'rejected' ? 'danger' : 'secondary') }}">
                        {{ ucfirst($appr->status) }}
                      </span>
                    </div>
                    <div class="small text-secondary">
                      Approver: {{ $appr->approver?->name ?? '-' }}<br>
                      @if($appr->decided_at) {{ $appr->decided_at->format('d M Y H:i') }} @endif
                    </div>
                    @if($appr->notes)
                      <div class="mt-1">{{ $appr->notes }}</div>
                    @endif
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
