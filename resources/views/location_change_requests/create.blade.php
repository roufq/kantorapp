@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Apply for Location Change</h1>
            <p class="text-muted mb-0">Send a location transfer request with clear reasons.</p>
        </div>
        <div>
            <a href="{{ route('location-change-requests.index') }}" class="btn btn-outline-secondary waves-effect waves-light">Back</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('location-change-requests.store') }}" method="POST" class="row g-3">
        @csrf
        @if(isset($users) && $users)
        <div class="col-md-6">
            <label for="user_id" class="form-label">Employee</label>
            <div class="searchable-dropdown position-relative" data-placeholder="-- Select User --">
                <input type="hidden" name="user_id" id="user_id" value="{{ old('user_id', '') }}" required>
                <button type="button" class="form-select text-start searchable-toggle">-- Select User --</button>
                <div class="searchable-panel card shadow-sm p-2 d-none" style="position:absolute; z-index:1000; width:100%; left:0; top:100%; border:1px solid #dee2e6;">
                    <input type="text" class="form-control form-control-sm mb-2 searchable-filter" placeholder="Search name/email...">
                    <div class="list-group searchable-list" style="max-height:220px; overflow:auto;">
                        <button type="button" class="list-group-item list-group-item-action searchable-option" data-value="" data-label="-- Select User --">-- Select User --</button>
                        <button type="button" class="list-group-item list-group-item-action searchable-option" data-value="{{ auth()->id() }}" data-label="— Assign to Myself ({{ auth()->user()->name }}) —">— Assign to Myself ({{ auth()->user()->name }}) —</button>
                        @foreach($users as $u)
                        <button type="button" class="list-group-item list-group-item-action searchable-option" data-value="{{ $u->id }}" data-label="{{ $u->name }} @ {{ $u->email }} ({{ $u->location->name ?? 'No Location' }})">{{ $u->name }} @ {{ $u->email }} ({{ $u->location->name ?? 'No Location' }})</button>
                        @endforeach
                    </div>
                </div>
            </div>
            <small class="text-muted d-block mt-1">Click to open dropdown, then type to search for name/email.</small>
        </div>
        @endif
        <div class="col-md-6">
            <label for="target_location_id" class="form-label">Target Location</label>
            <div class="searchable-dropdown position-relative" data-placeholder="-- Select Location --">
                <input type="hidden" name="target_location_id" id="target_location_id" value="{{ old('target_location_id', $locations->first()->id ?? '') }}" required>
                <button type="button" class="form-select text-start searchable-toggle">-- Select Location --</button>
                <div class="searchable-panel card shadow-sm p-2 d-none" style="position:absolute; z-index:1000; width:100%; left:0; top:100%; border:1px solid #dee2e6;">
                    <input type="text" class="form-control form-control-sm mb-2 searchable-filter" placeholder="Search location...">
                    <div class="list-group searchable-list" style="max-height:220px; overflow:auto;">
                        @foreach($locations as $location)
                        <button type="button" class="list-group-item list-group-item-action searchable-option" data-value="{{ $location->id }}" data-label="{{ $location->name }}">{{ $location->name }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
            <small class="text-muted d-block mt-1">Type to search for target location.</small>
        </div>
        <div class="col-md-12">
            <label for="reason" class="form-label">Reason</label>
            <textarea name="reason" id="reason" class="form-control" rows="3" required>{{ old('reason') }}</textarea>
        </div>
        <div class="col-12">
            <div class="row g-2 align-items-center">
                <div class="col-lg-4 col-md-6">
                    <label for="request_date" class="form-label">Effective Date</label>
                    <input type="date" class="form-control" id="request_date" name="request_date" value="{{ old('request_date', now()->toDateString()) }}" required>
                    <small class="text-muted">Transfer effective date (for temporary moves, it applies on this date).</small>
                </div>
                @if(auth()->user()->hasRole('Location Admin') || auth()->user()->hasRole('Super Admin'))
                <div class="col-lg-4 col-md-6">
                    <div class="form-check mb-1">
                        <input type="checkbox" class="form-check-input" id="is_permanent" name="is_permanent" value="1" {{ old('is_permanent') ? 'checked' : '' }}>
                        <label for="is_permanent" class="form-check-label">Permanent transfer</label>
                    </div>
                    <small class="text-muted d-block">Uncheck for one-day move; approval still required.</small>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-check mb-1">
                        <input type="checkbox" class="form-check-input" id="approve_now" name="approve_now" value="1" {{ old('approve_now') ? 'checked' : '' }}>
                        <label for="approve_now" class="form-check-label">Approve now</label>
                    </div>
                    <small class="text-muted d-block">Check to approve instantly without waiting for process.</small>
                </div>
                @endif
            </div>
        </div>
        <div class="col-12 d-flex flex-wrap align-items-center gap-2 mt-2">
            <button type="submit" class="btn btn-primary waves-effect waves-light">Submit Request</button>
            <a href="{{ route('location-change-requests.index') }}" class="btn btn-outline-secondary waves-effect">Cancel</a>
        </div>
        </form>
    </div>
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

            const placeholder = dropdown.getAttribute('data-placeholder') || '-- Select --';

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
