@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Buat Tugas untuk Diri Sendiri</h1>
            <p class="text-muted mb-0">Catat tugas pribadi dan pantau progresnya.</p>
        </div>
        <div>
            <a href="{{ route('tasks.index') }}" class="text-decoration-none">Kembali</a>
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
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <div class="fw-semibold mb-1">Validasi gagal:</div>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="alert alert-warning">
                    Tugas yang Anda buat untuk diri sendiri akan menunggu persetujuan:
                    Admin Lokasi (jika ada) atau Super Admin. Anda baru bisa update progres setelah disetujui.
                </div>
                <form action="{{ route('tasks.store.self') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" id="description" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="task_catalog_id" class="form-label">Task Catalog</label>
                        <select name="task_catalog_id" id="task_catalog_id" class="form-select" required>
                            <option value="">-- Pilih Task Catalog --</option>
                            @foreach($catalogs ?? [] as $catalog)
                                <option value="{{ $catalog->id }}" data-unit="{{ $catalog->unit }}" data-value="{{ $catalog->value }}" @selected(old('task_catalog_id') == $catalog->id)>
                                    {{ $catalog->name }} ({{ $catalog->unit }} {{ $catalog->value }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hanya catalog sesuai jobdesk Anda yang ditampilkan.</small>
                    </div>
                    <div class="mb-3" id="durationField">
                        <label for="duration_minutes" class="form-label">Durasi (menit)</label>
                        <input type="number" name="duration_minutes" class="form-control" id="duration_minutes" min="1" placeholder="Misal 240 untuk 4 jam" value="{{ old('duration_minutes') }}">
                        <small class="text-muted" id="durationHelp">Total menit yang akan dibagi ke slot progres. Due date tetap target akhir.</small>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" id="due_date" value="{{ old('due_date') }}">
                    </div>
                    <p class="text-muted">Progres tugas pribadi diupdate lewat halaman detail menggunakan bukti berupa link.</p>
                    <hr>
                    <h5 class="mb-2">Slot Progres (wajib, total % = 100%)</h5>
                    @php
                        $oldSlots = old('slots', [['name' => '', 'percentage' => '', 'minutes' => '', 'order' => 0]]);
                    @endphp
                    <div id="slotListSelf" data-initial-count="{{ count($oldSlots) }}">
                        @foreach($oldSlots as $idx => $slot)
                            <div class="row g-2 mb-2 slot-row">
                                <div class="col-md-4">
                                    <label class="form-label">Nama / Tujuan</label>
                                    <input type="text" name="slots[{{ $idx }}][name]" class="form-control" placeholder="Contoh: Riset" required value="{{ $slot['name'] }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Persentase (%)</label>
                                    <input type="number" name="slots[{{ $idx }}][percentage]" class="form-control" min="0.01" max="100" step="0.01" placeholder="25" required value="{{ $slot['percentage'] }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Menit</label>
                                    <input type="number" name="slots[{{ $idx }}][minutes]" class="form-control" min="1" placeholder="60" required value="{{ $slot['minutes'] }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Urutan</label>
                                    <input type="number" name="slots[{{ $idx }}][order]" class="form-control" min="0" value="{{ $slot['order'] ?? $idx }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div id="slotSummarySelf" class="small text-muted mb-2"></div>
                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addSlotBtnSelf">Tambah Slot</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSlotsBtnSelf">Hapus Semua Slot</button>
                        <span class="small text-muted ms-2">Minimal 1 slot. Total persentase harus 100%, total menit harus sama dengan durasi (jika diisi).</span>
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
          durationHelp.textContent = 'Total menit yang akan dibagi ke slot progres. Due date tetap target akhir.';
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
          durationHelp.textContent = `Durasi otomatis: ${value} point x ${POINT_TO_MINUTES} menit = ${minutes} menit.`;
        }
      } else {
        if (value && (!durationInput.value || parseFloat(durationInput.value) <= 0)) {
          durationInput.value = Math.round(value);
        }
        durationInput.readOnly = false;
        if (durationHelp) {
          durationHelp.textContent = 'Total menit yang akan dibagi ke slot progres. Due date tetap target akhir.';
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
          <div class="col-md-4">
            <input type="text" name="slots[${idx}][name]" class="form-control" placeholder="Nama / Tujuan" required>
          </div>
          <div class="col-md-3">
            <input type="number" name="slots[${idx}][percentage]" class="form-control" min="0.01" max="100" step="0.01" placeholder="%" required>
          </div>
          <div class="col-md-3">
            <input type="number" name="slots[${idx}][minutes]" class="form-control" min="1" placeholder="Menit" required>
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
@endsection
