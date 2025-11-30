@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Buat Pengajuan Lembur</h1>
            <p class="text-muted mb-0">Ajukan permintaan lembur dengan durasi dan alasan yang jelas.</p>
        </div>
        <div>
            <a href="{{ route('overtime.index') }}" class="text-decoration-none">Kembali</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Request Overtime</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('overtime.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" id="date" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Start Time (WIB)</label>
                                <input type="time" name="start_time" class="form-control" id="start_time" required step="60" pattern="[0-9]{2}:[0-9]{2}">
                                <small class="form-text text-muted">Format: HH:MM (24-jam)</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">End Time (WIB)</label>
                                <input type="time" name="end_time" class="form-control" id="end_time" required step="60" pattern="[0-9]{2}:[0-9]{2}">
                                <small class="form-text text-muted">Format: HH:MM (24-jam)</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Overtime</label>
                        <textarea name="reason" class="form-control" id="reason" rows="4" required placeholder="Please explain why you need to work overtime..."></textarea>
                    </div>
                    @php $isEmployee = auth()->user()->hasRole('Karyawan'); @endphp

                    @if($isEmployee)
                        <div class="mb-3">
                            <label class="form-label">Approvers</label>
                            <div class="card card-body bg-light">
                                <p class="mb-2">Pengajuan Anda akan dikirim ke:</p>
                                <ul class="mb-0">
                                    @foreach($autoApprovers as $approver)
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
                                            <input class="form-check-input master-checkbox" type="checkbox" name="selected_masters[]" value="{{ $master->id }}" id="master{{ $master->id }}">
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
                    <button type="submit" class="btn btn-primary" id="submitBtn" @if(!$isEmployee) disabled @endif>Submit Request</button>
                    <a href="{{ route('overtime.index') }}" class="btn btn-secondary">Cancel</a>
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
});
</script>
@endif
@endsection
