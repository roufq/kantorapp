@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1 text-dark fw-bold">Task Progress Approval</h3>
        <p class="text-muted mb-0">Approve/Reject tasks and slot-based progress. Legacy progress is retained for older backlogs.</p>
    </div>
    <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm ps-3 pe-3 rounded-pill fw-bold">
        <i class="mdi mdi-arrow-left me-1"></i> Back to Tasks
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('tasks.progress.approvals') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Search (title / employee)</label>
                <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Search title or employee name">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Location</label>
                <select name="location_id" class="form-select">
                    <option value="">All</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ (string)$locationId === (string)$loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Employees</label>
                <select name="assignee_id" class="form-select">
                    <option value="">All</option>
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
        <h5 class="card-title mb-0">New Task Approval (Self Assign)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Title</th>
                        <th>Assignee</th>
                        <th>Created by</th>
                        <th>Location</th>
                        <th>Due</th>
                        <th>Duration (minutes)</th>
                        <th>Action</th>
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
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-secondary">Details</a>
                            <form action="{{ route('tasks.approvals.approve', $task) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#rejectCreationModal{{ $task->id }}">Reject</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-muted text-center">No self-assigned tasks pending approval.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(!empty($pendingTasks) && $pendingTasks->count() > 0)
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Pending Approval (Per Task)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Title</th>
                        <th>Assignee</th>
                        <th>Location</th>
                        <th>Due</th>
                        <th>Duration (minutes)</th>
                        <th>Slot Pending</th>
                        <th>Action</th>
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
                                <span class="badge text-bg-warning ms-2">Needs task approval</span>
                            @endif
                        </td>
                        <td>{{ optional($task->assignee)->name }}</td>
                        <td>{{ optional($task->assignee->location)->name ?? '-' }}</td>
                        <td>{{ optional($task->due_date)->format('d M Y') ?? '-' }}</td>
                        <td>{{ $task->duration_minutes ?? '-' }}</td>
                        <td>{{ $task->slots->count() }}</td>
                        <td class="d-flex gap-1 flex-wrap">
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-secondary">Details</a>
                            <form action="{{ route('tasks.approve-slots', $task) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#rejectTaskModal{{ $task->id }}">Reject</button>
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
                        <th>Submitted By</th>
                        <th>Progress</th>
                        <th>Submitted At</th>
                        <th>Action</th>
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
                            <a href="{{ route('tasks.show', $update->task) }}" class="btn btn-sm btn-outline-secondary">Details</a>
                            <form action="{{ route('tasks.progress.approve', $update) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#rejectLegacyModal{{ $update->id }}">Reject</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted text-center">No progress pending approval.</td></tr>
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
        <h5 class="modal-title">Rejection Reason</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('tasks.approvals.reject', $task) }}" method="POST">
        @csrf
        <div class="modal-body">
          <textarea name="reason" class="form-control" rows="3" required placeholder="Write the reason here"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
        <h5 class="modal-title">Task Rejection Reason</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('tasks.reject-slots', $task) }}" method="POST">
        @csrf
        <div class="modal-body">
          <textarea name="reason" class="form-control" rows="3" required placeholder="Write the reason here"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
        <h5 class="modal-title">Progress Rejection Reason</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('tasks.progress.reject', $update) }}" method="POST">
        @csrf
        <div class="modal-body">
          <textarea name="reason" class="form-control" rows="3" required placeholder="Write the reason here"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
