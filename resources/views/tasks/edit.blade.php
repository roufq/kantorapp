@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Edit Tugas</h1>
            <p class="text-muted mb-0">
                Sesuaikan detail tugas, penugasan, atau status sesuai kebutuhan dan peran Anda.
            </p>
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
                <h3 class="card-title">Edit Task</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi'))
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text"  class="form-control" id="title" name="title" value="{{ old('title', $task->title) }}" >
                        @error('title')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control"  id="description" name="description" rows="3">{{ old('description', $task->description) }}</textarea>
                        @error('description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="duration_minutes" class="form-label">Durasi (menit)</label>
                        <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" min="1" value="{{ old('duration_minutes', $task->duration_minutes) }}">
                        <small class="text-muted">Total menit yang akan dibagi ke slot progres. Due date tetap sebagai target akhir.</small>
                        @error('duration_minutes')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign To</label>
                        <div class="assigned-to-dropdown position-relative">
                            <input type="hidden" name="assigned_to" id="assigned_to" value="{{ old('assigned_to', $task->assigned_to) }}" required>
                            <button type="button" class="form-select text-start assigned-to-toggle" data-placeholder="-- Pilih user --">-- Pilih user --</button>
                            <div class="assigned-to-panel card shadow-sm p-2 d-none" style="position:absolute; z-index:1000; width:100%; left:0; top:100%; border:1px solid #dee2e6;">
                                <input type="text" class="form-control form-control-sm mb-2 assigned-to-filter" placeholder="Cari nama/email...">
                                <div class="list-group assigned-to-list" style="max-height:220px; overflow:auto;">
                                    <button type="button" class="list-group-item list-group-item-action" data-user-id="" data-user-label="-- Pilih user --">-- Pilih user --</button>
                                    <button type="button" class="list-group-item list-group-item-action" data-user-id="{{ auth()->id() }}" data-user-label="— Assign to Myself ({{ auth()->user()->name }}) —">— Assign to Myself ({{ auth()->user()->name }}) —</button>
                                    @foreach($users as $user)
                                        <button type="button" class="list-group-item list-group-item-action" data-user-id="{{ $user->id }}" data-user-label="{{ $user->name }} @ {{ $user->email }}">{{ $user->name }} @ {{ $user->email }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @error('assigned_to')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
                        @error('due_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo (Optional)</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        @if($task->photo_path)
                            <div class="mt-2">
                                <small class="text-muted">Current photo: <a href="{{ route('tasks.download.photo', $task) }}" target="_blank">Download</a></small>
                            </div>
                        @endif
                        @error('photo')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Document (Optional)</label>
                        <input type="file" class="form-control" id="document" name="document" accept=".pdf,.doc,.docx,.txt">
                        @if($task->document_path)
                            <div class="mt-2">
                                <small class="text-muted">Current document: <a href="{{ route('tasks.download.document', $task) }}" target="_blank">Download</a></small>
                            </div>
                        @endif
                        @error('document')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <hr>
                    <h5 class="mb-2">Slot Progres (opsional, total % harus 100%)</h5>
                    <div id="slotListEdit">
                        @php $slots = old('slots', $task->slots->toArray()); @endphp
                        @forelse($slots as $i => $slot)
                        <div class="row g-2 mb-2 slot-row">
                            <div class="col-md-4">
                                <input type="text" name="slots[{{ $i }}][name]" class="form-control" value="{{ $slot['name'] ?? '' }}" placeholder="Nama / Tujuan">
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="slots[{{ $i }}][percentage]" class="form-control" min="1" max="100" value="{{ $slot['percentage'] ?? '' }}" placeholder="%">
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="slots[{{ $i }}][minutes]" class="form-control" min="1" value="{{ $slot['minutes'] ?? '' }}" placeholder="Menit">
                            </div>
                            <div class="col-md-2 d-flex align-items-center gap-2">
                                <input type="number" name="slots[{{ $i }}][order]" class="form-control" min="0" value="{{ $slot['order'] ?? $i }}">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-slot">X</button>
                            </div>
                        </div>
                        @empty
                        <div class="row g-2 mb-2 slot-row">
                            <div class="col-md-4">
                                <input type="text" name="slots[0][name]" class="form-control" placeholder="Nama / Tujuan">
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="slots[0][percentage]" class="form-control" min="1" max="100" placeholder="%">
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="slots[0][minutes]" class="form-control" min="1" placeholder="Menit">
                            </div>
                            <div class="col-md-2 d-flex align-items-center gap-2">
                                <input type="number" name="slots[0][order]" class="form-control" min="0" value="0">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-slot">X</button>
                            </div>
                        </div>
                        @endforelse
                    </div>
                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addSlotBtnEdit">Tambah Slot</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSlotsBtnEdit">Hapus Semua Slot</button>
                        <span class="small text-muted ms-2">Total persentase harus 100%, total menit ≤ durasi.</span>
                    </div>

                    <p class="text-muted">Progres dihitung dari slot yang disetujui. Unggah bukti per slot di halaman detail.</p>
                    <button type="submit" class="btn btn-primary">Update Task</button>
                    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Cancel</a>
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

    // Slot repeater for edit
    const slotList = document.getElementById('slotListEdit');
    const addBtn = document.getElementById('addSlotBtnEdit');
    const clearBtn = document.getElementById('clearSlotsBtnEdit');
    if (slotList && addBtn && clearBtn) {
      let idx = slotList.querySelectorAll('.slot-row').length;
      addBtn.addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 slot-row';
        row.innerHTML = `
          <div class="col-md-4">
            <input type="text" name="slots[${idx}][name]" class="form-control" placeholder="Nama / Tujuan">
          </div>
          <div class="col-md-3">
            <input type="number" name="slots[${idx}][percentage]" class="form-control" min="1" max="100" placeholder="%">
          </div>
          <div class="col-md-3">
            <input type="number" name="slots[${idx}][minutes]" class="form-control" min="1" placeholder="Menit">
          </div>
          <div class="col-md-2 d-flex align-items-center gap-2">
            <input type="number" name="slots[${idx}][order]" class="form-control" min="0" value="${idx}">
            <button type="button" class="btn btn-sm btn-outline-danger remove-slot">X</button>
          </div>
        `;
        slotList.appendChild(row);
        idx++;
      });

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
      });
    }
  })();
</script>
@endsection
