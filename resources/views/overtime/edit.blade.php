@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Edit Pengajuan Lembur</h1>
            <p class="text-muted mb-0">Sesuaikan detail permintaan lembur sebelum disetujui.</p>
        </div>
        <div>
            <a href="{{ route('overtime.index') }}" class="text-decoration-none">Kembali</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Edit Overtime Request</h3>
                <a href="{{ route('overtime.show', $overtime) }}" class="btn btn-sm btn-secondary">Back to Detail</a>
            </div>
            <div class="card-body">
                <form action="{{ route('overtime.update', $overtime) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" id="date" value="{{ old('date', $overtime->date->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Start Time (WIB)</label>
                                <input type="time" name="start_time" class="form-control time-24" id="start_time" value="{{ old('start_time', $overtime->start_time_wib) }}" required step="60" pattern="[0-9]{2}:[0-9]{2}" lang="id-ID" inputmode="numeric" placeholder="HH:MM">
                                <small class="form-text text-muted">Format: HH:MM (24-jam)</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">End Time (WIB)</label>
                                <input type="time" name="end_time" class="form-control time-24" id="end_time" value="{{ old('end_time', $overtime->end_time_wib) }}" required step="60" pattern="[0-9]{2}:[0-9]{2}" lang="id-ID" inputmode="numeric" placeholder="HH:MM">
                                <small class="form-text text-muted">Format: HH:MM (24-jam)</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Overtime</label>
                        <textarea name="reason" class="form-control" id="reason" rows="4" required placeholder="Please explain why you need to work overtime...">{{ old('reason', $overtime->reason) }}</textarea>
                    </div>

                    @php $isEmployee = auth()->user()->hasRole('Karyawan'); @endphp

                    @if($isEmployee)
                        <div class="mb-3">
                            <label class="form-label">Approvers</label>
                            <div class="card card-body bg-light">
                                <p class="mb-2">Pengajuan Anda akan dikirim ke:</p>
                                <ul class="mb-0">
                                    @foreach(($autoApprovers->count() ? $autoApprovers : \App\Models\User::whereIn('id', $overtime->selected_masters ?? [])->get()) as $approver)
                                        <li>{{ $approver->name }} @if($approver->hasRole('Admin Lokasi'))<span class="badge bg-info ms-1">Admin Lokasi</span>@else<span class="badge bg-primary ms-1">Super Admin</span>@endif</li>
                                    @endforeach
                                </ul>
                            </div>
                            <small class="form-text text-muted">Jika tidak ada Admin Lokasi untuk Anda, permintaan akan diteruskan ke Super Admin.</small>
                        </div>
                    @else
                        <div class="mb-3">
                            <label class="form-label">Select up to 2 Super Admins for Approval</label>
                            <div class="row">
                                @foreach($masters as $master)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input master-checkbox" type="checkbox" name="selected_masters[]" value="{{ $master->id }}" id="master{{ $master->id }}"
                                                {{ in_array($master->id, old('selected_masters', $overtime->selected_masters ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="master{{ $master->id }}">
                                                {{ $master->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small class="form-text text-muted">Select at least 1 approver (max 2).</small>
                        </div>
                    @endif

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="submitBtn" @if(!$isEmployee && count(old('selected_masters', $overtime->selected_masters ?? [])) === 0) disabled @endif>Update Request</button>
                        <a href="{{ route('overtime.show', $overtime) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>

                <hr class="my-4">

                <form action="{{ route('overtime.destroy', $overtime) }}" method="POST" onsubmit="return confirm('Cancel this overtime request?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Cancel Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

@if(!auth()->user()->hasRole('Karyawan'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.master-checkbox');
    const submitBtn = document.getElementById('submitBtn');

    function updateSubmitButton() {
        const checkedCount = document.querySelectorAll('.master-checkbox:checked').length;
        submitBtn.disabled = checkedCount === 0 || checkedCount > 2;
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.master-checkbox:checked').length;
            if (checkedCount > 2) {
                this.checked = false;
                alert('You can select at most 2 approvers.');
            }
            updateSubmitButton();
        });
    });

    updateSubmitButton();
});
</script>
@endif
@endsection
