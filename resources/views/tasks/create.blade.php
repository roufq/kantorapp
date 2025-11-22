@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')) ? 'Assign Task' : 'Create Task' }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" id="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" id="description" rows="3"></textarea>
                    </div>
                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi'))
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign To</label>
                        <input type="text" id="userFilter" class="form-control mb-2" placeholder="Filter user by name/email...">
                        <select name="assigned_to" class="form-control" id="assigned_to" required size="8">
                            <option value="{{ auth()->id() }}">— Assign to Myself ({{ auth()->user()->name }}) —</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} @ {{ $user->email }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Tip: Ketik pada filter untuk mencari cepat.</small>
                    </div>
                    @endif
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" id="due_date">
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo (Optional)</label>
                        <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Document (Optional)</label>
                        <input type="file" name="document" id="document" class="form-control" accept=".pdf,.doc,.docx,.txt">
                    </div>
                    <button type="submit" class="btn btn-primary">{{ (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')) ? 'Assign Task' : 'Create Task' }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
  const filterInput = document.getElementById('userFilter');
  const selectEl = document.getElementById('assigned_to');
  if (filterInput && selectEl) {
    filterInput.addEventListener('input', function() {
      const term = this.value.toLowerCase();
      for (const opt of selectEl.options) {
        if (!opt.value) continue;
        const txt = opt.textContent.toLowerCase();
        opt.hidden = term && !txt.includes(term);
      }
    });
  }
</script>
@endsection
