@php
    use Illuminate\Support\Facades\Storage;
@endphp
@extends('layouts.app')

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
                @if($task->photo_path || $task->document_path)
                    <div class="mt-2">
                        <p class="mb-1"><strong>Lampiran Tugas:</strong></p>
                        <div class="d-flex flex-wrap gap-2">
                            @if($task->photo_path)
                                <a href="{{ route('tasks.download.photo', $task) }}" class="btn btn-sm btn-outline-primary">Lihat / Unduh Foto</a>
                            @endif
                            @if($task->document_path)
                                <a href="{{ route('tasks.download.document', $task) }}" class="btn btn-sm btn-outline-secondary">Unduh Dokumen</a>
                            @endif
                        </div>
                    </div>
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
                <h6 class="mb-0">Lampiran</h6>
            </div>
            <div class="card-body">
                @if($task->photo_path)
                    <div class="mb-3">
                        <p class="fw-semibold small mb-2">Foto</p>
                        @php
                            $photoUrl = $task->photo_path ? asset('storage/' . ltrim($task->photo_path, '/')) : null;
                            $exists = $task->photo_path && Storage::disk('public')->exists($task->photo_path);
                        @endphp
                        @if($photoUrl && $exists)
                            <a href="{{ route('tasks.download.photo', $task) }}" target="_blank">
                                <img src="{{ $photoUrl }}" alt="Foto Tugas" class="img-fluid rounded border" style="max-height: 260px; object-fit: contain; background:#f8f9fa;">
                            </a>
                        @else
                            <p class="text-muted small mb-0">Foto tidak ditemukan di penyimpanan.</p>
                        @endif
                    </div>
                @else
                    <p class="text-muted small mb-3">Belum ada foto.</p>
                @endif
                @if($task->document_path)
                    <div>
                        <p class="fw-semibold small mb-2">Dokumen</p>
                        <a href="{{ route('tasks.download.document', $task) }}" class="btn btn-sm btn-outline-secondary">Unduh Dokumen</a>
                    </div>
                @else
                    <p class="text-muted small mb-0">Belum ada dokumen.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Slot Progres --}}
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Slot Progres</h5>
                @if(auth()->user()->hasAnyRole(['Super Admin','Admin Lokasi']))
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#addSlotForm">Tambah Slot</button>
                @endif
            </div>
            <div class="card-body">
                @if(auth()->user()->hasAnyRole(['Super Admin','Admin Lokasi']))
                <div class="collapse mb-3" id="addSlotForm">
                    <form action="{{ route('tasks.slots.store', $task) }}" method="POST" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-md-3">
                            <label class="form-label">Nama Slot</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">% (0-100)</label>
                            <input type="number" name="percentage" class="form-control" min="1" max="100" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Menit</label>
                            <input type="number" name="minutes" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Urutan</label>
                            <input type="number" name="order" class="form-control" min="0" value="0">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">Simpan Slot</button>
                        </div>
                    </form>
                    <div class="text-muted small mt-1">Pastikan total persen = 100% dan total menit ≤ durasi task.</div>
                </div>
                @endif

                @forelse($task->slots as $slot)
                    <div class="border rounded p-3 mb-2">
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
                            <div class="fw-semibold small mb-1">Lampiran</div>
                            <div class="d-flex flex-wrap gap-2">
                                @forelse($slot->attachments as $att)
                                    @if($att->type === 'link')
                                        <a href="{{ $att->path_or_url }}" target="_blank" class="btn btn-sm btn-outline-primary">Link</a>
                                    @else
                                        <a href="{{ Storage::disk('public')->url($att->path_or_url) }}" target="_blank" class="btn btn-sm btn-outline-primary">{{ ucfirst($att->type) }}</a>
                                    @endif
                                @empty
                                    <span class="text-muted small">Belum ada lampiran.</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="mt-2">
                            <form action="{{ route('task-slots.submit', $slot) }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-end">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label">Foto</label>
                                    <input type="file" name="photo" class="form-control form-control-sm" accept="image/*">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Dokumen</label>
                                    <input type="file" name="document" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.txt,.xls,.xlsx">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Link</label>
                                    <input type="url" name="link" class="form-control form-control-sm" placeholder="https://">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Catatan</label>
                                    <textarea name="note" class="form-control form-control-sm" rows="1" placeholder="Opsional"></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-sm btn-primary">Submit Bukti Slot</button>
                                    <small class="text-muted ms-2">Minimal satu bukti (foto/dokumen/link) wajib diisi.</small>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada slot progres. Tambahkan slot untuk memecah tugas.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
