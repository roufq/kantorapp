@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Ajukan Perubahan Lokasi</h1>
            <p class="text-muted mb-0">Kirim permintaan pindah lokasi dengan alasan yang jelas.</p>
        </div>
        <div>
            <a href="{{ route('location-change-requests.index') }}" class="nav-link {{ request()->routeIs('location-change-requests.*') ? 'active' : '' }}">kembali</a>
        </div>
    </div>
</div>
<div class="container">
    <h1>Create Location Change Request</h1>

    <form action="{{ route('location-change-requests.store') }}" method="POST">
        @csrf
        @if(isset($users) && $users)
        <div class="form-group mb-3">
            <label for="user_id">Employee</label>
            <div class="searchable-dropdown position-relative" data-placeholder="-- Pilih user --">
                <input type="hidden" name="user_id" id="user_id" value="{{ old('user_id', '') }}" required>
                <button type="button" class="form-select text-start searchable-toggle">-- Pilih user --</button>
                <div class="searchable-panel card shadow-sm p-2 d-none" style="position:absolute; z-index:1000; width:100%; left:0; top:100%; border:1px solid #dee2e6;">
                    <input type="text" class="form-control form-control-sm mb-2 searchable-filter" placeholder="Cari nama/email...">
                    <div class="list-group searchable-list" style="max-height:220px; overflow:auto;">
                        <button type="button" class="list-group-item list-group-item-action searchable-option" data-value="" data-label="-- Pilih user --">-- Pilih user --</button>
                        <button type="button" class="list-group-item list-group-item-action searchable-option" data-value="{{ auth()->id() }}" data-label="— Assign to Myself ({{ auth()->user()->name }}) —">— Assign to Myself ({{ auth()->user()->name }}) —</button>
                        @foreach($users as $u)
                        <button type="button" class="list-group-item list-group-item-action searchable-option" data-value="{{ $u->id }}" data-label="{{ $u->name }} @ {{ $u->email }} ({{ $u->location->name ?? 'No Location' }})">{{ $u->name }} @ {{ $u->email }} ({{ $u->location->name ?? 'No Location' }})</button>
                        @endforeach
                    </div>
                </div>
            </div>
            <small class="text-muted d-block mt-1">Klik untuk membuka dropdown, lalu ketik untuk mencari nama/email.</small>
        </div>
        @endif
        <div class="form-group">
            <label for="target_location_id">Target Location</label>
            <div class="searchable-dropdown position-relative" data-placeholder="-- Pilih lokasi --">
                <input type="hidden" name="target_location_id" id="target_location_id" value="{{ old('target_location_id', $locations->first()->id ?? '') }}" required>
                <button type="button" class="form-select text-start searchable-toggle">-- Pilih lokasi --</button>
                <div class="searchable-panel card shadow-sm p-2 d-none" style="position:absolute; z-index:1000; width:100%; left:0; top:100%; border:1px solid #dee2e6;">
                    <input type="text" class="form-control form-control-sm mb-2 searchable-filter" placeholder="Cari lokasi...">
                    <div class="list-group searchable-list" style="max-height:220px; overflow:auto;">
                        @foreach($locations as $location)
                        <button type="button" class="list-group-item list-group-item-action searchable-option" data-value="{{ $location->id }}" data-label="{{ $location->name }}">{{ $location->name }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
            <small class="text-muted d-block mt-1">Ketik untuk mencari lokasi tujuan.</small>
        </div>
        <div class="form-group">
            <label for="reason">Reason</label>
            <textarea name="reason" id="reason" class="form-control" rows="3" required></textarea>
        </div>
        <div class="form-group mb-3">
            <label for="request_date">Effective Date</label>
            <input type="date" class="form-control" id="request_date" name="request_date" value="{{ now()->toDateString() }}" required>
            <small class="text-muted">Tanggal berlaku perpindahan (untuk sementara berlaku pada tanggal ini).</small>
        </div>
        @if(auth()->user()->hasRole('Admin Lokasi') || auth()->user()->hasRole('Super Admin'))
        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="is_permanent" name="is_permanent" value="1">
            <label for="is_permanent" class="form-check-label">Permanent transfer</label>
            <div><small class="text-muted">Uncheck for one-day move; approval still required.</small></div>
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="approve_now" name="approve_now" value="1">
            <label for="approve_now" class="form-check-label">Approve now</label>
            <div><small class="text-muted">Centang untuk langsung menyetujui tanpa menunggu proses.</small></div>
        </div>
        @endif
        <button type="submit" class="btn btn-primary">Submit Request</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        const dropdowns = document.querySelectorAll('.searchable-dropdown');
        dropdowns.forEach(function(dropdown) {
            const hiddenInput = dropdown.querySelector('input[type="hidden"]');
            const toggle = dropdown.querySelector('.searchable-toggle');
            const panel = dropdown.querySelector('.searchable-panel');
            const filterInput = dropdown.querySelector('.searchable-filter');
            const list = dropdown.querySelector('.searchable-list');
            const options = dropdown.querySelectorAll('.searchable-option');
            if (!hiddenInput || !toggle || !panel || !filterInput || !list || !options.length) return;

            const placeholder = dropdown.getAttribute('data-placeholder') || '-- Pilih --';

            const setLabel = (value) => {
                let label = placeholder;
                options.forEach((btn) => {
                    if (btn.dataset.value === value) {
                        label = btn.dataset.label;
                    }
                });
                toggle.textContent = label;
            };

            const closePanel = () => panel.classList.add('d-none');
            const openPanel = () => {
                panel.classList.remove('d-none');
                filterInput.value = '';
                options.forEach((btn) => btn.classList.remove('d-none'));
                filterInput.focus();
            };

            setLabel(hiddenInput.value);

            toggle.addEventListener('click', function() {
                if (panel.classList.contains('d-none')) {
                    openPanel();
                } else {
                    closePanel();
                }
            });

            filterInput.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                options.forEach(function(btn) {
                    const text = btn.textContent.toLowerCase();
                    btn.classList.toggle('d-none', term && !text.includes(term));
                });
            });

            list.addEventListener('click', function(e) {
                const btn = e.target.closest('.searchable-option');
                if (!btn) return;
                hiddenInput.value = btn.dataset.value;
                setLabel(btn.dataset.value);
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
@endpush
