<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Create Task for Yourself</h1>
            <p class="text-muted mb-0">Record personal tasks and monitor their progress.</p>
        </div>
        <div>
            <a href="<?php echo e(route('tasks.index')); ?>" class="text-decoration-none">Back</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Task for Yourself</h3>
            </div>
            <div class="card-body">
                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <div class="fw-semibold mb-1">Validation failed:</div>
                        <ul class="mb-0">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <div class="alert alert-warning">
                    Tasks created for yourself will await approval:
                    Location Admin (if any) or Super Admin. You can update progress after it is approved.
                </div>
                <form action="<?php echo e(route('tasks.store.self')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" id="title" value="<?php echo e(old('title')); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" id="description" rows="3"><?php echo e(old('description')); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="task_catalog_id" class="form-label">Task Catalog</label>
                        <select name="task_catalog_id" id="task_catalog_id" class="form-select" required>
                            <option value="">-- Select Task Catalog --</option>
                            <?php $__currentLoopData = $catalogs ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catalog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($catalog->id); ?>" data-unit="<?php echo e($catalog->unit); ?>" data-value="<?php echo e($catalog->value); ?>" <?php if(old('task_catalog_id') == $catalog->id): echo 'selected'; endif; ?>>
                                    <?php echo e($catalog->name); ?> (<?php echo e($catalog->unit); ?> <?php echo e($catalog->value); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="text-muted">Only catalogs corresponding to your jobdesk are displayed.</small>
                    </div>
                    <div class="mb-3" id="durationField">
                        <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" class="form-control" id="duration_minutes" min="1" placeholder="e.g., 240 for 4 hours" value="<?php echo e(old('duration_minutes')); ?>">
                        <small class="text-muted" id="durationHelp">Total minutes to be divided into progress slots. Due date remains the final target.</small>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" id="due_date" value="<?php echo e(old('due_date')); ?>">
                    </div>
                    <p class="text-muted">Personal task progress is updated via the detail page using a link as evidence.</p>
                    <hr>
                    <h5 class="mb-2">Progress Slots (required, total % = 100%)</h5>
                    <?php
                        $oldSlots = old('slots', [['name' => '', 'percentage' => '', 'minutes' => '', 'order' => 0]]);
                    ?>
                    <div id="slotListSelf" data-initial-count="<?php echo e(count($oldSlots)); ?>">
                        <?php $__currentLoopData = $oldSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="row g-2 mb-2 slot-row">
                                <div class="col-md-4">
                                    <label class="form-label">Name / Goal</label>
                                    <input type="text" name="slots[<?php echo e($idx); ?>][name]" class="form-control" placeholder="Example: Research" required value="<?php echo e($slot['name']); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Percentage (%)</label>
                                    <input type="number" name="slots[<?php echo e($idx); ?>][percentage]" class="form-control" min="0.01" max="100" step="0.01" placeholder="25" required value="<?php echo e($slot['percentage']); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Minutes</label>
                                    <input type="number" name="slots[<?php echo e($idx); ?>][minutes]" class="form-control" min="1" placeholder="60" required value="<?php echo e($slot['minutes']); ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Order</label>
                                    <input type="number" name="slots[<?php echo e($idx); ?>][order]" class="form-control" min="0" value="<?php echo e($slot['order'] ?? $idx); ?>">
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div id="slotSummarySelf" class="small text-muted mb-2"></div>
                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addSlotBtnSelf">Add Slot</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSlotsBtnSelf">Clear All Slots</button>
                        <span class="small text-muted ms-2">Minimum 1 slot. Total percentage must be 100%, total minutes must equal duration (if provided).</span>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Task</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
  (function() {
    const POINT_TO_MINUTES = 30;
    const catalogSelect = document.getElementById('task_catalog_id');
    const durationInput = document.getElementById('duration_minutes');
    const durationHelp = document.getElementById('durationHelp');

    const applyCatalogDuration = () => {
      if (!catalogSelect || !durationInput) return;
      const option = catalogSelect.selectedOptions?.[0];
      if (!option || !option.value) {
        durationInput.readOnly = false;
        if (durationHelp) {
          durationHelp.textContent = 'Total minutes to be divided into progress slots. Due date remains the final target.';
        }
        return;
      }
      const unit = (option.getAttribute('data-unit') || '').toLowerCase();
      const value = parseFloat(option.getAttribute('data-value') || '0');
      if (unit === 'points') {
        const minutes = Math.round(value * POINT_TO_MINUTES);
        durationInput.value = minutes || '';
        durationInput.readOnly = true;
        if (durationHelp) {
          durationHelp.textContent = `Automatic duration: ${value} point x ${POINT_TO_MINUTES} minutes = ${minutes} minutes.`;
        }
      } else {
        if (value && (!durationInput.value || parseFloat(durationInput.value) <= 0)) {
          durationInput.value = Math.round(value);
        }
        durationInput.readOnly = false;
        if (durationHelp) {
          durationHelp.textContent = 'Total minutes to be divided into progress slots. Due date remains the final target.';
        }
      }
    };

    if (catalogSelect) {
      catalogSelect.addEventListener('change', applyCatalogDuration);
      applyCatalogDuration();
    }

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
          const pct = parseFloat(row.querySelector('input[name$="[percentage]"]')?.value);
          const min = parseFloat(row.querySelector('input[name$="[minutes]"]')?.value);
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
          `Total %: ${formatPct(totalPct)} / 100` + (remainingPct ? ` (remaining ${formatPct(remainingPct)})` : ''),
          durationVal !== null
            ? `Total minutes: ${totalMinutes} / ${durationVal}` + (remainingMin !== null ? ` (remaining ${remainingMin})` : '')
            : `Total minutes: ${totalMinutes}`
        ].join(' | ');
      };

      const syncRow = (row, from) => {
        if (isSyncing) return;
        const pctInput = row.querySelector('input[name$="[percentage]"]');
        const minInput = row.querySelector('input[name$="[minutes]"]');
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
        const pctInput = row.querySelector('input[name$="[percentage]"]');
        const minInput = row.querySelector('input[name$="[minutes]"]');
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
          <div class="col-md-4">
            <input type="text" name="slots[${idx}][name]" class="form-control" placeholder="Name / Goal" required>
          </div>
          <div class="col-md-3">
            <input type="number" name="slots[${idx}][percentage]" class="form-control" min="0.01" max="100" step="0.01" placeholder="%" required>
          </div>
          <div class="col-md-3">
            <input type="number" name="slots[${idx}][minutes]" class="form-control" min="1" placeholder="Minutes" required>
          </div>
          <div class="col-md-2 d-flex align-items-center gap-2">
            <input type="number" name="slots[${idx}][order]" class="form-control" min="0" value="${idx}">
            <button type="button" class="btn btn-sm btn-outline-danger remove-slot">X</button>
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
        addSlotRow();
      });

      updateSummary();
    };

    initSlotProgress({
      slotListId: 'slotListSelf',
      addBtnId: 'addSlotBtnSelf',
      clearBtnId: 'clearSlotsBtnSelf',
      summaryId: 'slotSummarySelf'
    });
  })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/tasks/create-self.blade.php ENDPATH**/ ?>