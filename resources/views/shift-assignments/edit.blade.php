@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Edit Shift Assignment</h3></div>
      <div class="card-body">
        <form method="POST" action="{{ route('shift-assignments.update', $assignment) }}">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label class="form-label">User</label>
            <select name="user_id" class="form-control" required>
              @foreach($users as $u)
                <option value="{{ $u->id }}" @if($assignment->user_id===$u->id) selected @endif>{{ $u->name }} (ID: {{ $u->id }})</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Shift</label>
            <select name="shift_id" class="form-control" required>
              @foreach($shifts as $s)
                <option value="{{ $s->id }}" @if($assignment->shift_id===$s->id) selected @endif>{{ $s->name }}</option>
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
            <input type="date" class="form-control" name="date" value="{{ $assignment->date->format('Y-m-d') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              @foreach(['scheduled','cancelled','completed'] as $st)
              <option value="{{ $st }}" @if($assignment->status===$st) selected @endif>{{ ucfirst($st) }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <input type="text" class="form-control" name="notes" value="{{ $assignment->notes }}">
          </div>
          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="handover_required" name="handover_required" value="1" {{ $assignment->handover_required ? 'checked' : '' }}>
            <label class="form-check-label" for="handover_required">Handover Required</label>
          </div>
          <div class="mb-3">
            <label class="form-label">Handover Note</label>
            <textarea class="form-control" name="handover_note" rows="2">{{ $assignment->handover_note }}</textarea>
          </div>
          <button class="btn btn-primary">Update</button>
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
