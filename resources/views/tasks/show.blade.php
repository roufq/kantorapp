@php
    use Illuminate\Support\Facades\Storage;
    use App\Models\TaskSlotHistory;
@endphp
@extends('layouts.appnew')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-lg-7">
        <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;">{{ __('Task Detail') }}</h2>
        <p class="text-muted mb-0" style="font-size: 1.05rem;">{{ $task->title }}</p>
    </div>
    <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
        <a href="{{ route('tasks.index') }}" class="btn btn-outline-light text-dark border bg-white rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-keyboard-backspace me-2 fs-5 align-middle"></i>{{ __('Back to List') }}
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-light mb-4" id="taskDetailCard">
            <div class="card-header border-bottom border-light p-4 d-flex justify-content-between align-items-center" style="background: #ffffff;">
                <h5 class="card-title mb-0 text-dark fw-bold">{{ $task->title }}</h5>
                <span class="badge {{ $task->status === 'completed' ? 'badge-success' : ($task->status === 'in_progress' ? 'badge-info' : 'badge-secondary') }}">
                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                </span>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <label class="text-muted smaller fw-bold mb-2 text-uppercase letter-spacing-1">{{ __('Description') }}</label>
                    <div class="p-3 bg-light rounded-4 border border-light" style="background-color: #fcfdfe !important;">
                        <p class="text-dark mb-0" style="line-height: 1.7; font-size: 0.95rem; opacity: 0.9;">{{ $task->description }}</p>
                    </div>
                </div>

                <div class="row mb-4 g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 border border-light h-100" style="background: #ffffff;">
                            <div class="text-muted smaller fw-bold text-uppercase mb-3">{{ __('Approval Status') }}</div>
                            @if($task->requires_approval)
                                @if($task->approval_status === 'approved')
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <div class="p-2 rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="mdi mdi-check-decagram-outline fs-5"></i>
                                        </div>
                                        <span class="text-dark fw-bold small">Approved</span>
                                    </div>
                                    <div class="smaller text-muted ms-5">{{ optional($task->approved_at)->format('d M Y H:i') }}</div>
                                @elseif($task->approval_status === 'pending')
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="mdi mdi-clock-outline fs-5"></i>
                                        </div>
                                        <span class="text-dark fw-bold small">Waiting for {{ $task->approval_level === 'location_admin' ? 'Location Admin' : 'Super Admin' }}</span>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="mdi mdi-close-octagon-outline fs-5"></i>
                                        </div>
                                        <span class="text-dark fw-bold small">Rejected</span>
                                    </div>
                                @endif
                            @else
                                <span class="text-muted smaller italic">No approval required</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 border border-light h-100" style="background: #ffffff;">
                            <div class="d-flex justify-content-between text-muted smaller fw-bold text-uppercase mb-3">
                                <span>{{ __('Task Progress') }}</span>
                                <span class="text-dark fw-bold">{{ $task->progress ?? 0 }}%</span>
                            </div>
                            <div class="progress rounded-pill shadow-none mb-2" style="height:10px; background: #f1f5f9;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $task->progress ?? 0 }}%; background-color: #10b981;"></div>
                            </div>
                            <div class="smaller text-muted italic">Current completion rate</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted smaller fw-bold text-uppercase mb-1">{{ __('Assigned By') }}</div>
                        <div class="text-dark fw-bold small">{{ $task->assigner->name }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted smaller fw-bold text-uppercase mb-1">{{ __('Assigned To') }}</div>
                        <div class="text-primary fw-bold small">{{ optional($task->assignee)->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted smaller fw-bold text-uppercase mb-1">{{ __('Target Date') }}</div>
                        <div class="text-dark small fw-bold">
                            <span class="text-{{ $task->due_date && $task->due_date->isPast() ? 'danger' : 'dark' }}">
                                {{ $task->due_date ? $task->due_date->format('d M Y') : 'No Due Date' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted smaller fw-bold text-uppercase mb-1">{{ __('Est. Duration') }}</div>
                        <div class="text-dark small fw-bold">{{ $task->duration_minutes ? $task->duration_minutes . ' mins' : '-' }}</div>
                    </div>
                </div>
            </div>
            <div class="card-footer border-top border-light p-4 d-flex justify-content-between align-items-center" style="background: #ffffff;">
                <div>
                    @can('update', $task)
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold">
                            <i class="mdi mdi-pencil-outline me-1"></i>{{ __('Edit Task') }}
                        </a>
                    @endcan
                </div>
                <div class="d-flex gap-2">
                    @php
                        $me = auth()->user();
                        $hasSlots = $task->slots->count() > 0;
                        $canUpdateProgress = $hasSlots && (!$task->requires_approval || $task->approval_status === 'approved') &&
                            ($me->hasRole('Super Admin') || $task->assigned_to === $me->id || ($me->hasRole('Location Admin') && optional($task->assignee)->location_id === $me->location_id));
                    @endphp
                    @if($canUpdateProgress)
                        <a href="{{ route('tasks.progress.create', $task) }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-soft">
                            <i class="mdi mdi-update me-1"></i>{{ __('Update Progress') }}
                        </a>
                    @elseif(!$hasSlots)
                        <span class="badge badge-warning"><i class="mdi mdi-alert-circle-outline me-1"></i> {{ __('Needs Slots') }}</span>
                    @elseif($task->requires_approval && $task->approval_status !== 'approved')
                         <span class="badge {{ $task->approval_status === 'rejected' ? 'badge-danger' : 'badge-warning' }}">
                            <i class="mdi mdi-timer-sand me-1"></i> {{ $task->approval_status === 'rejected' ? 'Rejected' : 'Awaiting Approval' }}
                         </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-light">
            <div class="card-header border-bottom border-light p-4" style="background: #ffffff;">
                <h5 class="mb-0 text-dark fw-bold"><i class="mdi mdi-view-list-outline me-2 text-info"></i>{{ __('Progress Slots') }}</h5>
            </div>
            <div class="card-body p-4">
                @php
                    $slotStatus = $task->getSlotCompositionStatus();
                    $missingPercent = max(0, round(100 - $slotStatus['total_percent'], 2));
                    $missingMinutes = $slotStatus['duration_minutes'] !== null
                        ? max(0, $slotStatus['duration_minutes'] - $slotStatus['total_minutes'])
                        : null;
                    $slotIncomplete = !$slotStatus['complete'];
                @endphp
                
                @if($slotIncomplete)
                    <div class="alert badge-warning bg-opacity-10 border-0 p-3 mb-4 rounded-4 d-flex align-items-center">
                        <i class="mdi mdi-alert-circle-outline fs-4 me-2"></i>
                        <span class="fw-bold fw-bold smaller" style="text-transform: none; letter-spacing: 0;">
                            {{ __('Slot composition is missing') }} {{ $missingPercent }}% {{ $missingMinutes ? 'and ' . $missingMinutes . ' mins.' : '.' }}
                        </span>
                    </div>
                @endif

                @if(auth()->user()->hasAnyRole(['Super Admin','Location Admin']))
                    <div class="mb-4">
                        <button class="btn btn-light border bg-white rounded-pill px-4 fw-bold shadow-sm w-100 mb-2 py-2" type="button" id="addSlotToggleBtn" data-bs-toggle="collapse" data-bs-target="#addSlotFormSide" @if(!$slotIncomplete) disabled @endif>
                            <i class="mdi mdi-plus-circle-outline me-1"></i>{{ __('Create Progress Slot') }}
                        </button>
                        
                        <div class="collapse" id="addSlotFormSide">
                            <div class="card shadow-sm border-light p-4 rounded-4 mt-3">
                                <div id="addSlotFormError" class="alert badge-danger rounded-3 p-3 mb-3 d-none"></div>
                                <form action="{{ route('tasks.slots.store', $task) }}" method="POST" class="row g-3" id="addSlotFormSideForm">
                                    @csrf
                                    <div class="col-md-6">
                                        <label class="form-label text-muted smaller fw-bold text-uppercase">{{ __('Slot Name') }}</label>
                                        <input type="text" name="name" class="form-control" required placeholder="e.g. Phase 1">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-muted smaller fw-bold text-uppercase">% Weight</label>
                                        <input type="number" name="percentage" class="form-control" min="1" max="100" step="0.01" value="{{ old('percentage', $missingPercent > 0 ? $missingPercent : '') }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-muted smaller fw-bold text-uppercase">{{ __('Minutes') }}</label>
                                        <input type="number" name="minutes" class="form-control" min="1" value="{{ old('minutes', $missingMinutes && $missingMinutes > 0 ? $missingMinutes : '') }}" required>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-soft">
                                            {{ __('Add Slot') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <div id="taskSlotList" data-duration-minutes="{{ $task->duration_minutes ?? '' }}" class="d-flex flex-column gap-3">
                    @forelse($task->slots as $slot)
                        @include('tasks.partials.slot-card', [
                            'task' => $task,
                            'slot' => $slot,
                            'slotIncomplete' => $slotIncomplete,
                        ])
                    @empty
                        <div class="text-center py-5 rounded-4 bg-light border border-dashed" id="taskSlotEmpty">
                            <i class="mdi mdi-layers-off-outline fs-2 text-muted opacity-50 d-block mb-2"></i>
                            <p class="text-muted small italic">{{ __('No slots defined for this task') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100 shadow-sm border-light overflow-hidden" id="historyProgressCard">
            <div class="card-header border-bottom border-light p-4 d-flex align-items-center" style="background: #ffffff;">
                <h5 class="mb-0 text-dark fw-bold"><i class="mdi mdi-history me-2 text-warning"></i>{{ __('Progress History') }}</h5>
            </div>
            <div class="card-body p-4" id="historyProgressBody" style="overflow-y:auto;">
                @php
                    $slotHistory = TaskSlotHistory::query()
                        ->whereIn('task_slot_id', $task->slots->pluck('id'))
                        ->whereIn('action', ['approved', 'rejected'])
                        ->with(['actor', 'slot.attachments'])
                        ->orderBy('created_at', 'desc')
                        ->get();
                @endphp
                @forelse($slotHistory as $history)
                    @php
                        $data = $history->data_after ?? $history->data_before ?? [];
                        $slotName = $data['name'] ?? optional($history->slot)->name ?? '-';
                        $slotPercent = $data['percentage'] ?? optional($history->slot)->percentage ?? 0;
                        $statusClass = $history->action === 'approved' ? 'badge-success' : 'badge-danger';
                        $detailPayload = [
                            'name' => $slotName,
                            'status' => $history->action,
                            'actor' => $history->actor ? $history->actor->name : '-',
                            'action_at' => $history->created_at ? $history->created_at->format('d M Y H:i') : '-',
                            'rejection_reason' => $data['rejection_reason'] ?? null,
                            'link' => optional($history->slot->attachments->first())->path_or_url ?? null,
                        ];
                    @endphp
                    <div class="p-3 rounded-4 border border-light mb-3 hover-lift shadow-sm bg-white">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                             <div>
                                <div class="text-dark fw-bold small text-truncate" style="max-width: 15rem;">{{ $slotName }}</div>
                                <div class="smaller text-muted mt-1">{{ $slotPercent }}% completed</div>
                             </div>
                             <span class="badge {{ $statusClass }} rounded-pill" style="font-size: 0.6rem !important;">
                                {{ strtoupper($history->action) }}
                             </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-light">
                            <div class="smaller text-muted d-flex align-items-center">
                                <i class="mdi mdi-account-circle-outline me-1"></i>
                                <span class="text-truncate" style="max-width: 8rem;">{{ $history->actor ? $history->actor->name : '-' }}</span>
                            </div>
                            <button type="button" class="btn btn-link text-primary btn-sm p-0 smaller fw-bold history-detail-btn text-decoration-none shadow-none" data-bs-toggle="modal" data-bs-target="#historyDetailModal" data-details='@json($detailPayload)'>
                                {{ __('Details') }} <i class="mdi mdi-chevron-right"></i>
                            </button>
                        </div>
                        @if($history->action === 'rejected' && !empty($data['rejection_reason']))
                            <div class="mt-2 text-rose smaller border-start border-danger border-2 ps-2 italic" style="color: #e11d48;">"{{ $data['rejection_reason'] }}"</div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-5">
                        <div class="p-3 bg-light rounded-circle d-inline-flex mb-3">
                            <i class="mdi mdi-history fs-2 text-muted"></i>
                        </div>
                        <p class="text-muted smaller mb-0">{{ __('No entries in history.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="historyDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-bottom border-light px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="mdi mdi-clock-check-outline me-2 text-primary"></i>History Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="text-muted smaller fw-bold text-uppercase mb-1">Slot Name</div>
                        <div id="historyDetailName" class="text-dark fw-bold">-</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted smaller fw-bold text-uppercase mb-1">Status</div>
                        <div id="historyDetailStatus" class="fw-bold">-</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted smaller fw-bold text-uppercase mb-1">Processed By</div>
                        <div id="historyDetailActor" class="text-dark-blue fw-bold">-</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted smaller fw-bold text-uppercase mb-1">Processed At</div>
                        <div id="historyDetailTime" class="text-dark small">-</div>
                    </div>
                    <div class="col-12" id="historyDetailReasonRow">
                        <div class="text-muted smaller fw-bold text-uppercase mb-1">Rejection Reason</div>
                        <div id="historyDetailReason" class="text-danger italic small">-</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <div id="historyDetailLinks" class="w-100"></div>
            </div>
        </div>
    </div>
</div>

<style>
    .smaller { font-size: 0.75rem; }
    .letter-spacing-1 { letter-spacing: 0.5px; }
    .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.05) !important; }
    .shadow-soft { box-shadow: 0 10px 20px rgba(16, 185, 129, 0.15) !important; }
    .text-rose { color: #f43f5e; }
    .text-dark-blue { color: #1e3a8a; }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const historyBody = document.getElementById('historyProgressBody');
        const taskDetailCard = document.getElementById('taskDetailCard');
        const historyCard = document.getElementById('historyProgressCard');

        const syncHistoryHeight = () => {
            if (!taskDetailCard || !historyCard || !historyBody) return;
            const detailHeight = taskDetailCard.offsetHeight;
            const header = historyCard.querySelector('.card-header');
            const headerHeight = header ? header.offsetHeight : 0;
            const bodyPadding = 48;
            historyBody.style.maxHeight = (detailHeight - headerHeight - bodyPadding) + 'px';
        };

        syncHistoryHeight();
        window.addEventListener('resize', syncHistoryHeight);

        document.querySelectorAll('.history-detail-btn').forEach((item) => {
            item.addEventListener('click', () => {
                const details = JSON.parse(item.getAttribute('data-details'));
                if (!details) return;

                document.getElementById('historyDetailName').textContent = details.name;
                document.getElementById('historyDetailStatus').textContent = details.status.toUpperCase();
                document.getElementById('historyDetailStatus').className = 'fw-bold ' + (details.status === 'approved' ? 'text-success' : 'text-danger');
                document.getElementById('historyDetailActor').textContent = details.actor;
                document.getElementById('historyDetailTime').textContent = details.action_at;
                document.getElementById('historyDetailReason').textContent = details.rejection_reason || '-';
                document.getElementById('historyDetailReasonRow').style.display = details.status === 'rejected' ? 'block' : 'none';

                const linksWrap = document.getElementById('historyDetailLinks');
                linksWrap.innerHTML = '';
                if (details.link) {
                    linksWrap.innerHTML = `<a href="${details.link}" target="_blank" class="btn btn-outline-primary w-100 rounded-pill fw-bold"><i class="mdi mdi-link-variant me-1"></i>View Link</a>`;
                }
            });
        });
    });
</script>
@endsection
