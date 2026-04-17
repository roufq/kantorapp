<div class="card mb-4 shadow-sm border border-white border-opacity-5 rounded-4 transition-all shift-card">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex align-items-start gap-3">
                <div class="form-check custom-check pt-1">
                    <input class="form-check-input bg-dark bg-opacity-50 border-light" 
                           type="checkbox" 
                           id="shift_{{ $shift->id }}" 
                           name="shift_ids[]" 
                           value="{{ $shift->id }}" 
                           {{ $checked ? 'checked' : '' }}>
                </div>
                <div>
                    <label class="form-check-label text-dark fw-bold mb-1" for="shift_{{ $shift->id }}">
                        {{ $shift->name }} <span class="text-muted fw-normal">({{ $shift->code }})</span>
                    </label>
                    <div class="text-muted smaller d-flex align-items-center">
                        <i class="mdi mdi-clock-outline me-1"></i>
                        {{ __('Default: ') }} {{ $shift->getFormattedSchedule() }} 
                        @if($shift->day)<span class="mx-1">•</span> {{ $shift->getDayName() }}@endif
                    </div>
                </div>
            </div>
            <div class="d-flex gap-3 align-items-center flex-wrap">
                <select name="shift_category[{{ $shift->id }}]" class="form-select form-select-sm bg-dark bg-opacity-50 border-light text-white rounded-pill px-3 shadow-none smaller" style="width: auto;">
                    <option value="office" @selected($currentCategory === 'office')>Office</option>
                    <option value="non_office" @selected($currentCategory === 'non_office')>Non Office</option>
                </select>
                @if($shift->category === 'office')
                <div class="form-check custom-radio d-flex align-items-center bg-white bg-opacity-5 rounded-pill px-3 py-1 border border-light">
                    <input class="form-check-input bg-dark bg-opacity-50 border-light me-2" 
                           type="radio" 
                           name="default_shift_id" 
                           id="default_{{ $shift->id }}" 
                           value="{{ $shift->id }}" 
                           @checked(old('default_shift_id') == $shift->id)>
                    <label class="form-check-label text-dark smaller fw-bold mb-0" for="default_{{ $shift->id }}">{{ __('Default Office') }}</label>
                </div>
                @endif
            </div>
        </div>
        
        <div class="mt-4 pt-4 border-top border-white border-opacity-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-bold text-uppercase letter-spacing-1">
                    <i class="mdi mdi-layers-outline me-1"></i>{{ __('Time Slots (per location)') }}
                </span>
                <button type="button" class="btn btn-outline-info btn-sm rounded-pill px-3 fw-bold smaller add-slot" data-shift="{{ $shift->id }}">
                    <i class="mdi mdi-plus me-1"></i>{{ __('Add Slot') }}
                </button>
            </div>
            
            <div class="slot-container" data-shift="{{ $shift->id }}" data-next-index="{{ $nextIndex }}">
                @foreach($slots as $idx => $slot)
                    <div class="row g-2 align-items-end slot-row mb-3 p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-5 mx-0">
                        <div class="col-md-4">
                            <label class="form-label text-muted smaller fw-bold text-uppercase mb-2">{{ __('Day') }}</label>
                            @php $selectedDays = $slot['days'] ?? (isset($slot['day']) ? [$slot['day']] : []); @endphp
                            <select name="shift_times[{{ $shift->id }}][{{ $idx }}][days][]" class="form-select multi-day-select" multiple>
                                <option value="">{{ __('Pick days (empty = all)') }}</option>
                                @foreach(['monday'=>__('Monday'),'tuesday'=>__('Tuesday'),'wednesday'=>__('Wednesday'),'thursday'=>__('Thursday'),'friday'=>__('Friday'),'saturday'=>__('Saturday'),'sunday'=>__('Sunday')] as $dKey=>$dLabel)
                                    <option value="{{ $dKey }}" @selected(in_array($dKey, $selectedDays ?? []))>{{ $dLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted smaller fw-bold text-uppercase mb-2">{{ __('Start') }}</label>
                            <input type="time" name="shift_times[{{ $shift->id }}][{{ $idx }}][start]" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-pill px-3 shadow-none smaller" value="{{ $slot['start'] ?? '' }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted smaller fw-bold text-uppercase mb-2">{{ __('End') }}</label>
                            <input type="time" name="shift_times[{{ $shift->id }}][{{ $idx }}][end]" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-pill px-3 shadow-none smaller" value="{{ $slot['end'] ?? '' }}" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-danger w-100 rounded-pill px-3 smaller fw-bold remove-slot mb-1">
                                <i class="mdi mdi-trash-can-outline me-1"></i>{{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
