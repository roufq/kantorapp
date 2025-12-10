@extends('layouts.app')
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

<!-- Search Form -->
<div class="mb-4 card">
  <div class="card-body">
    <form method="GET" action="{{ route('tasks.index') }}">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Search</label>
          <input type="text" name="search" class="form-control" placeholder="Title or assignee name" value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Status</label>
          <select name="status" class="form-select">
            <option value="">All</option>
            <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
            <option value="in_progress" {{ request('status')=='in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="completed" {{ request('status')=='completed' ? 'selected' : '' }}>Completed</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Start Date (Due)</label>
          <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">End Date (Due)</label>
          <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        @if(auth()->user()->hasRole('Super Admin'))
        <div class="col-md-3">
          <label class="form-label fw-semibold">Role</label>
          <select name="assignee_role" class="form-select">
            <option value="">All</option>
            <option value="Karyawan" {{ request('assignee_role')=='Karyawan' ? 'selected' : '' }}>Karyawan</option>
            <option value="Admin Lokasi" {{ request('assignee_role')=='Admin Lokasi' ? 'selected' : '' }}>Admin Lokasi</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Location</label>
          <select name="location_id" class="form-select">
            <option value="">All</option>
            @foreach(\App\Models\Location::orderBy('name')->get() as $loc)
              <option value="{{ $loc->id }}" {{ (string)request('location_id')===(string)$loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
            @endforeach
          </select>
        </div>
        @endif
      </div>
      <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-primary">Apply</button>
        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Clear</a>
      </div>
    </form>
  </div>
</div>
<div class="row">
    @foreach($tasks as $task)
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><a href="{{ route('tasks.show', $task) }}">{{ $task->title }}</a></h5>
                </div>
                <div class="card-body">
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
                    @if($needsApproval)
                        <span class="badge text-bg-warning mb-2">Butuh approval ({{ $task->pending_slots_count }})</span>
                    @endif
                    @if($approvalLabel)
                        <span class="badge text-bg-{{ $task->approval_status === 'rejected' ? 'danger' : 'warning' }} mb-2">{{ $approvalLabel }}</span>
                    @endif
                    <p>{{ $task->description }}</p>
                    <p class="mb-1"><strong>Status:</strong> {{ ucfirst($task->status) }}</p>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small">
                            <span>Progress</span>
                            <span>{{ $task->progress ?? 0 }}%</span>
                        </div>
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $task->progress ?? 0 }}%;" aria-valuenow="{{ $task->progress ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <p><strong>Due:</strong> {{ $task->due_date ? $task->due_date->format('d M Y') : 'No due date' }}</p>
                    <p><strong>Assigned by:</strong> {{ $task->assigner->name }}</p>
                    <p><strong>Assigned to:</strong> {{ optional($task->assignee)->name }}</p>
                </div>
                <div class="card-footer">
                    @php
                        $me = auth()->user();
                    @endphp
                    @if($canUpdateProgress)
                        <div class="mt-2">
                            <a href="{{ route('tasks.progress.create', $task) }}" class="btn btn-sm btn-primary me-1">Update Progress</a>
                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    @elseif($requiresCreationApproval)
                        @if($task->approval_status === 'rejected')
                            <div class="mt-2 text-danger small">Tugas Anda ditolak: {{ $task->approval_note ?? 'Alasan tidak tersedia.' }}</div>
                        @else
                            <div class="mt-2 text-muted small">Menunggu persetujuan sebelum progres bisa diupdate.</div>
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
