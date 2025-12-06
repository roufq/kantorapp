@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Edit Shift</h1>
            <p class="text-muted mb-0">Perbarui detail shift agar sesuai kebutuhan operasional.</p>
        </div>
        <div>
            <a href="{{ route('shifts.index') }}" class="text-decoration-none">Kembali</a>
        </div>
    </div>
</div>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Shift</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shifts.index') }}">Shifts</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Edit Shift Information</h3>
                        </div>
                        <!-- /.card-header -->

                        <!-- form start -->
                        <form action="{{ route('shifts.update', $shift) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $shift->name) }}" required>
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="code">Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $shift->code) }}" required maxlength="10">
                                            @error('code')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <small class="form-text text-muted">Unique code for the shift (max 10 characters)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="day">Day</label>
                                            <select class="form-control @error('day') is-invalid @enderror" id="day" name="day">
                                                <option value="">Select Day (Optional)</option>
                                                <option value="monday" {{ old('day', $shift->day) === 'monday' ? 'selected' : '' }}>Monday</option>
                                                <option value="tuesday" {{ old('day', $shift->day) === 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                                                <option value="wednesday" {{ old('day', $shift->day) === 'wednesday' ? 'selected' : '' }}>Wednesday</option>
                                                <option value="thursday" {{ old('day', $shift->day) === 'thursday' ? 'selected' : '' }}>Thursday</option>
                                                <option value="friday" {{ old('day', $shift->day) === 'friday' ? 'selected' : '' }}>Friday</option>
                                                <option value="saturday" {{ old('day', $shift->day) === 'saturday' ? 'selected' : '' }}>Saturday</option>
                                                <option value="sunday" {{ old('day', $shift->day) === 'sunday' ? 'selected' : '' }}>Sunday</option>
                                            </select>
                                            @error('day')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <small class="form-text text-muted">Optional: Specify the day of the week for this shift</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="category">Kategori Shift <span class="text-danger">*</span></label>
                                            <select class="form-control @error('category') is-invalid @enderror" id="category" name="category" required>
                                                <option value="">Pilih kategori</option>
                                                <option value="office" {{ old('category', $shift->category) === 'office' ? 'selected' : '' }}>Office (jam pasti, libur mingguan)</option>
                                                <option value="non_office" {{ old('category', $shift->category) === 'non_office' ? 'selected' : '' }}>Non Office (3-4 shift fleksibel)</option>
                                            </select>
                                            @error('category')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <small class="form-text text-muted">Office: 1 jam masuk pasti. Non Office: multi slot/shift.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="shift_type">Shift Type <span class="text-danger">*</span></label>
                                            <select class="form-control @error('shift_type') is-invalid @enderror" id="shift_type" name="shift_type" required>
                                                <option value="">Select Shift Type</option>
                                                <option value="single" {{ old('shift_type', $shift->shift_type) == 'single' ? 'selected' : '' }}>Single Shift (Office)</option>
                                                <option value="multiple" {{ old('shift_type', $shift->shift_type) == 'multiple' ? 'selected' : '' }}>Multiple Shifts (Factory)</option>
                                            </select>
                                            @error('shift_type')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <small class="form-text text-muted">Pilih tipe berdasarkan kebutuhan lokasi/karyawan.</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Single Shift Form -->
                                <div id="singleShiftForm" style="display: none;">
                                    <h5 class="mt-4 mb-3">Single Shift Schedule</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="single_start">Start Time <span class="text-danger">*</span></label>
                                                <input type="time" class="form-control @error('time_slots.start') is-invalid @enderror" id="single_start" name="time_slots[start]" value="{{ old('time_slots.start', $shift->time_slots['start'] ?? '') }}">
                                                @error('time_slots.start')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="single_end">End Time <span class="text-danger">*</span></label>
                                                <input type="time" class="form-control @error('time_slots.end') is-invalid @enderror" id="single_end" name="time_slots[end]" value="{{ old('time_slots.end', $shift->time_slots['end'] ?? '') }}">
                                                @error('time_slots.end')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Multiple Shifts Form -->
                                <div id="multipleShiftsForm" style="display: none;">
                                    <h5 class="mt-4 mb-3">Multiple Shifts Schedule</h5>
                                    <div id="timeSlotsContainer">
                                        <!-- Time slots will be added here dynamically -->
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="addTimeSlot">
                                        <i class="fas fa-plus"></i> Add Time Slot
                                    </button>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $shift->description) }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>



                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $shift->is_active) ? 'checked' : '' }}>
                                        <label for="is_active" class="custom-control-label">
                                            Active
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Inactive shifts cannot be used for new assignments</small>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update Shift
                                </button>
                                <a href="{{ route('shifts.show', $shift) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <a href="{{ route('shifts.index') }}" class="btn btn-secondary">
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


