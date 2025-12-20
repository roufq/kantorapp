@extends('layouts.appnew')
@section('title')
<div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Tasks</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Tasks</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Tasks</h1>
    <div>
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi'))
            <a href="{{ route('tasks.create') }}" class="btn btn-primary me-2">Assign Task</a>
            <a href="{{ route('tasks.progress.approvals') }}" class="btn btn-warning me-2">Approval Progress</a>
        @endif
        <a href="{{ route('tasks.create.self') }}" class="btn btn-outline-primary">Create Task for Myself</a>
    </div>
</div>

<!-- Search & Filters -->
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('tasks.index') }}">
      <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <div class="flex-grow-1">
          <label class="form-label fw-semibold d-block">Pencarian Task</label>
          <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari task berdasarkan judul, deskripsi, atau nama assignee" value="{{ request('search') }}">
            <div class="input-group-append d-flex gap-2">
              <button type="submit" class="btn btn-primary waves-effect waves-light">Apply</button>
              <button type="button" class="btn btn-outline-secondary waves-effect collapsed" data-toggle="collapse" data-target="#taskFiltersCollapse" aria-expanded="false" aria-controls="taskFiltersCollapse">
                <i class="mdi mdi-tune"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="collapse" id="taskFiltersCollapse">
        <div class="row g-3">
          <div class="col-md-4 col-lg-3">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-control">
              <option value="">Semua</option>
              <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
              <option value="in_progress" {{ request('status')=='in_progress' ? 'selected' : '' }}>In Progress</option>
              <option value="completed" {{ request('status')=='completed' ? 'selected' : '' }}>Completed</option>
            </select>
          </div>
          <div class="col-md-4 col-lg-3">
            <label class="form-label fw-semibold">Tanggal Dibuat</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
          </div>
          <div class="col-md-4 col-lg-3">
            <label class="form-label fw-semibold">Due Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
          </div>
          @if(auth()->user()->hasRole('Super Admin'))
          <div class="col-md-4 col-lg-3">
            <label class="form-label fw-semibold">Role</label>
            <select name="assignee_role" class="form-control">
              <option value="">Semua</option>
              <option value="Karyawan" {{ request('assignee_role')=='Karyawan' ? 'selected' : '' }}>Karyawan</option>
              <option value="Admin Lokasi" {{ request('assignee_role')=='Admin Lokasi' ? 'selected' : '' }}>Admin Lokasi</option>
            </select>
          </div>
          <div class="col-md-4 col-lg-3">
            <label class="form-label fw-semibold">Location</label>
            <select name="location_id" class="form-control">
              <option value="">Semua</option>
              @foreach(\App\Models\Location::orderBy('name')->get() as $loc)
                <option value="{{ $loc->id }}" {{ (string)request('location_id')===(string)$loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
              @endforeach
            </select>
          </div>
          @endif
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3">
          <button type="submit" class="btn btn-primary waves-effect waves-light">Apply</button>
          <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary waves-effect">Clear</a>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="row">
    @foreach($tasks as $task)
        @php
            $me = auth()->user();
            $needsApproval = ($me->hasAnyRole(['Super Admin','Admin Lokasi']) && ($task->pending_slots_count ?? 0) > 0);
            $requiresCreationApproval = $task->requires_approval ?? false;
            $creationApproved = !$requiresCreationApproval || $task->approval_status === 'approved';
            $approvalLabel = null;
            if ($requiresCreationApproval) {
                if ($task->approval_status === 'pending') {
                    $approvalLabel = 'Menunggu persetujuan ' . ($task->approval_level === 'location_admin' ? 'Admin Lokasi' : 'Super Admin');
                } elseif ($task->approval_status === 'rejected') {
                    $approvalLabel = 'Ditolak' . ($task->approval_note ? ': ' . $task->approval_note : '');
                }
            }
            $canUpdateProgress = $creationApproved && (
                $me->hasRole('Super Admin') ||
                $task->assigned_to === $me->id ||
                ($me->hasRole('Admin Lokasi') && optional($task->assignee)->location_id === $me->location_id)
            );
        @endphp
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <h5 class="card-title mb-1">
                                <a href="{{ route('tasks.show', $task) }}" class="text-dark">{{ $task->title }}</a>
                            </h5>
                            <small class="text-muted d-block">
                                Dibuat: {{ optional($task->created_at)->format('d M Y') }} ·
                                Due: {{ $task->due_date ? $task->due_date->format('d M Y') : 'No due date' }}
                            </small>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-1">
                            <span class="badge badge-light border text-capitalize">{{ $task->status }}</span>
                            @if($needsApproval)
                                <span class="badge badge-warning">Butuh approval ({{ $task->pending_slots_count }})</span>
                            @endif
                            @if($approvalLabel)
                                <span class="badge badge-{{ $task->approval_status === 'rejected' ? 'danger' : 'warning' }}">{{ $approvalLabel }}</span>
                            @endif
                        </div>
                    </div>
                    <p class="text-muted mb-2" style="min-height: 48px;">{{ \Illuminate\Support\Str::limit($task->description, 120) }}</p>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Progress</span>
                            <span>{{ $task->progress ?? 0 }}%</span>
                        </div>
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $task->progress ?? 0 }}%;" aria-valuenow="{{ $task->progress ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="mt-auto pt-2 small text-muted">
                        <div><strong>Assigned by:</strong> {{ $task->assigner->name }}</div>
                        <div><strong>Assigned to:</strong> {{ optional($task->assignee)->name ?? '-' }}</div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    @php
                        $isOwnRejected = $task->assigned_to === $me->id && $task->approval_status === 'rejected';
                    @endphp
                    @if($canUpdateProgress)
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('tasks.progress.create', $task) }}" class="btn btn-sm btn-primary">Update Progress</a>
                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    @elseif($requiresCreationApproval)
                        @if($task->approval_status === 'rejected')
                            <div class="text-danger small">Tugas ditolak: {{ $task->approval_note ?? 'Alasan tidak tersedia.' }}</div>
                            @if($isOwnRejected)
                                <div class="mt-2 d-flex flex-wrap gap-2">
                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary">Perbaiki &amp; ajukan ulang</a>
                                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-secondary">Lihat detail</a>
                                </div>
                            @endif
                        @else
                            <div class="text-muted small">Menunggu persetujuan sebelum progres bisa diupdate.</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Pagination -->
@if($tasks->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $tasks->appends(request()->query())->links() }}
    </div>
@endif
@endsection
