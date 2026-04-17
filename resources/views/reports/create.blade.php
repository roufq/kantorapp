@extends('layouts.appnew')

@section('content')
<div class="row mb-5 align-items-center">
    <div class="col-lg-7">
        <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ __('New Documentation') }}
        </h1>
        <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;">{{ __('Create a verified field report for administrative review.') }}</p>
    </div>
    <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-light text-dark border bg-white rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-keyboard-backspace me-2 fs-5 align-middle"></i>{{ __('Back to List') }}
        </a>
    </div>
</div>

<div class="card border-light shadow-soft rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 p-4">
        <div class="d-flex align-items-center gap-2">
            <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                <i class="mdi mdi-file-plus-outline fs-4"></i>
            </div>
            <h5 class="mb-0 text-dark fw-bold">{{ __('Document Verification Details') }}</h5>
        </div>
    </div>
    <div class="card-body p-4 pt-0">
        @if ($errors->any())
            <div class="alert badge-danger bg-opacity-10 border-0 p-3 mb-4 rounded-4">
                <ul class="mb-0 smaller fw-bold py-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-group mb-4">
                        <label class="form-label text-muted smaller fw-bold text-uppercase mb-2 letter-spacing-1">{{ __('Operation Locale') }}</label>
                        @if(auth()->user()->hasRole('Super Admin'))
                            <select name="location_id" class="form-select fw-bold" required>
                                <option value="">{{ __('Select Deployment Location') }}</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" @selected(old('location_id')==$loc->id)>{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <div class="p-3 bg-light rounded-4 border border-light d-flex align-items-center gap-2">
                                <i class="mdi mdi-map-marker-radius text-danger fs-5"></i>
                                <span class="text-dark fw-bold small">{{ auth()->user()->location?->name ?? 'Headquarters' }}</span>
                                <input type="hidden" name="location_id" value="{{ auth()->user()->location_id }}">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-4">
                        <label class="form-label text-muted smaller fw-bold text-uppercase mb-2 letter-spacing-1">{{ __('Report Classification (Title)') }}</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="form-control" required placeholder="e.g. Infrastructure Maintenance Log">
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group mb-4">
                        <label class="form-label text-muted smaller fw-bold text-uppercase mb-2 letter-spacing-1">{{ __('Executive Detailed Narrative') }}</label>
                        <textarea name="description" rows="5" class="form-control rounded-4" required placeholder="Provide a comprehensive breakdown of the report findings or ticket issue...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-4">
                        <label class="form-label text-muted smaller fw-bold text-uppercase mb-2 letter-spacing-1">{{ __('Supporting Evidence (Optional)') }}</label>
                        <div class="p-4 bg-light rounded-4 border border-dashed text-center">
                            <input type="file" name="attachments[]" class="form-control d-none" id="file_upload" multiple>
                            <label for="file_upload" style="cursor: pointer;">
                                <i class="mdi mdi-cloud-upload-outline fs-1 text-primary mb-2 d-block"></i>
                                <div class="text-dark fw-bold smaller">{{ __('Click to upload evidence') }}</div>
                                <div class="text-muted smaller mt-1">{{ __('Limit: Max 10 MB per file (Images, Documents)') }}</div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-end mt-4">
                <a href="{{ route('reports.index') }}" class="btn btn-outline-light text-dark border bg-white rounded-pill px-5 fw-bold">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-soft">
                    <i class="mdi mdi-check-decagram-outline me-2"></i>{{ __('Verify & Submit') }}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .smaller { font-size: 0.75rem; }
    .letter-spacing-1 { letter-spacing: 0.5px; }
    .shadow-soft { box-shadow: 0 10px 30px rgba(0,0,0,0.03) !important; }
</style>
@endsection
