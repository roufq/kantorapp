@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Location Change Request</h1>

    <form action="{{ route('location-change-requests.store') }}" method="POST">
        @csrf
        @if(isset($users) && $users)
        <div class="form-group mb-3">
            <label for="user_id">Employee</label>
            <input type="text" id="userFilter" class="form-control mb-2" placeholder="Filter user by name/email...">
            <select name="user_id" id="user_id" class="form-control" required size="8">
                @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }} @ {{ $u->email }} ({{ $u->location->name ?? 'No Location' }})</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="form-group">
            <label for="target_location_id">Target Location</label>
            <select name="target_location_id" id="target_location_id" class="form-control" required>
                @foreach($locations as $location)
                <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
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
        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="is_permanent" name="is_permanent" value="1">
            <label for="is_permanent" class="form-check-label">Permanent transfer</label>
            <div><small class="text-muted">Uncheck for one-day move; approval still required.</small></div>
        </div>
        @if(auth()->user()->hasRole('Admin Lokasi') || auth()->user()->hasRole('Super Admin'))
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
  const filterInput = document.getElementById('userFilter');
  const selectEl = document.getElementById('user_id');
  if (filterInput && selectEl) {
    filterInput.addEventListener('input', function() {
      const term = this.value.toLowerCase();
      for (const opt of selectEl.options) {
        const txt = opt.textContent.toLowerCase();
        opt.hidden = term && !txt.includes(term);
      }
    });
  }
</script>
@endpush
