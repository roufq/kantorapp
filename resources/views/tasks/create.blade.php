@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">{{ (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')) ? 'Buat / Assign Tugas' : 'Buat Tugas' }}</h1>
            <p class="text-muted mb-0">
                {{ (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')) ? 'Super Admin atau Admin Lokasi dapat menetapkan tugas ke karyawan/admin lokasi, atau diri sendiri.' : 'Buat tugas untuk diri sendiri dan pantau progresnya.' }}
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
                <h3 class="card-title">{{ (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')) ? 'Assign Task' : 'Create Task' }}</h3>
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
                <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" id="description" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="duration_minutes" class="form-label">Durasi (menit)</label>
                        <input type="number" name="duration_minutes" class="form-control" id="duration_minutes" min="1" placeholder="Misal 240 untuk 4 jam" value="{{ old('duration_minutes') }}">
                        <small class="text-muted">Total menit yang akan dibagi ke slot progres. Due date tetap berlaku sebagai target akhir.</small>
                    </div>
                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi'))
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign To</label>
                        <div class="assigned-to-dropdown position-relative">
                            <input type="hidden" name="assigned_to" id="assigned_to" value="{{ old('assigned_to') }}" required>
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
                        <small class="text-muted d-block mt-1">Klik untuk membuka dropdown, lalu ketik untuk mencari nama/email.</small>
                    </div>
                    @endif
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" id="due_date" value="{{ old('due_date') }}">
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo (Optional)</label>
                        <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Document (Optional)</label>
                        <input type="file" name="document" id="document" class="form-control" accept=".pdf,.doc,.docx,.txt">
                    </div>
                    <p class="text-muted">Setelah dibuat, progres 0-100% diupdate lewat halaman detail tugas dengan lampiran foto/dokumen.</p>
                    <hr>
                    <h5 class="mb-2">Slot Progres (wajib, total % = 100%)</h5>
                    @php
                        $oldSlots = old('slots', [['name' => '', 'percentage' => '', 'minutes' => '', 'order' => 0]]);
                    @endphp
                    <div id="slotList" data-initial-count="{{ count($oldSlots) }}">
                        @foreach($oldSlots as $idx => $slot)
                            <div class="row g-2 mb-2 slot-row">
                                <div class="col-md-4">
                                    <label class="form-label">Nama / Tujuan</label>
                                    <input type="text" name="slots[{{ $idx }}][name]" class="form-control" placeholder="Contoh: Desain UI" required value="{{ $slot['name'] }}">
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
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addSlotBtn">Tambah Slot</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSlotsBtn">Hapus Semua Slot</button>
                        <span class="small text-muted ms-2">Minimal 1 slot. Total persentase harus 100%, total menit harus sama dengan durasi (jika diisi).</span>
                    </div>

                    <button type="submit" class="btn btn-primary">{{ (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')) ? 'Assign Task' : 'Create Task' }}</button>
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

      // init selected text
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

    // Slot repeater
    (function() {
      const slotList = document.getElementById('slotList');
      const addBtn = document.getElementById('addSlotBtn');
      const clearBtn = document.getElementById('clearSlotsBtn');
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
  })();
</script>
@endsection
