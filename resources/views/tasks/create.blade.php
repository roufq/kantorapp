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
                <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" id="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" id="description" rows="3"></textarea>
                    </div>
                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi'))
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign To</label>
                        <div class="assigned-to-dropdown position-relative">
                            <input type="hidden" name="assigned_to" id="assigned_to" required>
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
                        <input type="date" name="due_date" class="form-control" id="due_date">
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo (Optional)</label>
                        <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Document (Optional)</label>
                        <input type="file" name="document" id="document" class="form-control" accept=".pdf,.doc,.docx,.txt">
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
  })();
</script>
@endsection
