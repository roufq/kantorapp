<div class="mt-4 p-4 card shadow-sm border-light rounded-4 bg-white">
  <div class="d-flex align-items-center gap-2 mb-4">
      <div class="p-2 bg-success bg-opacity-10 text-success rounded-3">
          <i class="mdi mdi-checkbox-marked-circle-outline fs-5"></i>
      </div>
      <h5 class="text-dark fw-bold mb-0">{{ __('Approval Verification') }}</h5>
  </div>
  
  <form action="{{ route('reports.approve', $report) }}" method="POST" class="d-flex flex-column gap-3">
    @csrf
    @method('PATCH')
    <div>
      <label class="form-label text-muted smaller fw-bold text-uppercase mb-2">{{ __('Verification Notes (optional)') }}</label>
      <textarea name="notes" rows="3" class="form-control rounded-4 border-light shadow-none bg-light pt-3" placeholder="{{ __('Provide reasoning for approval/rejection...') }}">{{ old('notes') }}</textarea>
    </div>
    <div class="row g-3 mt-2">
      <div class="col-sm-6">
          <button type="submit" name="status" value="approved" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-soft">
            <i class="mdi mdi-check-decagram-outline me-1"></i> {{ __('Approve Report') }}
          </button>
      </div>
      <div class="col-sm-6">
          <button type="submit" name="status" value="rejected" class="btn btn-outline-danger w-100 rounded-pill py-2 fw-bold">
            <i class="mdi mdi-close-octagon-outline me-1"></i> {{ __('Reject Report') }}
          </button>
      </div>
    </div>
  </form>
</div>
