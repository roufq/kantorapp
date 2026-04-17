@extends('layouts.appnew')

@section('content')
@php
    $canSelfEditRejected = $canSelfEditRejected ?? false;
@endphp

<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;">{{ __('Edit Task') }}</h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;">
            {{ __('Adjust task details, assignment, or status according to your needs and role.') }}
          </p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i>{{ __('Back') }}
          </a>
        </div>
      </div>

      @if($canSelfEditRejected)
        <div class="alert alert-warning border-0 bg-warning bg-opacity-25 text-white rounded-3 shadow-sm p-4 mb-4">
            <div class="d-flex align-items-start gap-3">
                <i class="mdi mdi-alert-circle-outline fs-3 text-warning"></i>
                <div>
                    <div class="fw-bold mb-1">{{ __('Task rejected.') }}</div>
                    <div class="small">{{ __('Update the details below to re-submit without creating a new task.') }}</div>
                    @if($task->approval_note)
                        <div class="smaller italic mt-2 opacity-75">{{ __('Rejection reason:') }} {{ $task->approval_note }}</div>
                    @endif
                </div>
            </div>
        </div>
      @endif

      <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 24px !important;">
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 p-3 mb-4 shadow-sm border-0 bg-danger bg-opacity-25 text-white">
                            <div class="fw-bold mb-2"><i class="mdi mdi-alert-circle-outline me-1"></i>{{ __('Validation failed:') }}</div>
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('tasks.update', $task) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="row g-4 mb-4">
                            @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Location Admin') || $canSelfEditRejected)
                            <div class="col-lg-7">
                                <div class="card shadow-sm border-0 p-4 rounded-4 border border-light h-100">
                                    <h5 class="text-dark fw-bold mb-4 d-flex align-items-center">
                                        <i class="mdi mdi-file-edit-outline me-2 text-info"></i>{{ __('Task Information') }}
                                    </h5>
                                    <div class="mb-3">
                                        <label for="title" class="form-label text-muted small fw-bold">{{ __('Title') }}</label>
                                        <input type="text" class="form-control rounded-pill px-4 shadow-sm" id="title" name="title" value="{{ old('title', $task->title) }}" required>
                                    </div>
                                    <div class="mb-0">
                                        <label for="description" class="form-label text-muted small fw-bold">{{ __('Description') }}</label>
                                        <textarea class="form-control" id="description" name="description" rows="10" required style="border-radius: 15px !important;">{{ old('description', $task->description) }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="card shadow-sm border-0 p-4 rounded-4 border border-light h-100">
                                    <h5 class="text-dark fw-bold mb-4 d-flex align-items-center">
                                        <i class="mdi mdi-account-cog-outline me-2 text-warning"></i>{{ __('Assignment & Schedule') }}
                                    </h5>
                                    <div class="mb-3">
                                        <label for="duration_minutes" class="form-label text-muted small fw-bold">{{ __('Duration (minutes):') }}</label>
                                        <input type="number" class="form-control rounded-pill px-4 shadow-sm" id="duration_minutes" name="duration_minutes" min="1" value="{{ old('duration_minutes', $task->duration_minutes) }}">
                                        <small class="text-muted italic smaller mt-1 d-block">{{ __('Total minutes to be split into progress slots. Due date still applies as final target.') }}</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="assigned_to" class="form-label text-muted small fw-bold">{{ __('Assign To') }}</label>
                                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Location Admin'))
                                            <div class="assigned-to-dropdown position-relative">
                                                <input type="hidden" name="assigned_to" id="assigned_to" value="{{ old('assigned_to', $task->assigned_to) }}" required>
                                                <button type="button" class="form-select text-start assigned-to-toggle rounded-pill px-4 shadow-sm" data-placeholder="{{ __('-- Select user --') }}">{{ __('-- Select user --') }}</button>
                                                <div class="assigned-to-panel card shadow-sm border-0 shadow-lg p-2 d-none border border-light" style="position:absolute; z-index:1000; width:100%; left:0; top:105%; border-radius: 15px;">
                                                    <input type="text" class="form-control form-control-sm mb-2 rounded-pill px-3 bg-dark bg-opacity-50 border-light text-white shadow-none assigned-to-filter" placeholder="Search name/email...">
                                                    <div class="list-group assigned-to-list" style="max-height:220px; overflow:auto; scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.1) transparent;">
                                                        <button type="button" class="list-group-item list-group-item-action bg-white text-dark border-0 py-2 smaller" data-user-id="" data-user-label="{{ __('-- Select user --') }}">{{ __('-- Select user --') }}</button>
                                                        <button type="button" class="list-group-item list-group-item-action bg-transparent text-info border-0 py-2 smaller italic fw-bold" data-user-id="{{ auth()->id() }}" data-user-label="{{ __('-- Assign to Myself') }} ({{ auth()->user()->name }})">
                                                            <i class="mdi mdi-account-circle-outline me-1"></i> {{ __('-- Assign to Myself') }} ({{ auth()->user()->name }})
                                                        </button>
                                                        @foreach($users as $user)
                                                            <button type="button" class="list-group-item list-group-item-action bg-white text-dark border-0 py-2 smaller" data-user-id="{{ $user->id }}" data-user-label="{{ $user->name }} @ {{ $user->email }}">
                                                                {{ $user->name }} <span class="text-muted smaller">({{ $user->email }})</span>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="p-2 px-3 bg-white bg-opacity-5 rounded-pill border border-white border-opacity-5 text-dark fw-bold d-flex align-items-center">
                                                <i class="mdi mdi-account-lock-outline me-2 text-muted"></i>
                                                {{ auth()->user()->name }} <span class="text-muted smaller italic ms-2">({{ __('cannot be changed') }})</span>
                                            </div>
                                            <input type="hidden" name="assigned_to" value="{{ $task->assigned_to }}">
                                        @endif
                                    </div>
                                    <div class="mb-0">
                                        <label for="due_date" class="form-label text-muted small fw-bold">{{ __('Due Date') }}</label>
                                        <input type="date" class="form-control rounded-pill px-4 shadow-sm" id="due_date" name="due_date" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="mt-4 p-4 card shadow-sm border-0 rounded-4 border border-info border-opacity-10">
                            <h5 class="text-dark fw-bold mb-2 d-flex align-items-center">
                                <i class="mdi mdi-layers-triple-outline me-2 text-info"></i>{{ __('Progress slots (optional, total % must be 100%)') }}
                            </h5>
                            <p class="text-muted smaller mb-4">{{ __('Send progress evidence using links in slots, without file uploads.') }}</p>
                            
                            @php $slots = old('slots', $task->slots->toArray()); @endphp
                            
                            <div id="slotListEdit" data-initial-count="{{ count($slots) }}" class="mb-3">
                                @forelse($slots as $i => $slot)
                                <div class="row g-3 mb-3 slot-row align-items-center">
                                    <div class="col-md-5">
                                        @if($i === 0) <label class="form-label text-muted smaller fw-bold">{{ __('Name / Target') }}</label> @endif
                                        <input type="text" name="slots[{{ $i }}][name]" class="form-control rounded-pill px-3 shadow-sm" value="{{ $slot['name'] ?? '' }}" placeholder="{{ __('Name / Target') }}">
                                    </div>
                                    <div class="col-md-2">
                                        @if($i === 0) <label class="form-label text-muted smaller fw-bold">{{ __('Percentage (%)') }}</label> @endif
                                        <input type="number" name="slots[{{ $i }}][percentage]" class="form-control rounded-pill px-3 shadow-sm" min="0.01" max="100" step="0.01" value="{{ $slot['percentage'] ?? '' }}" placeholder="%">
                                    </div>
                                    <div class="col-md-2">
                                        @if($i === 0) <label class="form-label text-muted smaller fw-bold">{{ __('Minutes') }}</label> @endif
                                        <input type="number" name="slots[{{ $i }}][minutes]" class="form-control rounded-pill px-3 shadow-sm" min="1" value="{{ $slot['minutes'] ?? '' }}" placeholder="{{ __('Minutes') }}">
                                    </div>
                                    <div class="col-md-3">
                                        @if($i === 0) <label class="form-label text-muted smaller fw-bold">{{ __('Order') }}</label> @endif
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="number" name="slots[{{ $i }}][order]" class="form-control rounded-pill px-3 shadow-sm" min="0" value="{{ $slot['order'] ?? $i }}">
                                            <button type="button" class="btn btn-outline-danger rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 remove-slot shadow-sm p-0" style="width: 32px; height: 32px;">
                                                <i class="mdi mdi-close"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="row g-3 mb-3 slot-row align-items-center">
                                    <div class="col-md-5">
                                        <label class="form-label text-muted smaller fw-bold">{{ __('Name / Target') }}</label>
                                        <input type="text" name="slots[0][name]" class="form-control rounded-pill px-3 shadow-sm" placeholder="{{ __('Name / Target') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label text-muted smaller fw-bold">{{ __('Percentage (%)') }}</label>
                                        <input type="number" name="slots[0][percentage]" class="form-control rounded-pill px-3 shadow-sm" min="0.01" max="100" step="0.01" placeholder="%">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label text-muted smaller fw-bold">{{ __('Minutes') }}</label>
                                        <input type="number" name="slots[0][minutes]" class="form-control rounded-pill px-3 shadow-sm" min="1" placeholder="{{ __('Minutes') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-muted smaller fw-bold">{{ __('Order') }}</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="number" name="slots[0][order]" class="form-control rounded-pill px-3 shadow-sm" min="0" value="0">
                                            <button type="button" class="btn btn-outline-danger rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 remove-slot shadow-sm p-0" style="width: 32px; height: 32px;">
                                                <i class="mdi mdi-close"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            
                            <div id="slotSummaryEdit" class="small fw-bold mb-3 p-2 px-3 rounded-pill bg-white bg-opacity-5 border border-white border-opacity-5 d-inline-block"></div>
                            
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-0">
                                <button type="button" class="btn btn-outline-info rounded-pill px-4 fw-bold shadow-sm" id="addSlotBtnEdit">
                                    <i class="mdi mdi-plus-circle-outline me-1"></i>{{ __('Add Slot') }}
                                </button>
                                <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold shadow-sm" id="clearSlotsBtnEdit">
                                    <i class="mdi mdi-trash-can-outline me-1"></i>{{ __('Delete All Slots') }}
                                </button>
                                <span class="smaller text-muted italic ms-lg-3">
                                    <i class="mdi mdi-information-outline me-1"></i>{{ __('Min. 1 slot. Total percentage must be 100%, total minutes must match duration (if filled).') }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-top border-white border-opacity-5 d-flex gap-3 justify-content-end">
                            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-lg">
                                <i class="mdi mdi-content-save-outline me-2"></i>{{ __('Update Task') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.italic { font-style: italic; }
.smaller { font-size: 0.8rem; }
.letter-spacing-1 { letter-spacing: 1px; }
.list-group-item-action:hover { background: #f1f5f9 !important; color: #06b6d4 !important; }
.assigned-to-list::-webkit-scrollbar { width: 5px; }
.assigned-to-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
</style>

<script>
  (function() {
    const dropdowns = document.querySelectorAll('.assigned-to-dropdown');
    const i18n = {
        totalPercent: "{{ __('Total %:') }}",
        remaining: "{{ __('remaining') }}",
        totalMinutes: "{{ __('Total minutes:') }}",
        namePlaceholder: "{{ __('Name / Target') }}",
        minutesPlaceholder: "{{ __('Minutes') }}"
    };

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
        toggle.textContent = toggle.getAttribute('data-placeholder') || "{{ __('-- Select user --') }}";
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

    const initSlotProgress = ({ slotListId, addBtnId, clearBtnId, summaryId, durationInputId = 'duration_minutes' }) => {
      const slotList = document.getElementById(slotListId);
      const addBtn = document.getElementById(addBtnId);
      const clearBtn = document.getElementById(clearBtnId);
      const durationInput = document.getElementById(durationInputId);
      const slotSummary = document.getElementById(summaryId);
      if (!slotList || !addBtn || !clearBtn) return;
      let idx = parseInt(slotList.getAttribute('data-initial-count') || slotList.querySelectorAll('.slot-row').length || 0);
      let isSyncing = false;

      const getDuration = () => {
        const val = parseFloat(durationInput?.value);
        return isNaN(val) || val <= 0 ? null : val;
      };

      const formatPct = (val, decimals = 2) => {
        if (!isFinite(val)) return '';
        const factor = Math.pow(10, decimals);
        const rounded = Math.round(val * factor) / factor;
        return Math.abs(rounded) < 0.01 ? 0 : rounded;
      };

      const updateSummary = () => {
        if (!slotSummary) return;
        let totalPct = 0;
        let totalMinutes = 0;
        slotList.querySelectorAll('.slot-row').forEach((row) => {
          const pct = parseFloat(row.querySelector('input[name$=\"[percentage]\"]')?.value);
          const min = parseFloat(row.querySelector('input[name$=\"[minutes]\"]')?.value);
          if (!isNaN(pct)) totalPct += pct;
          if (!isNaN(min)) totalMinutes += min;
        });
        totalPct = parseFloat(totalPct.toFixed(2));
        const durationVal = getDuration();
        let remainingPct = 100 - totalPct;
        if (Math.abs(remainingPct) < 0.01) remainingPct = 0;
        let remainingMin = durationVal !== null ? durationVal - totalMinutes : null;
        if (remainingMin !== null && Math.abs(remainingMin) < 0.01) remainingMin = 0;
        
        let labelPct = `${i18n.totalPercent} ${formatPct(totalPct)} / 100`;
        if (remainingPct) labelPct += ` (${i18n.remaining} ${formatPct(remainingPct)})`;
        
        let labelMin = durationVal !== null
            ? `${i18n.totalMinutes} ${totalMinutes} / ${durationVal}` + (remainingMin !== null ? ` (${i18n.remaining} ${remainingMin})` : '')
            : `${i18n.totalMinutes} ${totalMinutes}`;
            
        slotSummary.textContent = labelPct + ' | ' + labelMin;
      };

      const syncRow = (row, from) => {
        if (isSyncing) return;
        const pctInput = row.querySelector('input[name$=\"[percentage]\"]');
        const minInput = row.querySelector('input[name$=\"[minutes]\"]');
        if (!pctInput || !minInput) return;
        const durationVal = getDuration();
        isSyncing = true;
        if (from === 'percentage') {
          const pct = parseFloat(pctInput.value);
          if (durationVal && !isNaN(pct)) {
            const minutes = Math.round((pct / 100) * durationVal);
            minInput.value = minutes || '';
          } else if (!durationVal) {
            minInput.value = '';
          }
          row.dataset.lastSource = 'percentage';
        } else if (from === 'minutes') {
          const mins = parseFloat(minInput.value);
          if (durationVal && !isNaN(mins)) {
            const pct = (mins / durationVal) * 100;
            pctInput.value = formatPct(pct) || '';
          } else if (!durationVal) {
            pctInput.value = '';
          }
          row.dataset.lastSource = 'minutes';
        } else if (from === 'duration-change') {
          const pctVal = parseFloat(pctInput.value);
          const minVal = parseFloat(minInput.value);
          if (durationVal && !isNaN(pctVal)) {
            const minutes = Math.round((pctVal / 100) * durationVal);
            minInput.value = minutes || '';
          } else if (durationVal && isNaN(pctVal) && !isNaN(minVal)) {
            const pct = (minVal / durationVal) * 100;
            pctInput.value = formatPct(pct) || '';
          }
        }
        isSyncing = false;
        updateSummary();
      };

      const attachSlotSync = (row) => {
        const pctInput = row.querySelector('input[name$=\"[percentage]\"]');
        const minInput = row.querySelector('input[name$=\"[minutes]\"]');
        if (!pctInput || !minInput) return;
        pctInput.addEventListener('input', () => syncRow(row, 'percentage'));
        minInput.addEventListener('input', () => syncRow(row, 'minutes'));
      };

      slotList.querySelectorAll('.slot-row').forEach((row) => attachSlotSync(row));

      if (durationInput) {
        durationInput.addEventListener('input', () => {
          slotList.querySelectorAll('.slot-row').forEach((row) => syncRow(row, 'duration-change'));
          updateSummary();
        });
      }

      const addSlotRow = () => {
        const row = document.createElement('div');
        row.className = 'row g-3 mb-3 slot-row align-items-center';
        row.innerHTML = `
          <div class=\"col-md-5\">
            <input type=\"text\" name=\"slots[${idx}][name]\" class=\"form-control rounded-pill px-3 shadow-sm\" placeholder=\"${i18n.namePlaceholder}\">
          </div>
          <div class=\"col-md-2\">
            <input type=\"number\" name=\"slots[${idx}][percentage]\" class=\"form-control rounded-pill px-3 shadow-sm\" min=\"0.01\" max=\"100\" step=\"0.01\" placeholder=\"%\">
          </div>
          <div class=\"col-md-2\">
            <input type=\"number\" name=\"slots[${idx}][minutes]\" class=\"form-control rounded-pill px-3 shadow-sm\" min=\"1\" placeholder=\"${i18n.minutesPlaceholder}\">
          </div>
          <div class=\"col-md-3 d-flex align-items-center gap-2\">
            <input type=\"number\" name=\"slots[${idx}][order]\" class=\"form-control rounded-pill px-3 shadow-sm\" min=\"0\" value=\"${idx}\">
            <button type=\"button\" class=\"btn btn-outline-danger rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 remove-slot shadow-sm p-0\" style=\"width: 32px; height: 32px;\"><i class=\"mdi mdi-close\"></i></button>
          </div>
        `;
        slotList.appendChild(row);
        attachSlotSync(row);
        updateSummary();
        idx++;
      };

      addBtn.addEventListener('click', addSlotRow);

      slotList.addEventListener('click', (e) => {
         const removeBtn = e.target.closest('.remove-slot');
         if (removeBtn) {
            e.preventDefault();
            const row = removeBtn.closest('.slot-row');
            if (row) row.remove();
            updateSummary();
         }
      });

      clearBtn.addEventListener('click', () => {
        slotList.innerHTML = '';
        idx = 0;
        updateSummary();
      });

      updateSummary();
    };

    initSlotProgress({
      slotListId: 'slotListEdit',
      addBtnId: 'addSlotBtnEdit',
      clearBtnId: 'clearSlotsBtnEdit',
      summaryId: 'slotSummaryEdit'
    });
  })();
</script>
@endsection
