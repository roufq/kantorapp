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
                  <input type="number" name="slots[{{ $idx }}][percentage]" class="form-control" min="1" max="100" placeholder="25" required value="{{ $slot['percentage'] }}">
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
    const slotList = document.getElementById('slotListLoc');
    const addBtn = document.getElementById('addSlotBtnLoc');
    const clearBtn = document.getElementById('clearSlotsBtnLoc');
    if (!slotList || !addBtn || !clearBtn) return;
    let idx = parseInt(slotList.getAttribute('data-initial-count') || slotList.querySelectorAll('.slot-row').length || 0);

    const addSlotRow = () => {
      const row = document.createElement('div');
      row.className = 'row g-2 mb-2 slot-row';
      row.innerHTML = `
        <div class="col-md-4">
          <input type="text" name="slots[${idx}][name]" class="form-control" placeholder="Nama / Tujuan" required>
        </div>
        <div class="col-md-3">
          <input type="number" name="slots[${idx}][percentage]" class="form-control" min="1" max="100" placeholder="%" required>
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
      idx++;
    };

    addBtn.addEventListener('click', addSlotRow);

    slotList.addEventListener('click', (e) => {
      if (e.target.classList.contains('remove-slot')) {
        e.preventDefault();
        const row = e.target.closest('.slot-row');
        if (row) row.remove();
      }
    });

    clearBtn.addEventListener('click', () => {
      slotList.innerHTML = '';
      idx = 0;
      addSlotRow();
    });
  })();
</script>
@endsection
