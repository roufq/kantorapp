@extends('layouts.app')
@section('title')
<div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">Task Details</h3></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('master-tasks.index') }}">Tasks</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $masterTask->title }}</li>
            </ol>
        </div>
    </div>
    <!--end::Row-->
</div>
@endsection

@section('scripts')
<script>
function updateStatus(taskId, newStatus) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = `/master-tasks/${taskId}`;
    const data = {
        status: newStatus,
        _method: 'PATCH'
    };

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the status badge in the main card
            const statusBadge = document.querySelector('.card-body p .badge');
            statusBadge.className = 'badge ';
            switch (newStatus) {
                case 'pending':
                    statusBadge.classList.add('text-bg-warning');
                    statusBadge.textContent = 'Pending';
                    break;
                case 'in_progress':
                    statusBadge.classList.add('text-bg-info');
                    statusBadge.textContent = 'In Progress';
                    break;
                case 'completed':
                    statusBadge.classList.add('text-bg-success');
                    statusBadge.textContent = 'Completed';
                    break;
            }
            // Show success message
            document.getElementById('status-message').innerHTML = '<div class="alert alert-success">Status updated successfully!</div>';
            setTimeout(() => {
                document.getElementById('status-message').innerHTML = '';
            }, 3000);
        } else {
            document.getElementById('status-message').innerHTML = '<div class="alert alert-danger">Failed to update status.</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('status-message').innerHTML = '<div class="alert alert-danger">An error occurred while updating status.</div>';
    });
}
</script>
@endsection
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $masterTask->title }}</h3>
                <div class="card-tools">
                    <a href="{{ route('master-tasks.index') }}" class="btn btn-secondary btn-sm">Back</a>
                    <a href="{{ route('master-tasks.edit', $masterTask) }}" class="btn btn-primary btn-sm">Edit</a>
                    <form action="{{ route('master-tasks.destroy', $masterTask) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this task?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Description</h5>
                        <p>{{ $masterTask->description }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Status</h5>
                        <p>
                            @switch($masterTask->status)
                                @case('pending')
                                    <span class="badge text-bg-warning">Pending</span>
                                    @break
                                @case('in_progress')
                                    <span class="badge text-bg-info">In Progress</span>
                                    @break
                                @case('completed')
                                    <span class="badge text-bg-success">Completed</span>
                                    @break
                                @default
                                    <span class="badge text-bg-secondary">{{ $masterTask->status }}</span>
                            @endswitch
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Due Date</h5>
                        <p>{{ $masterTask->due_date ? $masterTask->due_date->format('d M Y') : 'No due date' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Assigned By</h5>
                        <p>{{ $masterTask->assigner->name }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Assigned To</h5>
                        <p>{{ $masterTask->assignee ? $masterTask->assignee->name : 'Not assigned' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Created At</h5>
                        <p>{{ $masterTask->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                @if($masterTask->updated_at != $masterTask->created_at)
                <div class="row">
                    <div class="col-md-6">
                        <h5>Last Updated</h5>
                        <p>{{ $masterTask->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                @endif
                @if($masterTask->photo_path)
                <div class="row">
                    <div class="col-md-6">
                        <h5>Photo</h5>
                        <p><a href="{{ route('master-tasks.download.photo', $masterTask) }}" target="_blank" class="btn btn-sm btn-outline-primary">Download Photo</a></p>
                    </div>
                </div>
                @endif
                @if($masterTask->document_path)
                <div class="row">
                    <div class="col-md-6">
                        <h5>Document</h5>
                        <p><a href="{{ route('master-tasks.download.document', $masterTask) }}" target="_blank" class="btn btn-sm btn-outline-primary">Download Document</a></p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="status" class="form-label">Update Status</label>
                    <select name="status" id="status" class="form-select" onchange="updateStatus({{ $masterTask->id }}, this.value)">
                        <option value="pending" {{ $masterTask->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ $masterTask->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ $masterTask->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div id="status-message"></div>
            </div>
        </div>
    </div>
</div>
@endsection
