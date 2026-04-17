@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;">{{ (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Location Admin')) ? __('Assign Task') : __('Create Task') }}</h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;">
            {{ (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Location Admin')) ? __('Super Admin or Location Admin can assign tasks to employees/location admins, or themselves.') : __('Create task for yourself and monitor progress.') }}
          </p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i>{{ __('Back') }}
          </a>
        </div>
      </div>

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

                    <form action="{{ route('tasks.store') }}" method="POST">
                        @csrf
                        <div class="row g-4">
                            <div class="col-lg-7">
                                <div class="card shadow-sm border-0 p-4 rounded-4 border border-light h-100">
                                    <h5 class="text-dark fw-bold mb-4 d-flex align-items-center">
                                        <i class="mdi mdi-information-outline me-2 text-info"></i>{{ __('Task Information') }}
                                    </h5>
                                    <div class="mb-3">
                                        <label for="title" class="form-label text-muted small fw-bold">{{ __('Title') }}</label>
                                        <input type="text" name="title" class="form-control rounded-pill px-4 shadow-sm" id="title" value="{{ old('title') }}" required placeholder="Enter task title">
                                    </div>
                                    <div class="mb-0">
                                        <label for="description" class="form-label text-muted small fw-bold">{{ __('Description') }}</label>
                                        <textarea name="description" class="form-control" id="description" rows="12" required placeholder="Describe the task details..." style="border-radius: 15px !important;"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="card shadow-sm border-0 p-4 rounded-4 border border-light h-100">
                                    <h5 class="text-dark fw-bold mb-4 d-flex align-items-center">
                                        <i class="mdi mdi-account-cog-outline me-2 text-warning"></i>{{ __('Assignment & Schedule') }}
                                    </h5>
                                    
                                    @if(auth()->user()->hasRole('Super Admin'))
                                    <div class="mb-3">
                                        <label for="location_filter" class="form-label text-muted small fw-bold">{{ __('Location') }}</label>
                                        <select id="location_filter" class="form-select rounded-pill px-4 shadow-sm">
                                            <option value="">{{ __('-- All locations --') }}</option>
                                            @foreach($locations ?? [] as $location)
                                                @php
                                                    $shiftNames = $location->shifts->pluck('name')->map(fn ($n) => strtolower($n));
                                                    $shiftLabel = $shiftNames->contains('factory multiple shifts')
                                                        ? __('Factory')
                                                        : ($shiftNames->contains('office standard shift') ? __('Office') : __('Unknown Shift'));
                                                @endphp
                                                <option value="{{ $location->id }}">
                                                    {{ $location->name }} ({{ $shiftLabel }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted italic smaller mt-1 d-block">{{ __('Select location to filter users and task catalog.') }}</small>
                                    </div>
                                    @endif

                                    <div class="mb-3">
                                        <label for="task_catalog_id" class="form-label text-muted small fw-bold">{{ __('Task Catalog') }}</label>
                                        <select name="task_catalog_id" id="task_catalog_id" class="form-select rounded-pill px-4 shadow-sm" required>
                                            <option value="">{{ __('-- Select Task Catalog --') }}</option>
                                            @foreach($catalogs ?? [] as $catalog)
                                                <option value="{{ $catalog->id }}" data-jobdesk-id="{{ $catalog->jobdesk_id }}" data-location-id="{{ $catalog->jobdesk->location_id ?? '' }}" data-unit="{{ $catalog->unit }}" data-value="{{ $catalog->value }}" @selected(old('task_catalog_id') == $catalog->id)>
                                                    {{ $catalog->name }} ({{ $catalog->unit }} {{ $catalog->value }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted italic smaller mt-1 d-block">{{ __('Catalog will be filtered by the selected employee\'s jobdesk.') }}</small>
                                    </div>

                                    <div class="mb-3" id="durationField">
                                        <label for="duration_minutes" class="form-label text-muted small fw-bold">{{ __('Duration (minutes):') }}</label>
                                        <input type="number" name="duration_minutes" class="form-control rounded-pill px-4 shadow-sm" id="duration_minutes" min="1" placeholder="{{ __('e.g. 240 for 4 hours') }}" value="{{ old('duration_minutes') }}">
                                        <small class="text-muted italic smaller mt-1 d-block" id="durationHelp">{{ __('Total minutes to be split into progress slots. Due date still applies as final target.') }}</small>
                                    </div>

                                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Location Admin'))
                                    <div class="mb-3">
                                        <label for="assigned_to" class="form-label text-muted small fw-bold">{{ __('Assign To') }}</label>
                                        <div class="assigned-to-dropdown position-relative">
                                            <input type="hidden" name="assigned_to" id="assigned_to" value="{{ old('assigned_to') }}" required>
                                            <button type="button" class="form-select text-start assigned-to-toggle rounded-pill px-4 shadow-sm" data-placeholder="{{ __('-- Select user --') }}">{{ __('-- Select user --') }}</button>
                                            <div class="assigned-to-panel card shadow-sm border-0 shadow-lg p-2 d-none border border-light" style="position:absolute; z-index:1000; width:100%; left:0; top:105%; border-radius: 15px;">
                                                <input type="text" class="form-control form-control-sm mb-2 rounded-pill px-3 bg-dark bg-opacity-50 border-light text-white shadow-none assigned-to-filter" placeholder="Search name/email...">
                                                <div class="list-group assigned-to-list" style="max-height:220px; overflow:auto; scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.1) transparent;">
                                                    <button type="button" class="list-group-item list-group-item-action bg-white text-dark border-0 py-2 smaller" data-user-id="" data-user-label="{{ __('-- Select user --') }}">{{ __('-- Select user --') }}</button>
                                                    <button type="button" class="list-group-item list-group-item-action bg-transparent text-info border-0 py-2 smaller italic fw-bold" data-user-id="{{ auth()->id() }}" data-location-id="{{ auth()->user()->location_id ?? '' }}" data-user-label="{{ __('-- Assign to Myself') }} ({{ auth()->user()->name }})">
                                                        <i class="mdi mdi-account-circle-outline me-1"></i> {{ __('-- Assign to Myself') }} ({{ auth()->user()->name }})
                                                    </button>
                                                    @foreach($users as $user)
                                                        @php
                                                            $jobdeskIds = $userJobdeskMap[$user->id] ?? [];
                                                        @endphp
                                                        <button type="button" class="list-group-item list-group-item-action bg-white text-dark border-0 py-2 smaller"
                                                            data-user-id="{{ $user->id }}"
                                                            data-location-id="{{ $user->location_id ?? '' }}"
                                                            data-user-label="{{ $user->name }} @ {{ $user->email }}"
                                                            data-jobdesks="{{ implode(',', $jobdeskIds) }}">
                                                            {{ $user->name }} <span class="text-muted smaller">({{ $user->email }})</span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted italic smaller mt-1 d-block">{{ __('Click to open dropdown, then type to search name/email.') }}</small>
                                    </div>
                                    @endif

                                    <div class="mb-0">
                                        <label for="due_date" class="form-label text-muted small fw-bold">{{ __('Due Date') }}</label>
                                        <input type="date" name="due_date" class="form-control rounded-pill px-4 shadow-sm" id="due_date" value="{{ old('due_date') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-4 card shadow-sm border-0 rounded-4 border border-info border-opacity-10">
                            <h5 class="text-dark fw-bold mb-2 d-flex align-items-center">
                                <i class="mdi mdi-layers-triple-outline me-2 text-info"></i>{{ __('Progress Slots (required, total % = 100%)') }}
                            </h5>
                            <p class="text-muted smaller mb-4">{{ __('Once created, 0-100% progress is updated via task detail page using link evidence.') }}</p>
                            
                            @php
                                $oldSlots = old('slots', [['name' => '', 'percentage' => '', 'minutes' => '', 'order' => 0]]);
                            @endphp
                            
                            <div id="slotList" data-initial-count="{{ count($oldSlots) }}" class="mb-3">
                                @foreach($oldSlots as $idx => $slot)
                                    <div class="row g-3 mb-3 slot-row align-items-center">
                                        <div class="col-md-5">
                                            @if($idx === 0)
                                                <label class="form-label text-muted smaller fw-bold">{{ __('Name / Target') }}</label>
                                            @endif
                                            <input type="text" name="slots[{{ $idx }}][name]" class="form-control rounded-pill px-3 shadow-sm" placeholder="{{ __('e.g. UI Design') }}" required value="{{ $slot['name'] }}">
                                        </div>
                                        <div class="col-md-2">
                                            @if($idx === 0)
                                                <label class="form-label text-muted smaller fw-bold">{{ __('Percentage (%)') }}</label>
                                            @endif
                                            <input type="number" name="slots[{{ $idx }}][percentage]" class="form-control rounded-pill px-3 shadow-sm" min="0.01" max="100" step="0.01" placeholder="25" required value="{{ $slot['percentage'] }}">
                                        </div>
                                        <div class="col-md-2">
                                            @if($idx === 0)
                                                <label class="form-label text-muted smaller fw-bold">{{ __('Minutes') }}</label>
                                            @endif
                                            <input type="number" name="slots[{{ $idx }}][minutes]" class="form-control rounded-pill px-3 shadow-sm" min="1" placeholder="60" required value="{{ $slot['minutes'] }}">
                                        </div>
                                        <div class="col-md-3">
                                            @if($idx === 0)
                                                <label class="form-label text-muted smaller fw-bold">{{ __('Order') }}</label>
                                            @endif
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="number" name="slots[{{ $idx }}][order]" class="form-control rounded-pill px-3 shadow-sm" min="0" value="{{ $slot['order'] ?? $idx }}">
                                                <button type="button" class="btn btn-outline-danger rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 remove-slot shadow-sm p-0" style="width: 32px; height: 32px;">
                                                    <i class="mdi mdi-close"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div id="slotSummary" class="small fw-bold mb-3 p-2 px-3 rounded-pill bg-white bg-opacity-5 border border-white border-opacity-5 d-inline-block"></div>
                            
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-0">
                                <button type="button" class="btn btn-outline-info rounded-pill px-4 fw-bold shadow-sm" id="addSlotBtn">
                                    <i class="mdi mdi-plus-circle-outline me-1"></i>{{ __('Add Slot') }}
                                </button>
                                <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold shadow-sm" id="clearSlotsBtn">
                                    <i class="mdi mdi-trash-can-outline me-1"></i>{{ __('Delete All Slots') }}
                                </button>
                                <span class="smaller text-muted italic ms-lg-3">
                                    <i class="mdi mdi-information-outline me-1"></i>{{ __('Min. 1 slot. Total percentage must be 100%, total minutes must match duration (if filled).') }}
                                </span>
                            </div>
                        </div>

                        <div class="text-end mt-5 pt-4 border-top border-white border-opacity-5">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-lg fs-5">
                                <i class="mdi mdi-check-all me-2"></i>{{ (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Location Admin')) ? __('Assign Task') : __('Create Task') }}
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

@push('scripts')
<script>
  window.userJobdeskMap = {!! json_encode($userJobdeskMap ?? []) !!};
  // Translations for JS
  window.i18n = {
      nameTarget: "{{ __('Name / Target') }}",
      percentage: "{{ __('Percentage (%)') }}",
      minutes: "{{ __('Minutes') }}",
      order: "{{ __('Order') }}",
      uiDesignPlaceholder: "{{ __('e.g. UI Design') }}",
  };
</script>
<script src="{{ asset('js/tasks-create.js') }}"></script>
@endpush
@endsection
