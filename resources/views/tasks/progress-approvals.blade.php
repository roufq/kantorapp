@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Approval Progress Tugas</h1>
            <p class="text-muted mb-0">Setujui/Reject tugas dan progres berbasis slot. Legacy progress tetap ditampilkan untuk backlog lama.</p>
        </div>
        <div>
            <a href="{{ route('tasks.index') }}" class="text-decoration-none">Kembali ke Tasks</a>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('tasks.progress.approvals') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Cari (judul / karyawan)</label>
                <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Cari judul atau nama karyawan">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Lokasi</label>
                <select name="location_id" class="form-select">
                    <option value="">Semua</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ (string)$locationId === (string)$loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Karyawan</label>
                <select name="assignee_id" class="form-select">
                    <option value="">Semua</option>
                    @foreach($assignees as $emp)
                        <option value="{{ $emp->id }}" {{ (string)$assigneeId === (string)$emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Apply</button>
                <a href="{{ route('tasks.progress.approvals') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Approval Tugas Baru (Self Assign)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Judul</th>
                        <th>Assignee</th>
                        <th>Dibuat oleh</th>
                        <th>Lokasi</th>
                        <th>Due</th>
                        <th>Durasi (menit)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pendingTaskCreations ?? [] as $task)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $task->title }}</td>
                        <td>{{ optional($task->assignee)->name }}</td>
                        <td>{{ optional($task->assigner)->name }}</td>
                        <td>{{ optional($task->assignee->location)->name ?? '-' }}</td>
                        <td>{{ optional($task->due_date)->format('d M Y') ?? '-' }}</td>
                        <td>{{ $task->duration_minutes ?? '-' }}</td>
                        <td class="d-flex gap-1 flex-wrap">
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                            <form action="{{ route('tasks.approvals.approve', $task) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectCreationModal{{ $task->id }}">Reject</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-muted text-center">Tidak ada tugas self assign yang menunggu persetujuan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(!empty($pendingTasks) && $pendingTasks->count() > 0)
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Pending Approval (Per Tugas)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Judul</th>
                        <th>Assignee</th>
                        <th>Lokasi</th>
                        <th>Due</th>
                        <th>Durasi (menit)</th>
                        <th>Slot Pending</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($pendingTasks as $task)
                    @php
                        $needsTaskApproval = $task->requires_approval && $task->approval_status === 'pending';
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ $task->title }}
                            @if($needsTaskApproval)
                                <span class="badge text-bg-warning ms-2">Butuh approval task</span>
                            @endif
                        </td>
                        <td>{{ optional($task->assignee)->name }}</td>
                        <td>{{ optional($task->assignee->location)->name ?? '-' }}</td>
                        <td>{{ optional($task->due_date)->format('d M Y') ?? '-' }}</td>
                        <td>{{ $task->duration_minutes ?? '-' }}</td>
                        <td>{{ $task->slots->count() }}</td>
                        <td class="d-flex gap-1 flex-wrap">
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                            <form action="{{ route('tasks.approve-slots', $task) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectTaskModal{{ $task->id }}">Reject</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Pending Approval (Legacy Progress)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Task</th>
                        <th>Assignee</th>
                        <th>Dikirim Oleh</th>
                        <th>Progress</th>
                        <th>Dikirim</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pendingUpdates as $update)
                    <tr>
                        <td>{{ $loop->iteration + ($pendingUpdates->currentPage()-1)*$pendingUpdates->perPage() }}</td>
                        <td>{{ $update->task->title }}</td>
                        <td>{{ optional($update->task->assignee)->name }}</td>
                        <td>{{ $update->user->name }}</td>
                        <td>{{ $update->progress }}%</td>
                        <td>{{ $update->created_at->format('d M Y H:i') }}</td>
                        <td class="d-flex gap-1 flex-wrap">
                            <a href="{{ route('tasks.show', $update->task) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                            <form action="{{ route('tasks.progress.approve', $update) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectLegacyModal{{ $update->id }}">Reject</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted text-center">Tidak ada progres yang menunggu persetujuan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($pendingUpdates->hasPages())
            <div class="p-3">
                {{ $pendingUpdates->links() }}
            </div>
        @endif
    </div>
</div>

@foreach($pendingTaskCreations ?? [] as $task)
<div class="modal fade" id="rejectCreationModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Alasan Reject</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('tasks.approvals.reject', $task) }}" method="POST">
        @csrf
        <div class="modal-body">
          <textarea name="reason" class="form-control" rows="3" required placeholder="Tuliskan alasan"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Reject</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

@foreach($pendingTasks ?? [] as $task)
<div class="modal fade" id="rejectTaskModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Alasan Reject Tugas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('tasks.reject-slots', $task) }}" method="POST">
        @csrf
        <div class="modal-body">
          <textarea name="reason" class="form-control" rows="3" required placeholder="Tuliskan alasan"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Reject</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

@foreach($pendingUpdates as $update)
<div class="modal fade" id="rejectLegacyModal{{ $update->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Alasan Reject Progress</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('tasks.progress.reject', $update) }}" method="POST">
        @csrf
        <div class="modal-body">
          <textarea name="reason" class="form-control" rows="3" required placeholder="Tuliskan alasan"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Reject</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

<style>
  /* Gunakan backdrop ringan agar konten belakang tetap terlihat */
  .modal-backdrop.show { background-color: rgba(0, 0, 0, 0.05); opacity: 1; }
  .modal-content { border-radius: 8px; }
</style>
@endsection
