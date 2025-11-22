<div class="mt-4">
  <h6>Approval</h6>
  <form action="{{ route('reports.approve', $report) }}" method="POST" class="d-flex flex-column gap-2">
    @csrf
    @method('PATCH')
    <div>
      <label class="form-label">Catatan (opsional)</label>
      <textarea name="notes" rows="2" class="form-control" placeholder="Tambahkan catatan">{{ old('notes') }}</textarea>
    </div>
    <div class="d-flex gap-2">
      <button type="submit" name="status" value="approved" class="btn btn-success">Approve</button>
      <button type="submit" name="status" value="rejected" class="btn btn-danger">Reject</button>
    </div>
  </form>
</div>
