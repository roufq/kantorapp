<?php
    use Illuminate\Support\Facades\Storage;
    use App\Models\TaskSlotHistory;
?>


<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Task Detail</h3>
        <p class="text-muted mb-0"><?php echo e($task->title); ?></p>
    </div>
    <a href="<?php echo e(route('tasks.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="card" id="taskDetailCard">
            <div class="card-header d-flex justify-content-between align-items-center">
                 <h4 class="card-title mb-0"><?php echo e($task->title); ?></h4>
                 <a href="<?php echo e(route('tasks.index')); ?>" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body">
                <p><strong>Description:</strong></p>
                <p><?php echo e($task->description); ?></p>
                <hr>
                <p class="mb-1"><strong>Status:</strong> <span class="badge text-bg-<?php echo e($task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'info' : 'secondary')); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $task->status))); ?></span></p>
                <?php if($task->requires_approval): ?>
                    <p class="mb-1"><strong>Persetujuan Tugas:</strong>
                        <?php if($task->approval_status === 'approved'): ?>
                            <span class="badge text-bg-success">Disetujui @ <?php echo e(optional($task->approved_at)->format('d M Y H:i')); ?></span>
                            <?php if($task->approver): ?>
                                <span class="text-muted small">oleh <?php echo e($task->approver->name); ?></span>
                            <?php endif; ?>
                        <?php elseif($task->approval_status === 'pending'): ?>
                            <span class="badge text-bg-warning">Menunggu <?php echo e($task->approval_level === 'location_admin' ? 'Admin Lokasi' : 'Super Admin'); ?></span>
                        <?php else: ?>
                            <span class="badge text-bg-danger">Ditolak</span>
                            <?php if($task->approval_note): ?>
                                <span class="text-danger small">Alasan: <?php echo e($task->approval_note); ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small">
                        <span>Progress</span>
                        <span><?php echo e($task->progress ?? 0); ?>%</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar" role="progressbar" style="width: <?php echo e($task->progress ?? 0); ?>%;" aria-valuenow="<?php echo e($task->progress ?? 0); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <p><strong>Assigned by:</strong><br><?php echo e($task->assigner->name); ?></p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>Assigned to:</strong><br><?php echo e(optional($task->assignee)->name ?? 'N/A'); ?></p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>Created Date:</strong><br><?php echo e($task->created_at->format('d M Y')); ?></p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>Due Date:</strong><br><?php echo e($task->due_date ? $task->due_date->format('d M Y') : 'No due date'); ?></p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>Durasi (menit):</strong><br><?php echo e($task->duration_minutes ?? '-'); ?></p>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <?php
                    $me = auth()->user();
                    $hasSlots = $task->slots->count() > 0;
                    $canUpdateProgress = $hasSlots && (!$task->requires_approval || $task->approval_status === 'approved') &&
                        ($me->hasRole('Super Admin') || $task->assigned_to === $me->id || ($me->hasRole('Admin Lokasi') && optional($task->assignee)->location_id === $me->location_id));
                ?>
                <?php if($canUpdateProgress): ?>
                    <a href="<?php echo e(route('tasks.progress.create', $task)); ?>" class="btn btn-primary me-2">Update Progress</a>
                <?php elseif(!$hasSlots): ?>
                    <span class="text-muted me-2">Tambah slot progres dulu sebelum update.</span>
                <?php elseif($task->requires_approval): ?>
                    <?php if($task->approval_status === 'rejected'): ?>
                        <span class="text-danger me-2">Tugas Anda ditolak: <?php echo e($task->approval_note ?? 'Alasan tidak tersedia.'); ?></span>
                    <?php else: ?>
                        <span class="text-muted me-2">Menunggu persetujuan sebelum progres bisa diupdate.</span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $task)): ?>
                    <a href="<?php echo e(route('tasks.edit', $task)); ?>" class="btn btn-outline-primary">Edit Task</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100" id="historyProgressCard">
            <div class="card-header">
                <h6 class="mb-0">History Progress</h6>
            </div>
            <div class="card-body" id="historyProgressBody" style="overflow-y:auto;">
                <?php
                    $slotHistory = TaskSlotHistory::query()
                        ->whereIn('task_slot_id', $task->slots->pluck('id'))
                        ->whereIn('action', ['approved', 'rejected'])
                        ->with(['actor', 'slot.attachments'])
                        ->orderBy('created_at', 'desc')
                        ->get();
                ?>
                <?php $__empty_1 = true; $__currentLoopData = $slotHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="border rounded p-2 mb-3">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <?php
                                    $data = $history->data_after ?? $history->data_before ?? [];
                                    $slotName = $data['name'] ?? optional($history->slot)->name ?? '-';
                                    $slotPercent = $data['percentage'] ?? optional($history->slot)->percentage ?? 0;
                                    $slotMinutes = $data['minutes'] ?? optional($history->slot)->minutes ?? 0;
                                    $statusClass = $history->action === 'approved' ? 'badge badge-success' : 'badge badge-danger';
                                    $attachments = optional($history->slot)->attachments
                                        ? $history->slot->attachments
                                            ->where('type', 'link')
                                            ->filter(function ($att) use ($history) {
                                                return $att->created_at && $history->created_at && $att->created_at->lte($history->created_at);
                                            })
                                            ->sortByDesc('created_at')
                                            ->values()
                                        : collect();
                                    $latestLink = $attachments->first();
                                    $detailPayload = [
                                        'name' => $slotName,
                                        'percentage' => $slotPercent,
                                        'minutes' => $slotMinutes,
                                        'status' => $history->action,
                                        'actor' => $history->actor ? $history->actor->name : '-',
                                        'action_at' => $history->created_at ? $history->created_at->format('d M Y H:i') : '-',
                                        'rejection_reason' => $data['rejection_reason'] ?? null,
                                        'link' => $latestLink ? $latestLink->path_or_url : null,
                                    ];
                                ?>
                                <div class="fw-semibold">
                                    <?php echo e($slotName); ?> (<?php echo e($slotPercent); ?>% | <?php echo e($slotMinutes); ?> menit)
                                </div>
                                <div class="small text-muted">
                                    <span class="<?php echo e($statusClass); ?>"><?php echo e(ucfirst($history->action)); ?></span>
                                    <?php if($history->actor): ?>
                                        <span class="text-muted">oleh <?php echo e($history->actor->name); ?> @ <?php echo e($history->created_at->format('d M Y H:i')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-primary btn-sm history-detail-btn" data-toggle="modal" data-target="#historyDetailModal" data-details='<?php echo json_encode($detailPayload, 15, 512) ?>'>Detail</button>
                            </div>
                        </div>
                        <?php if($history->action === 'rejected' && !empty($data['rejection_reason'])): ?>
                            <div class="mt-2 text-danger small"><strong>Alasan penolakan:</strong> <?php echo e($data['rejection_reason']); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted mb-0">Belum ada history progress.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Slot Progres</h6>
            </div>
            <div class="card-body">
                <?php
                    $slotStatus = $task->getSlotCompositionStatus();
                    $missingPercent = max(0, round(100 - $slotStatus['total_percent'], 2));
                    $missingMinutes = $slotStatus['duration_minutes'] !== null
                        ? max(0, $slotStatus['duration_minutes'] - $slotStatus['total_minutes'])
                        : null;
                    $slotIncomplete = !$slotStatus['complete'];
                    $slotAlertMessage = 'Komposisi slot belum lengkap. ';
                    if ($slotStatus['duration_minutes'] !== null) {
                        $slotAlertMessage .= 'Kurang ' . $missingPercent . '% dan ' . $missingMinutes . ' menit. Lengkapi slot terlebih dahulu.';
                    } else {
                        $slotAlertMessage .= 'Kurang ' . $missingPercent . '%. Lengkapi slot terlebih dahulu.';
                    }
                ?>
                <div id="slotIncompleteAlert" class="alert alert-warning small <?php echo e($slotIncomplete ? '' : 'd-none'); ?>">
                    <span id="slotIncompleteMessage"><?php echo e($slotAlertMessage); ?></span>
                </div>
                <?php if(auth()->user()->hasAnyRole(['Super Admin','Admin Lokasi'])): ?>
                    <div class="mb-3">
                        <button class="btn btn-sm btn-outline-primary w-100" type="button" id="addSlotToggleBtn" data-toggle="collapse" data-target="#addSlotFormSide" aria-expanded="false" aria-controls="addSlotFormSide" <?php if(!$slotIncomplete): ?> disabled <?php endif; ?>>
                            Tambah Slot
                        </button>
                        <div class="small text-muted mt-1">Tombol ini nonaktif jika total persentase sudah 100% dan total menit sudah sesuai durasi task.</div>
                        <div class="collapse mt-2" id="addSlotFormSide">
                            <div id="addSlotFormError" class="alert alert-danger small d-none"></div>
                            <form action="<?php echo e(route('tasks.slots.store', $task)); ?>" method="POST" class="row g-2" id="addSlotFormSideForm">
                                <?php echo csrf_field(); ?>
                                <div class="col-6">
                                    <label class="form-label">Nama</label>
                                    <input type="text" name="name" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">% (0-100)</label>
                                    <input type="number" name="percentage" class="form-control form-control-sm" min="1" max="100" step="0.01" value="<?php echo e(old('percentage', $missingPercent > 0 ? $missingPercent : '')); ?>" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Menit</label>
                                    <input type="number" name="minutes" class="form-control form-control-sm" min="1" value="<?php echo e(old('minutes', $missingMinutes && $missingMinutes > 0 ? $missingMinutes : '')); ?>" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Urutan</label>
                                    <input type="number" name="order" class="form-control form-control-sm" min="0" value="0">
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Simpan Slot</button>
                                    <div class="small text-muted mt-1">Pastikan total persen = 100% dan total menit = durasi task (jika diisi).</div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
                <div id="taskSlotList" data-duration-minutes="<?php echo e($task->duration_minutes ?? ''); ?>">
                    <?php $__empty_1 = true; $__currentLoopData = $task->slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php echo $__env->make('tasks.partials.slot-card', [
                            'task' => $task,
                            'slot' => $slot,
                            'slotIncomplete' => $slotIncomplete,
                            'slotAlertMessage' => $slotAlertMessage,
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-muted mb-0" id="taskSlotEmpty">Belum ada slot progres.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="historyDetailModal" tabindex="-1" role="dialog" aria-labelledby="historyDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyDetailModalLabel">Detail History Slot</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-2"><strong>Nama Slot:</strong> <span id="historyDetailName">-</span></div>
                <div class="mb-2"><strong>Persentase:</strong> <span id="historyDetailPercent">-</span></div>
                <div class="mb-2"><strong>Menit:</strong> <span id="historyDetailMinutes">-</span></div>
                <div class="mb-2"><strong>Status:</strong> <span id="historyDetailStatus">-</span></div>
                <div class="mb-2"><strong>Diproses Oleh:</strong> <span id="historyDetailActor">-</span></div>
                <div class="mb-2"><strong>Waktu:</strong> <span id="historyDetailTime">-</span></div>
                <div class="mb-2"><strong>Alasan Reject:</strong> <span id="historyDetailReason">-</span></div>
                <div class="mb-2">
                    <strong>Link:</strong>
                    <div id="historyDetailLinks" class="mt-1 d-flex flex-wrap gap-2"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('addSlotFormSideForm');
        const list = document.getElementById('taskSlotList');
        const alertBox = document.getElementById('slotIncompleteAlert');
        const alertMessage = document.getElementById('slotIncompleteMessage');
        const errorBox = document.getElementById('addSlotFormError');
        const addSlotToggleBtn = document.getElementById('addSlotToggleBtn');
        const taskDetailCard = document.getElementById('taskDetailCard');
        const historyCard = document.getElementById('historyProgressCard');
        const historyBody = document.getElementById('historyProgressBody');
        if (!form || !list) return;

        let slotIncomplete = <?php echo json_encode($slotIncomplete, 15, 512) ?>;
        let slotAlertText = <?php echo json_encode($slotAlertMessage, 15, 512) ?>;
        const percentInput = form.querySelector('input[name="percentage"]');
        const minutesInput = form.querySelector('input[name="minutes"]');
        const durationRaw = list.getAttribute('data-duration-minutes');
        const durationMinutes = durationRaw ? parseInt(durationRaw, 10) : null;
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const round2 = (value) => Math.round(value * 100) / 100;

        const buildStatusFromDom = () => {
            let totalPercent = 0;
            let totalMinutes = 0;
            list.querySelectorAll('.slot-card').forEach((card) => {
                const pct = parseFloat(card.getAttribute('data-percentage') || '0');
                const mins = parseInt(card.getAttribute('data-minutes') || '0', 10);
                totalPercent += isNaN(pct) ? 0 : pct;
                totalMinutes += isNaN(mins) ? 0 : mins;
            });
            totalPercent = round2(totalPercent);
            if (Math.abs(totalPercent - 100) <= 0.05) {
                totalPercent = 100;
            }
            const percentComplete = Math.abs(totalPercent - 100) <= 0.01;
            const minutesComplete = durationMinutes === null ? true : totalMinutes === durationMinutes;
            const complete = percentComplete && minutesComplete;
            const missingPercent = Math.max(0, round2(100 - totalPercent));
            const missingMinutes = durationMinutes === null ? null : Math.max(0, durationMinutes - totalMinutes);
            let message = 'Komposisi slot belum lengkap. ';
            if (durationMinutes !== null) {
                message += 'Kurang ' + missingPercent + '% dan ' + missingMinutes + ' menit. Lengkapi slot terlebih dahulu.';
            } else {
                message += 'Kurang ' + missingPercent + '%. Lengkapi slot terlebih dahulu.';
            }
            return {
                complete,
                total_percent: totalPercent,
                total_minutes: totalMinutes,
                duration_minutes: durationMinutes,
                missing_percent: missingPercent,
                missing_minutes: missingMinutes,
                message,
            };
        };

        const applyStatus = (status) => {
            slotIncomplete = !status.complete;
            slotAlertText = status.message;
            if (alertMessage) {
                alertMessage.textContent = status.message;
            }
            if (alertBox) {
                alertBox.classList.toggle('d-none', status.complete);
            }
            if (addSlotToggleBtn) {
                addSlotToggleBtn.disabled = status.complete;
            }
            if (percentInput) {
                percentInput.value = status.missing_percent > 0 ? status.missing_percent : '';
            }
            if (minutesInput) {
                minutesInput.value = (status.duration_minutes !== null && status.missing_minutes > 0) ? status.missing_minutes : '';
            }
        };

        document.addEventListener('submit', function (event) {
            const target = event.target;
            if (target && target.matches('form[data-slot-submit="1"]') && slotIncomplete) {
                event.preventDefault();
                alert(slotAlertText || 'Komposisi slot belum lengkap.');
            }
        });

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            if (errorBox) {
                errorBox.classList.add('d-none');
                errorBox.textContent = '';
            }

            const formData = new FormData(form);
            let response;
            try {
                response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
            } catch (error) {
                if (errorBox) {
                    errorBox.textContent = 'Gagal mengirim data. Coba lagi.';
                    errorBox.classList.remove('d-none');
                }
                return;
            }

            let data = null;
            if (!response.ok) {
                try {
                    data = await response.json();
                } catch (error) {
                    data = null;
                }

                if (response.status === 422 && data && data.errors) {
                    const messages = [];
                    Object.values(data.errors).forEach((items) => {
                        items.forEach((item) => messages.push(item));
                    });
                    if (errorBox) {
                        errorBox.textContent = messages.join(' ');
                        errorBox.classList.remove('d-none');
                    }
                    return;
                }

                if (errorBox) {
                    const message = data && (data.message || data.error) ? (data.message || data.error) : 'Gagal menambahkan slot. Coba lagi.';
                    errorBox.textContent = message + ' (HTTP ' + response.status + ')';
                    errorBox.classList.remove('d-none');
                }
                return;
            }

            try {
                data = await response.json();
            } catch (error) {
                if (errorBox) {
                    errorBox.textContent = 'Respon server tidak valid. Coba lagi.';
                    errorBox.classList.remove('d-none');
                }
                return;
            }
            if (data.slot_html) {
                list.insertAdjacentHTML('beforeend', data.slot_html);
                const empty = document.getElementById('taskSlotEmpty');
                if (empty) {
                    empty.remove();
                }
            }
            form.reset();
            if (data.slot_status) {
                applyStatus(data.slot_status);
            } else {
                applyStatus(buildStatusFromDom());
            }
        });

        const syncHistoryHeight = () => {
            if (!taskDetailCard || !historyCard || !historyBody) return;
            const detailHeight = taskDetailCard.offsetHeight;
            const header = historyCard.querySelector('.card-header');
            const headerHeight = header ? header.offsetHeight : 0;
            const padding = 24;
            const bodyHeight = Math.max(200, detailHeight - headerHeight - padding);
            historyBody.style.maxHeight = bodyHeight + 'px';
        };

        syncHistoryHeight();
        window.addEventListener('resize', syncHistoryHeight);

        document.querySelectorAll('.history-detail-btn').forEach((item) => {
            item.addEventListener('click', () => {
                const details = item.getAttribute('data-details');
                if (!details) return;
                let payload = null;
                try {
                    payload = JSON.parse(details);
                } catch (error) {
                    payload = null;
                }
                if (!payload) return;
                const statusText = payload.status ? payload.status.charAt(0).toUpperCase() + payload.status.slice(1) : '-';
                document.getElementById('historyDetailName').textContent = payload.name || '-';
                document.getElementById('historyDetailPercent').textContent = (payload.percentage ?? '-') + '%';
                document.getElementById('historyDetailMinutes').textContent = (payload.minutes ?? '-') + ' menit';
                document.getElementById('historyDetailStatus').textContent = statusText;
                document.getElementById('historyDetailActor').textContent = payload.actor || '-';
                document.getElementById('historyDetailTime').textContent = payload.action_at || '-';
                document.getElementById('historyDetailReason').textContent = payload.rejection_reason || '-';
                const linksWrap = document.getElementById('historyDetailLinks');
                if (linksWrap) {
                    linksWrap.innerHTML = '';
                    if (payload.link) {
                        const a = document.createElement('a');
                        a.href = payload.link;
                        a.target = '_blank';
                        a.rel = 'noopener';
                        a.className = 'btn btn-sm btn-outline-primary';
                        a.textContent = 'Link';
                        linksWrap.appendChild(a);
                    } else {
                        const span = document.createElement('span');
                        span.className = 'text-muted small';
                        span.textContent = 'Belum ada link.';
                        linksWrap.appendChild(span);
                    }
                }
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\tasks\show.blade.php ENDPATH**/ ?>