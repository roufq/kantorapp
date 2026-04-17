@extends('layouts.appnew')

@section('content')
      <div class="row mb-4 align-items-center">
        <div class="col-lg-6">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;">{{ __('Edit Report') }}</h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;">{{ __('Update report content before sending or reviewing.') }}</p>
        </div>
        <div class="col-lg-6 text-lg-end mt-3 mt-lg-0">
          <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i>{{ __('Back') }}
          </a>
        </div>
      </div>

<div class="container-fluid">
  @if ($errors->any())
    <div class="alert alert-danger rounded-pill px-4">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <div class="card-body">
      <form action="{{ route('reports.update', $report) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label fw-bold text-dark small">{{ __('Location') }}</label>
          <input type="text" class="form-control rounded-pill px-4" value="{{ $report->location?->name ?? '-' }}" disabled>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold text-dark small">{{ __('Title') }}</label>
          <input type="text" name="title" value="{{ old('title', $report->title) }}" class="form-control rounded-pill px-4" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold text-dark small">{{ __('Description') }}</label>
          <textarea name="description" rows="4" class="form-control" required style="border-radius: 15px !important;">{{ old('description', $report->description) }}</textarea>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold text-dark small">{{ __('Add Attachment (optional, 10 MB/file)') }}</label>
          <input type="file" name="attachments[]" class="form-control rounded-pill px-4" multiple>
          @if($report->attachments->isNotEmpty())
            <div class="text-info small mt-2"><i class="mdi mdi-information-outline me-1"></i>{{ __('Existing attachments are still stored.') }}</div>
          @endif
        </div>

        <div class="d-flex gap-3 mt-4">
          <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">
            <i class="mdi mdi-content-save-outline me-2 fs-5 align-middle"></i>{{ __('Save Changes') }}
          </button>
          <a href="{{ route('reports.show', $report) }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
            {{ __('Cancel') }}
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