<script>
document.addEventListener('DOMContentLoaded', function() {
    const shiftTypeSelect = document.getElementById('shift_type');
    const singleShiftForm = document.getElementById('singleShiftForm');
    const multipleShiftsForm = document.getElementById('multipleShiftsForm');
    const addTimeSlotBtn = document.getElementById('addTimeSlot');
    const timeSlotsContainer = document.getElementById('timeSlotsContainer');
    const categorySelect = document.getElementById('category');

    let timeSlotIndex = 0;

    function addTimeSlot(start = '', end = '') {
        const timeSlotHtml = `
            <div class="time-slot-item card mb-3" data-index="${timeSlotIndex}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <label>Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="time_slots[${timeSlotIndex}][start]" value="${start}" required>
                        </div>
                        <div class="col-md-5">
                            <label>End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="time_slots[${timeSlotIndex}][end]" value="${end}" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-time-slot">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        timeSlotsContainer.insertAdjacentHTML('beforeend', timeSlotHtml);
        timeSlotIndex++;
    }

    shiftTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        if (selectedType === 'single' && categorySelect.value === 'non_office') {
            categorySelect.value = 'office';
        } else if (selectedType === 'multiple' && categorySelect.value === 'office') {
            categorySelect.value = 'non_office';
        }

        if (selectedType === 'single') {
            singleShiftForm.style.display = 'block';
            multipleShiftsForm.style.display = 'none';
        } else if (selectedType === 'multiple') {
            singleShiftForm.style.display = 'none';
            multipleShiftsForm.style.display = 'block';
            if (timeSlotsContainer.children.length === 0) {
                addTimeSlot();
            }
        } else {
            singleShiftForm.style.display = 'none';
            multipleShiftsForm.style.display = 'none';
        }
    });

    addTimeSlotBtn.addEventListener('click', function() {
        addTimeSlot();
    });

    timeSlotsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-time-slot') || e.target.closest('.remove-time-slot')) {
            e.target.closest('.time-slot-item').remove();
        }
    });

    function syncCategoryToType() {
        if (categorySelect.value === 'office') {
            shiftTypeSelect.value = 'single';
        } else if (categorySelect.value === 'non_office') {
            shiftTypeSelect.value = 'multiple';
        }
        shiftTypeSelect.dispatchEvent(new Event('change'));
    }
    if (categorySelect) {
        categorySelect.addEventListener('change', syncCategoryToType);
    }

    // Preload existing slots for multiple shift
    const existingSlots = @json(old('time_slots', $shift->isMultipleShift() ? $shift->time_slots : []));
    if (Array.isArray(existingSlots) && existingSlots.length > 0) {
        existingSlots.forEach((slot) => addTimeSlot(slot.start || '', slot.end || ''));
    }

    if (categorySelect.value) {
        syncCategoryToType();
    } else if (shiftTypeSelect.value) {
        shiftTypeSelect.dispatchEvent(new Event('change'));
    }
});
</script>

@endsection
