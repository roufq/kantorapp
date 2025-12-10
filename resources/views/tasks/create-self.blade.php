@extends('layouts.app')

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
                <form action="{{ route('tasks.store.self') }}" method="POST" enctype="multipart/form-data">
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
                        <small class="text-muted">Total menit yang akan dibagi ke slot progres. Due date tetap target akhir.</small>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" id="due_date" value="{{ old('due_date') }}">
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo (Optional)</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Document (Optional)</label>
                        <input type="file" class="form-control" id="document" name="document" accept=".pdf,.doc,.docx,.txt">
                    </div>
                    <p class="text-muted">Progres tugas pribadi diupdate lewat halaman detail dengan melampirkan foto atau dokumen.</p>
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
                                    <input type="number" name="slots[{{ $idx }}][percentage]" class="form-control" min="1" max="100" placeholder="25" required value="{{ $slot['percentage'] }}">
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
    const slotList = document.getElementById('slotListSelf');
    const addBtn = document.getElementById('addSlotBtnSelf');
    const clearBtn = document.getElementById('clearSlotsBtnSelf');
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
