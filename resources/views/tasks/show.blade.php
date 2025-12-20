@php
    use Illuminate\Support\Facades\Storage;
@endphp
@extends('layouts.appnew')

@section('title')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">Task Detail</h3></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">Tasks</a></li>
                <li class="breadcrumb-item active" aria-current="page">Task Detail</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                 <h4 class="card-title mb-0">{{ $task->title }}</h4>
                 <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body">
                <p><strong>Description:</strong></p>
                <p>{{ $task->description }}</p>
                <hr>
                <p class="mb-1"><strong>Status:</strong> <span class="badge text-bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'info' : 'secondary') }}">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span></p>
                @if($task->requires_approval)
                    <p class="mb-1"><strong>Persetujuan Tugas:</strong>
                        @if($task->approval_status === 'approved')
                            <span class="badge text-bg-success">Disetujui @ {{ optional($task->approved_at)->format('d M Y H:i') }}</span>
                            @if($task->approver)
                                <span class="text-muted small">oleh {{ $task->approver->name }}</span>
                            @endif
                        @elseif($task->approval_status === 'pending')
                            <span class="badge text-bg-warning">Menunggu {{ $task->approval_level === 'location_admin' ? 'Admin Lokasi' : 'Super Admin' }}</span>
                        @else
                            <span class="badge text-bg-danger">Ditolak</span>
                            @if($task->approval_note)
                                <span class="text-danger small">Alasan: {{ $task->approval_note }}</span>
                            @endif
                        @endif
                    </p>
                @endif
                <div class="mb-3">
                    <div class="d-flex justify-content-between small">
                        <span>Progress</span>
                        <span>{{ $task->progress ?? 0 }}%</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $task->progress ?? 0 }}%;" aria-valuenow="{{ $task->progress ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <p><strong>Assigned by:</strong><br>{{ $task->assigner->name }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>Assigned to:</strong><br>{{ optional($task->assignee)->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>Created Date:</strong><br>{{ $task->created_at->format('d M Y') }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>Due Date:</strong><br>{{ $task->due_date ? $task->due_date->format('d M Y') : 'No due date' }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>Durasi (menit):</strong><br>{{ $task->duration_minutes ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                @php
                    $me = auth()->user();
                    $hasSlots = $task->slots->count() > 0;
                    $canUpdateProgress = $hasSlots && (!$task->requires_approval || $task->approval_status === 'approved') &&
                        ($me->hasRole('Super Admin') || $task->assigned_to === $me->id || ($me->hasRole('Admin Lokasi') && optional($task->assignee)->location_id === $me->location_id));
                @endphp
                @if($canUpdateProgress)
                    <a href="{{ route('tasks.progress.create', $task) }}" class="btn btn-primary me-2">Update Progress</a>
                @elseif(!$hasSlots)
                    <span class="text-muted me-2">Tambah slot progres dulu sebelum update.</span>
                @elseif($task->requires_approval)
                    @if($task->approval_status === 'rejected')
                        <span class="text-danger me-2">Tugas Anda ditolak: {{ $task->approval_note ?? 'Alasan tidak tersedia.' }}</span>
                    @else
                        <span class="text-muted me-2">Menunggu persetujuan sebelum progres bisa diupdate.</span>
                    @endif
                @endif
                @can('update', $task)
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-primary">Edit Task</a>
                @endcan
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0">Slot Progres</h6>
            </div>
            <div class="card-body">
                @if(auth()->user()->hasAnyRole(['Super Admin','Admin Lokasi']))
                    <div class="mb-3">
                        <button class="btn btn-sm btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#addSlotFormSide">Tambah Slot</button>
                        <div class="collapse mt-2" id="addSlotFormSide">
                            <form action="{{ route('tasks.slots.store', $task) }}" method="POST" class="row g-2">
                                @csrf
                                <div class="col-6">
                                    <label class="form-label">Nama</label>
                                    <input type="text" name="name" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">% (0-100)</label>
                                    <input type="number" name="percentage" class="form-control form-control-sm" min="1" max="100" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Menit</label>
                                    <input type="number" name="minutes" class="form-control form-control-sm" min="1" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Urutan</label>
                                    <input type="number" name="order" class="form-control form-control-sm" min="0" value="0">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Simpan Slot</button>
                                    <div class="small text-muted mt-1">Pastikan total persen=100% dan total menit ≤ durasi task.</div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
                @forelse($task->slots as $slot)
                    <div class="border rounded p-2 mb-3">
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold">{{ $slot->name }} ({{ $slot->percentage }}% | {{ $slot->minutes }} menit)</div>
                                <div class="small text-muted">Status:
                                    <span class="badge text-bg-{{ $slot->status === 'approved' ? 'success' : ($slot->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($slot->status) }}</span>
                                    @if($slot->approved_at)
                                        <span class="text-muted">oleh {{ optional($slot->approver)->name }} @ {{ $slot->approved_at->format('d M Y H:i') }}</span>
                                    @endif
                                </div>
                                @if($slot->rejection_reason)
                                    <div class="text-danger small">Alasan reject: {{ $slot->rejection_reason }}</div>
                                @endif
                            </div>
                            <div class="d-flex align-items-start gap-2 flex-wrap">
                                @php
                                    $canApprove = $slot->status === 'pending' && (
                                        auth()->user()->hasRole('Super Admin') ||
                                        (auth()->user()->hasRole('Admin Lokasi') && optional($task->assignee)->location_id === auth()->user()->location_id)
                                    );
                                    $canManage = auth()->user()->hasAnyRole(['Super Admin','Admin Lokasi']);
                                @endphp
                                @if($canApprove)
                                    <form action="{{ route('task-slots.approve', $slot) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                    <form action="{{ route('task-slots.reject', $slot) }}" method="POST" class="d-flex align-items-start gap-2">
                                        @csrf
                                        <textarea name="reason" class="form-control form-control-sm" placeholder="Alasan reject" rows="1" required style="min-width: 180px;"></textarea>
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Reject</button>
                                    </form>
                                @endif
                                @if($canManage)
                                    <form action="{{ route('tasks.slots.destroy', [$task, $slot]) }}" method="POST" onsubmit="return confirm('Hapus slot ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-secondary btn-sm">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <div class="mt-2">
                            <div class="fw-semibold small mb-1">Lampiran Link</div>
                            @php $linkAttachments = $slot->attachments->where('type','link'); @endphp
                            <div class="d-flex flex-wrap gap-2">
                                @forelse($linkAttachments as $att)
                                    <a href="{{ $att->path_or_url }}" target="_blank" class="btn btn-sm btn-outline-primary">Link {{ $loop->iteration }}</a>
                                @empty
                                    <span class="text-muted small">Belum ada lampiran.</span>
                                @endforelse
                            </div>
                        </div>

                        @php
                            $slotCanSubmit = ($slot->status === 'rejected') || ($slot->status === 'pending' && $slot->attachments->where('type','link')->isEmpty());
                        @endphp
                        <div class="mt-2">
                            @if($slotCanSubmit)
                                <form action="{{ route('task-slots.submit', $slot) }}" method="POST" class="row g-2 align-items-end">
                                    @csrf
                                    <div class="col-12">
                                        <label class="form-label">Link</label>
                                        <input type="url" name="link" class="form-control form-control-sm" placeholder="https://" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Catatan</label>
                                        <textarea name="note" class="form-control form-control-sm" rows="1" placeholder="Opsional"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-sm btn-primary w-100">Submit Bukti Slot (Link)</button>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-light border small mb-0">
                                    Bukti slot sudah dikirim dan menunggu/approved. Ajukan ulang hanya setelah status di-reject.
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada slot progres.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
