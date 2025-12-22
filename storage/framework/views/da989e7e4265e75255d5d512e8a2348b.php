

<?php $__env->startSection('content'); ?>
<?php
    $canSelfEditRejected = $canSelfEditRejected ?? false;
?>
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Edit Tugas</h1>
            <p class="text-muted mb-0">
                Sesuaikan detail tugas, penugasan, atau status sesuai kebutuhan dan peran Anda.
            </p>
        </div>
        <div>
            <a href="<?php echo e(route('tasks.index')); ?>" class="text-decoration-none">Kembali</a>
        </div>
    </div>
</div>
<?php if($canSelfEditRejected): ?>
<div class="alert alert-warning">
    <strong>Tugas ditolak.</strong> Perbarui detail di bawah untuk mengajukan kembali tanpa membuat tugas baru.
    <?php if($task->approval_note): ?>
        <div class="small text-muted mt-1">Alasan penolakan: <?php echo e($task->approval_note); ?></div>
    <?php endif; ?>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Task</h3>
            </div>
            <div class="card-body">
                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <div class="fw-semibold mb-1">Validasi gagal:</div>
                        <ul class="mb-0">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form action="<?php echo e(route('tasks.update', $task)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <?php if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi') || $canSelfEditRejected): ?>
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text"  class="form-control" id="title" name="title" value="<?php echo e(old('title', $task->title)); ?>" >
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control"  id="description" name="description" rows="3"><?php echo e(old('description', $task->description)); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="mb-3">
                        <label for="duration_minutes" class="form-label">Durasi (menit)</label>
                        <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" min="1" value="<?php echo e(old('duration_minutes', $task->duration_minutes)); ?>">
                        <small class="text-muted">Total menit yang akan dibagi ke slot progres. Due date tetap sebagai target akhir.</small>
                        <?php $__errorArgs = ['duration_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign To</label>
                        <?php if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')): ?>
                            <div class="assigned-to-dropdown position-relative">
                                <input type="hidden" name="assigned_to" id="assigned_to" value="<?php echo e(old('assigned_to', $task->assigned_to)); ?>" required>
                                <button type="button" class="form-select text-start assigned-to-toggle" data-placeholder="-- Pilih user --">-- Pilih user --</button>
                                <div class="assigned-to-panel card shadow-sm p-2 d-none" style="position:absolute; z-index:1000; width:100%; left:0; top:100%; border:1px solid #dee2e6;">
                                    <input type="text" class="form-control form-control-sm mb-2 assigned-to-filter" placeholder="Cari nama/email...">
                                    <div class="list-group assigned-to-list" style="max-height:220px; overflow:auto;">
                                        <button type="button" class="list-group-item list-group-item-action" data-user-id="" data-user-label="-- Pilih user --">-- Pilih user --</button>
                                        <button type="button" class="list-group-item list-group-item-action" data-user-id="<?php echo e(auth()->id()); ?>" data-user-label="-- Assign to Myself (<?php echo e(auth()->user()->name); ?>) --">-- Assign to Myself (<?php echo e(auth()->user()->name); ?>) --</button>
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <button type="button" class="list-group-item list-group-item-action" data-user-id="<?php echo e($user->id); ?>" data-user-label="<?php echo e($user->name); ?> @ <?php echo e($user->email); ?>"><?php echo e($user->name); ?> @ <?php echo e($user->email); ?></button>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                            <?php $__errorArgs = ['assigned_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php else: ?>
                            <div class="form-control-plaintext px-0">
                                <?php echo e(auth()->user()->name); ?> (tidak bisa diubah)
                            </div>
                            <input type="hidden" name="assigned_to" value="<?php echo e($task->assigned_to); ?>">
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo e(old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '')); ?>">
                        <?php $__errorArgs = ['due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <p class="text-muted">Kirim bukti progres menggunakan link pada slot, tanpa upload file.</p>
                    <hr>
                    <h5 class="mb-2">Slot Progres (opsional, total % harus 100%)</h5>
                    <?php $slots = old('slots', $task->slots->toArray()); ?>
                    <div id="slotListEdit" data-initial-count="<?php echo e(count($slots)); ?>">
                        <?php $__empty_1 = true; $__currentLoopData = $slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="row g-2 mb-2 slot-row">
                            <div class="col-md-4">
                                <input type="text" name="slots[<?php echo e($i); ?>][name]" class="form-control" value="<?php echo e($slot['name'] ?? ''); ?>" placeholder="Nama / Tujuan">
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="slots[<?php echo e($i); ?>][percentage]" class="form-control" min="0.01" max="100" step="0.01" value="<?php echo e($slot['percentage'] ?? ''); ?>" placeholder="%">
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="slots[<?php echo e($i); ?>][minutes]" class="form-control" min="1" value="<?php echo e($slot['minutes'] ?? ''); ?>" placeholder="Menit">
                            </div>
                            <div class="col-md-2 d-flex align-items-center gap-2">
                                <input type="number" name="slots[<?php echo e($i); ?>][order]" class="form-control" min="0" value="<?php echo e($slot['order'] ?? $i); ?>">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-slot">X</button>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="row g-2 mb-2 slot-row">
                            <div class="col-md-4">
                                <input type="text" name="slots[0][name]" class="form-control" placeholder="Nama / Tujuan">
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="slots[0][percentage]" class="form-control" min="0.01" max="100" step="0.01" placeholder="%">
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="slots[0][minutes]" class="form-control" min="1" placeholder="Menit">
                            </div>
                            <div class="col-md-2 d-flex align-items-center gap-2">
                                <input type="number" name="slots[0][order]" class="form-control" min="0" value="0">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-slot">X</button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div id="slotSummaryEdit" class="small text-muted mb-2"></div>
                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addSlotBtnEdit">Tambah Slot</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSlotsBtnEdit">Hapus Semua Slot</button>
                        <span class="small text-muted ms-2">Total persentase harus 100%, total menit harus sama dengan durasi (jika diisi).</span>
                    </div>

                    <p class="text-muted">Progres dihitung dari slot yang disetujui. Unggah bukti per slot di halaman detail.</p>
                    <button type="submit" class="btn btn-primary">Update Task</button>
                    <a href="<?php echo e(route('tasks.index')); ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
  (function() {
    const dropdowns = document.querySelectorAll('.assigned-to-dropdown');
    dropdowns.forEach(function(dropdown) {
      const hiddenInput = dropdown.querySelector('input[type="hidden"]');
      const toggle = dropdown.querySelector('.assigned-to-toggle');
      const panel = dropdown.querySelector('.assigned-to-panel');
      const filterInput = dropdown.querySelector('.assigned-to-filter');
      const list = dropdown.querySelector('.assigned-to-list');
      if (!hiddenInput || !toggle || !panel || !filterInput || !list) return;

      const initId = hiddenInput.value;
      const currentBtn = initId ? list.querySelector('[data-user-id="' + initId + '"]') : null;
      if (currentBtn) {
        toggle.textContent = currentBtn.getAttribute('data-user-label');
      } else {
        toggle.textContent = toggle.getAttribute('data-placeholder') || '-- Pilih user --';
      }

      const closePanel = () => panel.classList.add('d-none');
      const openPanel = () => {
        panel.classList.remove('d-none');
        filterInput.focus();
      };

      toggle.addEventListener('click', function() {
        if (panel.classList.contains('d-none')) {
          openPanel();
        } else {
          closePanel();
        }
      });

      filterInput.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        list.querySelectorAll('[data-user-id]').forEach(function(btn) {
          const text = btn.textContent.toLowerCase();
          btn.classList.toggle('d-none', term && !text.includes(term));
        });
      });

      list.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-user-id]');
        if (!btn) return;
        hiddenInput.value = btn.getAttribute('data-user-id');
        toggle.textContent = btn.getAttribute('data-user-label');
        closePanel();
      });

      document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target)) {
          closePanel();
        }
      });
    });

    const initSlotProgress = ({ slotListId, addBtnId, clearBtnId, summaryId, durationInputId = 'duration_minutes' }) => {
      const slotList = document.getElementById(slotListId);
      const addBtn = document.getElementById(addBtnId);
      const clearBtn = document.getElementById(clearBtnId);
      const durationInput = document.getElementById(durationInputId);
      const slotSummary = document.getElementById(summaryId);
      if (!slotList || !addBtn || !clearBtn) return;
      let idx = parseInt(slotList.getAttribute('data-initial-count') || slotList.querySelectorAll('.slot-row').length || 0);
      let isSyncing = false;

      const getDuration = () => {
        const val = parseFloat(durationInput?.value);
        return isNaN(val) || val <= 0 ? null : val;
      };

      const formatPct = (val, decimals = 2) => {
        if (!isFinite(val)) return '';
        const factor = Math.pow(10, decimals);
        const rounded = Math.round(val * factor) / factor;
        return Math.abs(rounded) < 0.01 ? 0 : rounded;
      };

      const updateSummary = () => {
        if (!slotSummary) return;
        let totalPct = 0;
        let totalMinutes = 0;
        slotList.querySelectorAll('.slot-row').forEach((row) => {
          const pct = parseFloat(row.querySelector('input[name$=\"[percentage]\"]')?.value);
          const min = parseFloat(row.querySelector('input[name$=\"[minutes]\"]')?.value);
          if (!isNaN(pct)) totalPct += pct;
          if (!isNaN(min)) totalMinutes += min;
        });
        totalPct = parseFloat(totalPct.toFixed(2));
        const durationVal = getDuration();
        let remainingPct = 100 - totalPct;
        if (Math.abs(remainingPct) < 0.01) remainingPct = 0;
        let remainingMin = durationVal !== null ? durationVal - totalMinutes : null;
        if (remainingMin !== null && Math.abs(remainingMin) < 0.01) remainingMin = 0;
        slotSummary.textContent = [
          `Total %: ${formatPct(totalPct)} / 100` + (remainingPct ? ` (sisa ${formatPct(remainingPct)})` : ''),
          durationVal !== null
            ? `Total menit: ${totalMinutes} / ${durationVal}` + (remainingMin !== null ? ` (sisa ${remainingMin})` : '')
            : `Total menit: ${totalMinutes}`
        ].join(' | ');
      };

      const syncRow = (row, from) => {
        if (isSyncing) return;
        const pctInput = row.querySelector('input[name$=\"[percentage]\"]');
        const minInput = row.querySelector('input[name$=\"[minutes]\"]');
        if (!pctInput || !minInput) return;
        const durationVal = getDuration();
        isSyncing = true;
        if (from === 'percentage') {
          const pct = parseFloat(pctInput.value);
          if (durationVal && !isNaN(pct)) {
            const minutes = Math.round((pct / 100) * durationVal);
            minInput.value = minutes || '';
          } else if (!durationVal) {
            minInput.value = '';
          }
          row.dataset.lastSource = 'percentage';
        } else if (from === 'minutes') {
          const mins = parseFloat(minInput.value);
          if (durationVal && !isNaN(mins)) {
            const pct = (mins / durationVal) * 100;
            pctInput.value = formatPct(pct) || '';
          } else if (!durationVal) {
            pctInput.value = '';
          }
          row.dataset.lastSource = 'minutes';
        } else if (from === 'duration-change') {
          const pctVal = parseFloat(pctInput.value);
          const minVal = parseFloat(minInput.value);
          if (durationVal && !isNaN(pctVal)) {
            const minutes = Math.round((pctVal / 100) * durationVal);
            minInput.value = minutes || '';
          } else if (durationVal && isNaN(pctVal) && !isNaN(minVal)) {
            const pct = (minVal / durationVal) * 100;
            pctInput.value = formatPct(pct) || '';
          }
        }
        isSyncing = false;
        updateSummary();
      };

      const attachSlotSync = (row) => {
        const pctInput = row.querySelector('input[name$=\"[percentage]\"]');
        const minInput = row.querySelector('input[name$=\"[minutes]\"]');
        if (!pctInput || !minInput) return;
        pctInput.addEventListener('input', () => syncRow(row, 'percentage'));
        minInput.addEventListener('input', () => syncRow(row, 'minutes'));
      };

      slotList.querySelectorAll('.slot-row').forEach((row) => attachSlotSync(row));

      if (durationInput) {
        durationInput.addEventListener('input', () => {
          slotList.querySelectorAll('.slot-row').forEach((row) => syncRow(row, 'duration-change'));
          updateSummary();
        });
      }

      const addSlotRow = () => {
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 slot-row';
        row.innerHTML = `
          <div class=\"col-md-4\">
            <input type=\"text\" name=\"slots[${idx}][name]\" class=\"form-control\" placeholder=\"Nama / Tujuan\">
          </div>
          <div class=\"col-md-3\">
            <input type=\"number\" name=\"slots[${idx}][percentage]\" class=\"form-control\" min=\"0.01\" max=\"100\" step=\"0.01\" placeholder=\"%\">
          </div>
          <div class=\"col-md-3\">
            <input type=\"number\" name=\"slots[${idx}][minutes]\" class=\"form-control\" min=\"1\" placeholder=\"Menit\">
          </div>
          <div class=\"col-md-2 d-flex align-items-center gap-2\">
            <input type=\"number\" name=\"slots[${idx}][order]\" class=\"form-control\" min=\"0\" value=\"${idx}\">
            <button type=\"button\" class=\"btn btn-sm btn-outline-danger remove-slot\">X</button>
          </div>
        `;
        slotList.appendChild(row);
        attachSlotSync(row);
        updateSummary();
        idx++;
      };

      addBtn.addEventListener('click', addSlotRow);

      slotList.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-slot')) {
          e.preventDefault();
          const row = e.target.closest('.slot-row');
          if (row) row.remove();
          updateSummary();
        }
      });

      clearBtn.addEventListener('click', () => {
        slotList.innerHTML = '';
        idx = 0;
        updateSummary();
      });

      updateSummary();
    };

    initSlotProgress({
      slotListId: 'slotListEdit',
      addBtnId: 'addSlotBtnEdit',
      clearBtnId: 'clearSlotsBtnEdit',
      summaryId: 'slotSummaryEdit'
    });
  })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/tasks/edit.blade.php ENDPATH**/ ?>