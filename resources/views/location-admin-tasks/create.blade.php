@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
      <h1 class="h3 mb-1">Assign Tugas Lokasi</h1>
      <p class="text-muted mb-0">Buat tugas untuk karyawan/Admin Lokasi di lokasi Anda.</p>
    </div>
    <div>
      <a href="{{ route('location-admin-tasks.index') }}" class="text-decoration-none">Kembali</a>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Assign Task (Location)</h3></div>
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
        <form action="{{ route('location-admin-tasks.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
            @error('title')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            @error('description')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="duration_minutes" class="form-label">Durasi (menit)</label>
            <input type="number" name="duration_minutes" id="duration_minutes" class="form-control" min="1" placeholder="Misal 240 untuk 4 jam" value="{{ old('duration_minutes') }}">
            <small class="text-muted">Total menit yang akan dibagi ke slot progres. Due date tetap target akhir.</small>
            @error('duration_minutes')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="assigned_to" class="form-label">Assign To (Employees in my location)</label>
            <select name="assigned_to" id="assigned_to" class="form-select" required>
              @foreach($users as $u)
                <option value="{{ $u->id }}" {{ (string)old('assigned_to') === (string)$u->id ? 'selected' : '' }}>{{ $u->name }}</option>
              @endforeach
            </select>
            @error('assigned_to')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="due_date" class="form-label">Due Date</label>
            <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date') }}">
            @error('due_date')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="photo" class="form-label">Photo (Optional)</label>
            <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
            @error('photo')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="document" class="form-label">Document (Optional)</label>
            <input type="file" name="document" id="document" class="form-control" accept=".pdf,.doc,.docx,.txt">
            @error('document')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <hr>
          <h5 class="mb-2">Slot Progres (wajib, total % = 100%)</h5>
          @php
            $oldSlots = old('slots', [['name' => '', 'percentage' => '', 'minutes' => '', 'order' => 0]]);
          @endphp
          <div id="slotListLoc" data-initial-count="{{ count($oldSlots) }}">
            @foreach($oldSlots as $idx => $slot)
              <div class="row g-2 mb-2 slot-row">
                <div class="col-md-4">
                  <label class="form-label">Nama / Tujuan</label>
                  <input type="text" name="slots[{{ $idx }}][name]" class="form-control" placeholder="Contoh: Analisa" required value="{{ $slot['name'] }}">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Persentase (%)</label>
                  <input type="number" name="slots[{{ $idx }}][percentage]" class="form-control" min="0.01" max="100" step="0.01" placeholder="25" required value="{{ $slot['percentage'] }}">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Menit</label>
                  <input type="number" name="slots[{{ $idx }}][minutes]" class="form-control" min="1" placeholder="60" required value="{{ $slot['minutes'] }}">
                </div>
                <div class="col-md-2 d-flex align-items-center gap-2">
                  <input type="number" name="slots[{{ $idx }}][order]" class="form-control" min="0" value="{{ $slot['order'] ?? $idx }}">
                  <button type="button" class="btn btn-sm btn-outline-danger remove-slot">X</button>
                </div>
              </div>
            @endforeach
          </div>
          <div id="slotSummaryLoc" class="small text-muted mb-2"></div>
          <div class="d-flex gap-2 mb-3">
            <button type="button" class="btn btn-sm btn-outline-primary" id="addSlotBtnLoc">Tambah Slot</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSlotsBtnLoc">Hapus Semua Slot</button>
            <span class="small text-muted ms-2">Minimal 1 slot. Total persentase harus 100%, total menit harus sama dengan durasi (jika diisi).</span>
          </div>
          <button type="submit" class="btn btn-primary">Create Task</button>
          <a href="{{ route('location-admin-tasks.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
  (function() {
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
            <input type=\"text\" name=\"slots[${idx}][name]\" class=\"form-control\" placeholder=\"Nama / Tujuan\" required>
          </div>
          <div class=\"col-md-3\">
            <input type=\"number\" name=\"slots[${idx}][percentage]\" class=\"form-control\" min=\"0.01\" max=\"100\" step=\"0.01\" placeholder=\"%\" required>
          </div>
          <div class=\"col-md-3\">
            <input type=\"number\" name=\"slots[${idx}][minutes]\" class=\"form-control\" min=\"1\" placeholder=\"Menit\" required>
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
        addSlotRow();
      });

      updateSummary();
    };

    initSlotProgress({
      slotListId: 'slotListLoc',
      addBtnId: 'addSlotBtnLoc',
      clearBtnId: 'clearSlotsBtnLoc',
      summaryId: 'slotSummaryLoc'
    });
  })();
</script>
@endsection
