@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Tambah Shift Lokasi</h1>
            <p class="text-muted mb-0">Buat konfigurasi shift khusus untuk lokasi yang dipilih.</p>
        </div>
        <div>
            <a href="{{ route('location-shifts.index') }}" class="text-decoration-none">Kembali</a>
        </div>
    </div>
</div>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Assign Shifts to Location</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('location-shifts.index') }}">Location Shifts</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Assign Shifts to Location</h3>
                        </div>
                        <!-- /.card-header -->

                        <!-- form start -->
                        <form action="{{ route('location-shifts.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="location_id">Select Location <span class="text-danger">*</span></label>
                                    <select class="form-control @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
                                        <option value="">Choose a location</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }} ({{ $location->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="shift_ids">Pilih Shift <span class="text-danger">*</span></label>
                                    <div class="border p-3" style="max-height: 480px; overflow-y: auto;">
                                        <div class="mb-2 fw-semibold text-primary">Office</div>
                                        @foreach($shifts->where('category','office') as $shift)
                                            @continue($shift->code === 'WKND_OFF')
                                            @php
                                                $checked = in_array($shift->id, old('shift_ids', []));
                                                $currentCategory = old('shift_category.'.$shift->id, $shift->category);
                                                $slots = old('shift_times.'.$shift->id);
                                                if (!$slots) {
                                                    $raw = $shift->time_slots ?? [];
                                                    if (is_array($raw) && isset($raw['start'])) {
                                                        $raw = [ $raw ];
                                                    }
                                                    $slots = is_array($raw) ? $raw : [];
                                                }
                                                $nextIndex = is_array($slots) ? count($slots) : 0;
                                            @endphp
                                            <div class="card mb-3 shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                                        <div class="d-flex align-items-start gap-2">
                                                            <input class="form-check-input mt-1" type="checkbox" id="shift_{{ $shift->id }}" name="shift_ids[]" value="{{ $shift->id }}" {{ $checked ? 'checked' : '' }}>
                                                            <div>
                                                                <label class="form-check-label" for="shift_{{ $shift->id }}">
                                                                    <strong>{{ $shift->name }}</strong> ({{ $shift->code }})
                                                                </label>
                                                                <div class="small text-muted">Default: {{ $shift->getFormattedSchedule() }} @if($shift->day)- {{ $shift->getDayName() }}@endif</div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2 align-items-center">
                                                            <select name="shift_category[{{ $shift->id }}]" class="form-select form-select-sm">
                                                                <option value="office" @selected($currentCategory === 'office')>Office</option>
                                                                <option value="non_office" @selected($currentCategory === 'non_office')>Non Office</option>
                                                            </select>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="default_shift_id" id="default_{{ $shift->id }}" value="{{ $shift->id }}" @checked(old('default_shift_id') == $shift->id)>
                                                                <label class="form-check-label small" for="default_{{ $shift->id }}">Default Office</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="fw-semibold">Time Slots (per lokasi)</span>
                                                            <button type="button" class="btn btn-outline-primary btn-sm add-slot" data-shift="{{ $shift->id }}">Tambah Slot</button>
                                                        </div>
                                                        <div class="slot-container" data-shift="{{ $shift->id }}" data-next-index="{{ $nextIndex }}">
                                                            @foreach($slots as $idx => $slot)
                                                                <div class="row g-2 align-items-end slot-row mb-2">
                                                                <div class="col-md-4">
                                                                    <label class="form-label mb-1">Hari</label>
                                                                    @php $selectedDays = $slot['days'] ?? (isset($slot['day']) ? [$slot['day']] : []); @endphp
                                                                    <select name="shift_times[{{ $shift->id }}][{{ $idx }}][days][]" class="form-select multi-day-select" multiple>
                                                                        <option value="">Semua/Any (biarkan kosong)</option>
                                                                        @foreach(['monday'=>'Senin','tuesday'=>'Selasa','wednesday'=>'Rabu','thursday'=>'Kamis','friday'=>'Jumat','saturday'=>'Sabtu','sunday'=>'Minggu'] as $dKey=>$dLabel)
                                                                            <option value="{{ $dKey }}" @selected(in_array($dKey, $selectedDays ?? []))>{{ $dLabel }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Start</label>
                                                                    <input type="time" name="shift_times[{{ $shift->id }}][{{ $idx }}][start]" class="form-control" value="{{ $slot['start'] ?? '' }}" required>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">End</label>
                                                                    <input type="time" name="shift_times[{{ $shift->id }}][{{ $idx }}][end]" class="form-control" value="{{ $slot['end'] ?? '' }}" required>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="button" class="btn btn-outline-danger w-100 remove-slot">Hapus</button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                        @endforeach

                                        <div class="mb-2 mt-3 fw-semibold text-info">Non Office</div>
                                        @foreach($shifts->where('category','non_office') as $shift)
                                            @php
                                                $checked = in_array($shift->id, old('shift_ids', []));
                                                $currentCategory = old('shift_category.'.$shift->id, $shift->category);
                                                $slots = old('shift_times.'.$shift->id);
                                                if (!$slots) {
                                                    $raw = $shift->time_slots ?? [];
                                                    if (is_array($raw) && isset($raw['start'])) {
                                                        $raw = [ $raw ];
                                                    }
                                                    $slots = is_array($raw) ? $raw : [];
                                                }
                                                $nextIndex = is_array($slots) ? count($slots) : 0;
                                            @endphp
                                            <div class="card mb-3 shadow-sm border-info">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                                        <div class="d-flex align-items-start gap-2">
                                                            <input class="form-check-input mt-1" type="checkbox" id="shift_{{ $shift->id }}" name="shift_ids[]" value="{{ $shift->id }}" {{ $checked ? 'checked' : '' }}>
                                                            <div>
                                                                <label class="form-check-label" for="shift_{{ $shift->id }}">
                                                                    <strong>{{ $shift->name }}</strong> ({{ $shift->code }})
                                                                </label>
                                                                <div class="small text-muted">Default: {{ $shift->getFormattedSchedule() }} @if($shift->day)- {{ $shift->getDayName() }}@endif</div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2 align-items-center">
                                                            <select name="shift_category[{{ $shift->id }}]" class="form-select form-select-sm">
                                                                <option value="office" @selected($currentCategory === 'office')>Office</option>
                                                                <option value="non_office" @selected($currentCategory === 'non_office')>Non Office</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="fw-semibold">Time Slots (per lokasi)</span>
                                                            <button type="button" class="btn btn-outline-primary btn-sm add-slot" data-shift="{{ $shift->id }}">Tambah Slot</button>
                                                        </div>
                                                        <div class="slot-container" data-shift="{{ $shift->id }}" data-next-index="{{ $nextIndex }}">
                                                            @foreach($slots as $idx => $slot)
                                                                <div class="row g-2 align-items-end slot-row mb-2">
                                                                <div class="col-md-4">
                                                                    <label class="form-label mb-1">Hari</label>
                                                                    @php $selectedDays = $slot['days'] ?? (isset($slot['day']) ? [$slot['day']] : []); @endphp
                                                                    <select name="shift_times[{{ $shift->id }}][{{ $idx }}][days][]" class="form-select multi-day-select" multiple>
                                                                        <option value="">Semua/Any (biarkan kosong)</option>
                                                                        @foreach(['monday'=>'Senin','tuesday'=>'Selasa','wednesday'=>'Rabu','thursday'=>'Kamis','friday'=>'Jumat','saturday'=>'Sabtu','sunday'=>'Minggu'] as $dKey=>$dLabel)
                                                                            <option value="{{ $dKey }}" @selected(in_array($dKey, $selectedDays ?? []))>{{ $dLabel }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Start</label>
                                                                    <input type="time" name="shift_times[{{ $shift->id }}][{{ $idx }}][start]" class="form-control" value="{{ $slot['start'] ?? '' }}" required>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">End</label>
                                                                    <input type="time" name="shift_times[{{ $shift->id }}][{{ $idx }}][end]" class="form-control" value="{{ $slot['end'] ?? '' }}" required>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="button" class="btn btn-outline-danger w-100 remove-slot">Hapus</button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('shift_ids')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">Pilih shift untuk lokasi ini, atur kategori dan slot per lokasi. Tandai Default Office untuk jam utama lokasi.</small>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Assign Shifts
                                </button>
                                <a href="{{ route('location-shifts.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dayOptions = [
        { value: 'monday', label: 'Senin' },
        { value: 'tuesday', label: 'Selasa' },
        { value: 'wednesday', label: 'Rabu' },
        { value: 'thursday', label: 'Kamis' },
        { value: 'friday', label: 'Jumat' },
        { value: 'saturday', label: 'Sabtu' },
        { value: 'sunday', label: 'Minggu' },
    ];
    const indexedDayOptions = dayOptions.map((opt, idx) => ({ ...opt, idx }));

    function buildDayDropdown(selectEl) {
        if (selectEl.dataset.enhanced === '1') return;
        selectEl.dataset.enhanced = '1';
        selectEl.classList.add('d-none');

        const wrapper = document.createElement('div');
        wrapper.className = 'position-relative';

        const control = document.createElement('div');
        control.className = 'form-control d-flex align-items-center flex-wrap gap-1 day-control';
        control.setAttribute('tabindex', '0');
        const placeholder = document.createElement('span');
        placeholder.className = 'text-muted small';
        placeholder.textContent = 'Pilih hari (kosong = semua)';
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
                    chip.className = 'badge bg-primary-subtle text-primary border me-1 mb-1';
                    chip.textContent = label;
                    control.appendChild(chip);
                });
            }
            const caret = document.createElement('span');
            caret.className = 'ms-auto text-muted';
            caret.innerHTML = '&#9662;';
            control.appendChild(caret);
        }

        function rebuildMenu() {
            const selectedValues = Array.from(selectEl.selectedOptions).map(o => o.value);
            const sorted = [...indexedDayOptions].sort((a, b) => {
                const aSel = selectedValues.includes(a.value);
                const bSel = selectedValues.includes(b.value);
                if (aSel !== bSel) return aSel ? -1 : 1;
                return a.idx - b.idx; // keep natural order Senin-Minggu
            });
            menu.innerHTML = '';
            sorted.forEach(opt => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-item d-flex justify-content-between align-items-center';
                btn.dataset.value = opt.value;
                btn.innerHTML = `<span>${opt.label}</span><span class="checkmark text-primary" style="display:${selectedValues.includes(opt.value) ? 'inline' : 'none'}">&#10003;</span>`;
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

        document.addEventListener('click', () => {
            closeAllMenus();
        });

        selectEl.parentNode.insertBefore(wrapper, selectEl);
        wrapper.appendChild(control);
        wrapper.appendChild(menu);

        selectEl.addEventListener('change', () => {
            syncChips();
            rebuildMenu();
        });

        // initial render
        syncChips();
        rebuildMenu();
    }

    // Enhance all existing multi-day selects
    document.querySelectorAll('.multi-day-select').forEach(buildDayDropdown);

    function addSlot(shiftId) {
        const container = document.querySelector('.slot-container[data-shift="' + shiftId + '"]');
        if (!container) return;
        const idx = parseInt(container.getAttribute('data-next-index') || '0', 10);
        container.setAttribute('data-next-index', idx + 1);
        const html = `
            <div class="row g-2 align-items-end slot-row mb-2">
                <div class="col-md-4">
                    <label class="form-label">Hari</label>
                    <select name="shift_times[${shiftId}][${idx}][days][]" class="form-select multi-day-select" multiple>
                        <option value="">Semua/Any (kosongkan jika untuk semua)</option>
                        ${dayOptions.map(o => `<option value="${o.value}">${o.label}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Start</label>
                    <input type="time" name="shift_times[${shiftId}][${idx}][start]" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">End</label>
                    <input type="time" name="shift_times[${shiftId}][${idx}][end]" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger w-100 remove-slot">Hapus</button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        // enhance the newly added select
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
