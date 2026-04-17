@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;">{{ __('Assign Location Shifts') }}</h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;">{{ __('Create custom shift configurations for the selected location.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="{{ route('location-shifts.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i>{{ __('Back') }}
          </a>
        </div>
      </div>

      <form action="{{ route('location-shifts.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 p-4 rounded-4 border border-light shadow-sm mb-4">
                    <label for="location_id" class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-3">{{ __('Select Location *') }}</label>
                    <select class="form-select bg-dark bg-opacity-50 border-light text-white rounded-pill px-4 shadow-none @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
                        <option value="">{{ __('Choose a location') }}</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }} ({{ $location->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('location_id')
                        <div class="invalid-feedback ms-3">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius: 24px !important;">
                    <div class="card-header border-bottom border-light p-4" style="background: #f8fafc;">
                        <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-clock-check-outline me-2 text-info"></i>{{ __('Select Shifts *') }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="custom-shift-grid" style="max-height: 700px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.1) transparent;">
                            <h6 class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-3 mt-2 px-2 d-flex align-items-center">
                                <i class="mdi mdi-office-building-outline me-2"></i>Office
                            </h6>
                            @foreach($shifts->where('category','office') as $shift)
                                @continue($shift->code === 'WKND_OFF')
                                @php
                                    $checked = in_array($shift->id, old('shift_ids', []));
                                    $currentCategory = old('shift_category.'.$shift->id, $shift->category);
                                    $slots = old('shift_times.'.$shift->id);
                                    if (!$slots) {
                                        $raw = $shift->time_slots ?? [];
                                        if (is_array($raw) && isset($raw['start'])) { $raw = [ $raw ]; }
                                        $slots = is_array($raw) ? $raw : [];
                                    }
                                    $nextIndex = is_array($slots) ? count($slots) : 0;
                                @endphp
                                @include('location-shifts._shift_card', compact('shift', 'checked', 'currentCategory', 'slots', 'nextIndex'))
                            @endforeach

                            <h6 class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-3 mt-5 px-2 d-flex align-items-center">
                                <i class="mdi mdi-factory me-2"></i>Non Office
                            </h6>
                            @foreach($shifts->where('category','non_office') as $shift)
                                @php
                                    $checked = in_array($shift->id, old('shift_ids', []));
                                    $currentCategory = old('shift_category.'.$shift->id, $shift->category);
                                    $slots = old('shift_times.'.$shift->id);
                                    if (!$slots) {
                                        $raw = $shift->time_slots ?? [];
                                        if (is_array($raw) && isset($raw['start'])) { $raw = [ $raw ]; }
                                        $slots = is_array($raw) ? $raw : [];
                                    }
                                    $nextIndex = is_array($slots) ? count($slots) : 0;
                                @endphp
                                @include('location-shifts._shift_card', compact('shift', 'checked', 'currentCategory', 'slots', 'nextIndex'))
                            @endforeach
                        </div>
                        
                        @error('shift_ids')
                            <div class="alert alert-danger bg-danger bg-opacity-10 text-white border-0 rounded-4 mt-4 ps-4">
                                <i class="mdi mdi-alert-circle-outline me-2"></i>{{ $message }}
                            </div>
                        @enderror
                        <p class="text-muted smaller italic mt-4 px-2">
                            <i class="mdi mdi-information-outline me-1"></i>{{ __('Select shifts for this location, set categories and slots per location. Mark Default Office for the main hours.') }}
                        </p>
                    </div>

                    <div class="card-footer border-top border-light p-4 bg-transparent d-flex justify-content-end gap-3">
                        <a href="{{ route('location-shifts.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-lg">
                            <i class="mdi mdi-content-save-check-outline me-2"></i>{{ __('Assign Shifts') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.italic { font-style: italic; }
.smaller { font-size: 0.8rem; }
.letter-spacing-1 { letter-spacing: 1px; }
.transition-all { transition: all 0.3s ease; }
.shift-card { border: 1px solid #f1f5f9; }
.shift-card:hover { border-color: rgba(6, 182, 212, 0.3); background: rgba(255,255,255,0.02); }
.form-check-input { cursor: pointer; }
.multi-day-select { display: none; }
.day-control { cursor: pointer; min-height: 38px; background: #ffffff !important; border-color: rgba(255,255,255,0.1) !important; color: white !important; }
.day-menu { background: #1a1d21 !important; border-color: rgba(255,255,255,0.1) !important; }
.day-menu .dropdown-item { color: #ccc !important; padding: 8px 20px; transition: all 0.2s; }
.day-menu .dropdown-item:hover { background: rgba(6, 182, 212, 0.1) !important; color: #fff !important; }
.day-menu .dropdown-item.active, .day-menu .dropdown-item:active { background: #06b6d4 !important; }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dayLabels = {
        'monday': "{{ __('Monday') }}",
        'tuesday': "{{ __('Tuesday') }}",
        'wednesday': "{{ __('Wednesday') }}",
        'thursday': "{{ __('Thursday') }}",
        'friday': "{{ __('Friday') }}",
        'saturday': "{{ __('Saturday') }}",
        'sunday': "{{ __('Sunday') }}"
    };
    
    const dayOptions = Object.keys(dayLabels).map(key => ({ value: key, label: dayLabels[key] }));
    const indexedDayOptions = dayOptions.map((opt, idx) => ({ ...opt, idx }));

    function buildDayDropdown(selectEl) {
        if (selectEl.dataset.enhanced === '1') return;
        selectEl.dataset.enhanced = '1';
        selectEl.classList.add('d-none');

        const wrapper = document.createElement('div');
        wrapper.className = 'position-relative';

        const control = document.createElement('div');
        control.className = 'form-control d-flex align-items-center flex-wrap gap-1 day-control rounded-pill px-3';
        control.setAttribute('tabindex', '0');
        const placeholder = document.createElement('span');
        placeholder.className = 'text-muted smaller';
        placeholder.textContent = "{{ __('Pick days (empty = all)') }}";
        control.appendChild(placeholder);

        const menu = document.createElement('div');
        menu.className = 'day-menu border rounded bg-white shadow-sm py-2';
        menu.style.cssText = 'display:none; max-height:220px; overflow:auto; position:absolute; z-index:1050; min-width:100%; width:100%;';

        function syncChips() {
            const selected = Array.from(selectEl.selectedOptions).map(o => o.textContent.trim());
            control.innerHTML = '';
            if (selected.length === 0) {
                control.appendChild(placeholder);
            } else {
                selected.forEach(label => {
                    const chip = document.createElement('span');
                    chip.className = 'badge rounded-pill bg-info bg-opacity-20 text-info border border-info border-opacity-25 smaller px-2 py-1';
                    chip.textContent = label;
                    control.appendChild(chip);
                });
            }
            const caret = document.createElement('span');
            caret.className = 'ms-auto text-muted smaller';
            caret.innerHTML = '&#9662;';
            control.appendChild(caret);
        }

        function rebuildMenu() {
            const selectedValues = Array.from(selectEl.selectedOptions).map(o => o.value);
            const sorted = [...indexedDayOptions].sort((a, b) => {
                const aSel = selectedValues.includes(a.value);
                const bSel = selectedValues.includes(b.value);
                if (aSel !== bSel) return aSel ? -1 : 1;
                return a.idx - b.idx;
            });
            menu.innerHTML = '';
            sorted.forEach(opt => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-item d-flex justify-content-between align-items-center border-0 bg-transparent';
                btn.dataset.value = opt.value;
                btn.innerHTML = `<span class="smaller">${opt.label}</span><span class="checkmark text-info" style="display:${selectedValues.includes(opt.value) ? 'inline' : 'none'}">&#10003;</span>`;
                btn.addEventListener('click', () => {
                    const targetOpt = Array.from(selectEl.options).find(o => o.value === opt.value);
                    if (targetOpt) {
                        targetOpt.selected = !targetOpt.selected;
                        selectEl.dispatchEvent(new Event('change'));
                    }
                });
                menu.appendChild(btn);
            });
        }

        function closeAllMenus() {
            document.querySelectorAll('.day-menu').forEach(m => m.style.display = 'none');
        }

        control.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = menu.style.display === 'block';
            closeAllMenus();
            menu.style.display = isOpen ? 'none' : 'block';
        });

        document.addEventListener('click', () => { closeAllMenus(); });

        selectEl.parentNode.insertBefore(wrapper, selectEl);
        wrapper.appendChild(control);
        wrapper.appendChild(menu);

        selectEl.addEventListener('change', () => {
            syncChips();
            rebuildMenu();
        });

        syncChips();
        rebuildMenu();
    }

    document.querySelectorAll('.multi-day-select').forEach(buildDayDropdown);

    function addSlot(shiftId) {
        const container = document.querySelector('.slot-container[data-shift="' + shiftId + '"]');
        if (!container) return;
        const idx = parseInt(container.getAttribute('data-next-index') || '0', 10);
        container.setAttribute('data-next-index', idx + 1);
        const html = `
            <div class="row g-2 align-items-end slot-row mb-3 p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-5 mx-0">
                <div class="col-md-4">
                    <label class="form-label text-muted smaller fw-bold text-uppercase mb-2">{{ __('Day') }}</label>
                    <select name="shift_times[${shiftId}][${idx}][days][]" class="form-select multi-day-select" multiple>
                        <option value="">{{ __('Pick days (empty = all)') }}</option>
                        ${dayOptions.map(o => `<option value="${o.value}">${o.label}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted smaller fw-bold text-uppercase mb-2">{{ __('Start') }}</label>
                    <input type="time" name="shift_times[${shiftId}][${idx}][start]" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-pill px-3 shadow-none smaller" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted smaller fw-bold text-uppercase mb-2">{{ __('End') }}</label>
                    <input type="time" name="shift_times[${shiftId}][${idx}][end]" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-pill px-3 shadow-none smaller" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger w-100 rounded-pill px-3 smaller fw-bold remove-slot mb-1">
                        <i class="mdi mdi-trash-can-outline me-1"></i>{{ __('Delete') }}
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        const newSelect = container.querySelector(`select[name="shift_times[${shiftId}][${idx}][days][]"]`);
        if (newSelect) buildDayDropdown(newSelect);
    }

    document.querySelectorAll('.add-slot').forEach(btn => {
        btn.addEventListener('click', function () {
            const shiftId = this.getAttribute('data-shift');
            addSlot(shiftId);
        });
    });

    document.addEventListener('click', function (e) {
        const target = e.target.closest('.remove-slot');
        if (target) {
            const row = target.closest('.slot-row');
            if (row) row.remove();
        }
    });
});
</script>
@endpush
