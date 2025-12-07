@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Edit Penugasan Shift</h1>
            <p class="text-muted mb-0">Perbarui shift, status, atau catatan penugasan karyawan.</p>
        </div>
        <div>
            <a href="{{ route('shift-assignments.index') }}" class="text-decoration-none">Kembali</a>
        </div>
    </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Edit Shift Assignment</h3></div>
      <div class="card-body">
        <form method="POST" action="{{ route('shift-assignments.update', $assignment) }}">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label class="form-label">User</label>
            <select name="user_id" id="user_id" class="form-control" required>
              @foreach($users as $u)
                <option value="{{ $u->id }}" @if(old('user_id', $assignment->user_id)===$u->id) selected @endif>{{ $u->name }} (ID: {{ $u->id }})</option>
              @endforeach
            </select>
            <div class="form-text" id="location_hint">Pilih user untuk melihat lokasi & shift yang tersedia.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Shift</label>
            <select name="location_shift_id" id="location_shift_id" class="form-control" required></select>
            <div class="form-text" id="shift_slots_hint">Hanya shift yang terhubung ke lokasi user yang akan tampil.</div>
            <div class="mt-2">
              <small class="text-muted me-2">Quick Templates:</small>
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="pickShiftByName('Pagi')">Pagi</button>
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="pickShiftByName('Siang')">Siang</button>
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="pickShiftByName('Malam')">Malam</button>
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="pickShiftByName('24')">24H</button>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" class="form-control" name="date" value="{{ $assignment->date->format('Y-m-d') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              @foreach(['scheduled','cancelled','completed'] as $st)
              <option value="{{ $st }}" @if($assignment->status===$st) selected @endif>{{ ucfirst($st) }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <input type="text" class="form-control" name="notes" value="{{ $assignment->notes }}">
          </div>
          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="handover_required" name="handover_required" value="1" {{ $assignment->handover_required ? 'checked' : '' }}>
            <label class="form-check-label" for="handover_required">Handover Required</label>
          </div>
          <div class="mb-3">
            <label class="form-label">Handover Note</label>
            <textarea class="form-control" name="handover_note" rows="2">{{ $assignment->handover_note }}</textarea>
          </div>
          <button class="btn btn-primary">Update</button>
          <a class="btn btn-secondary" href="{{ route('shift-assignments.index') }}">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
  const shiftOptions = @json($shiftOptions);
  const userLocations = @json($userLocations);
  const locationNames = @json($locationNames);
  const currentLocationShiftId = @json(old('location_shift_id', $assignment->location_shift_id));

  const userSelect = document.getElementById('user_id');
  const shiftSelect = document.getElementById('location_shift_id');
  const locationHint = document.getElementById('location_hint');
  const shiftHint = document.getElementById('shift_slots_hint');

  function renderSlotsHint(option) {
    if (!option) {
      shiftHint.textContent = 'Pilih user untuk menampilkan shift.';
      return;
    }
    const slots = option.dataset.slots ? JSON.parse(option.dataset.slots) : [];
    if (!slots.length) {
      shiftHint.textContent = 'Shift belum memiliki slot waktu di lokasi ini.';
      return;
    }
    const parts = slots.map(s => {
      const dayLabel = s.days && s.days.length ? ` (${s.days.join(', ')})` : '';
      return `${s.start} - ${s.end}${dayLabel}`;
    });
    shiftHint.textContent = `Slot: ${parts.join('; ')}`;
  }

  function refreshShiftOptions() {
    const userId = userSelect.value;
    const locId = userLocations[userId] ?? null;
    const locationName = locId ? (locationNames[locId] ?? `Lokasi #${locId}`) : 'Tidak ada lokasi';
    locationHint.textContent = locId ? `Lokasi: ${locationName}` : 'User belum memiliki lokasi.';

    shiftSelect.innerHTML = '';
    if (!locId || !shiftOptions[locId] || shiftOptions[locId].length === 0) {
      const opt = document.createElement('option');
      opt.value = '';
      opt.textContent = locId ? 'Tidak ada shift aktif untuk lokasi ini' : 'Pilih user dengan lokasi';
      shiftSelect.appendChild(opt);
      shiftSelect.disabled = true;
      renderSlotsHint(null);
      return;
    }

    shiftSelect.disabled = false;
    shiftOptions[locId].forEach(item => {
      const opt = document.createElement('option');
      opt.value = item.location_shift_id;
      opt.textContent = item.name;
      opt.dataset.slots = JSON.stringify(item.slots || []);
      if (String(currentLocationShiftId) === String(item.location_shift_id)) {
        opt.selected = true;
      }
      shiftSelect.appendChild(opt);
    });
    renderSlotsHint(shiftSelect.selectedOptions[0] || shiftSelect.options[0]);
  }

  function pickShiftByName(keyword) {
    const kw = keyword.toLowerCase();
    for (const opt of shiftSelect.options) {
      if (opt.text.toLowerCase().includes(kw)) {
        shiftSelect.value = opt.value;
        renderSlotsHint(opt);
        break;
      }
    }
  }

  userSelect.addEventListener('change', refreshShiftOptions);
  shiftSelect.addEventListener('change', () => renderSlotsHint(shiftSelect.selectedOptions[0]));
  refreshShiftOptions();
</script>
@endsection
