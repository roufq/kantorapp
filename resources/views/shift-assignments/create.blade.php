@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Create Shift Assignment</h3></div>
      <div class="card-body">
        <form method="POST" action="{{ route('shift-assignments.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">User</label>
            <select name="user_id" class="form-control" required>
              @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }} (ID: {{ $u->id }})</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Shift</label>
            <select name="shift_id" class="form-control" required>
              @foreach($shifts as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
              @endforeach
            </select>
            <div class="mt-2">
              <small class="text-muted me-2">Quick Templates:</small>
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="pickShiftByName('Pagi')">Pagi</button>
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="pickShiftByName('Siang')">Siang</button>
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="pickShiftByName('Malam')">Malam</button>
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="pickShiftByName('24')">24H</button>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" class="form-control" name="date" value="{{ old('date') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              @foreach(['scheduled','cancelled','completed'] as $st)
              <option value="{{ $st }}" @if(old('status')===$st) selected @endif>{{ ucfirst($st) }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <input type="text" class="form-control" name="notes" value="{{ old('notes') }}">
          </div>
          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="handover_required" name="handover_required" value="1" {{ old('handover_required') ? 'checked' : '' }}>
            <label class="form-check-label" for="handover_required">Handover Required</label>
          </div>
          <div class="mb-3">
            <label class="form-label">Handover Note</label>
            <textarea class="form-control" name="handover_note" rows="2">{{ old('handover_note') }}</textarea>
          </div>
          <button class="btn btn-primary">Save</button>
          <a class="btn btn-secondary" href="{{ route('shift-assignments.index') }}">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
  function pickShiftByName(keyword) {
    const sel = document.querySelector('select[name="shift_id"]');
    if (!sel) return;
    const kw = keyword.toLowerCase();
    for (const opt of sel.options) {
      if (opt.text.toLowerCase().includes(kw)) { sel.value = opt.value; break; }
    }
  }
</script>
@endsection
