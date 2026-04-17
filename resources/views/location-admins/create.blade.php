@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;">{{ __('Add Location Admin') }}</h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;">{{ __('Establish admin accounts to manage operations at specific locations.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="{{ route('location-admins.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i>{{ __('Back') }}
          </a>
        </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-xl-8">
          <div class="card shadow-sm border-0 p-4 p-md-5 rounded-4 border border-light shadow-lg">
            <form action="{{ route('location-admins.store') }}" method="POST">
              @csrf
              <div class="row g-4">
                <div class="col-md-12">
                  <h5 class="text-dark fw-bold mb-3 d-flex align-items-center">
                    <i class="mdi mdi-account-details-outline text-info me-2 fs-4"></i>{{ __('Admin Information') }}
                  </h5>
                </div>

                <div class="col-md-6">
                  <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2">{{ __('Name') }}</label>
                  <input type="text" name="name" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-pill px-4 shadow-none @error('name') is-invalid @enderror" 
                         value="{{ old('name') }}" required>
                  @error('name')<div class="invalid-feedback ms-3">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                  <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2">{{ __('Email') }}</label>
                  <input type="email" name="email" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-pill px-4 shadow-none @error('email') is-invalid @enderror" 
                         value="{{ old('email') }}" required>
                  @error('email')<div class="invalid-feedback ms-3">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                  <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2">{{ __('Password') }}</label>
                  <div class="input-group">
                    <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-lock-outline"></i></span>
                    <input type="password" name="password" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none @error('password') is-invalid @enderror" required>
                  </div>
                  @error('password')<div class="invalid-feedback ms-3 d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                  <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2">{{ __('Confirm Password') }}</label>
                  <div class="input-group">
                    <span class="input-group-text bg-dark border-light text-muted px-3" style="border-radius: 50rem 0 0 50rem;"><i class="mdi mdi-lock-check-outline"></i></span>
                    <input type="password" name="password_confirmation" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none" required>
                  </div>
                </div>

                <div class="col-md-12">
                  <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2">{{ __('Location') }}</label>
                  <select name="location_id" class="form-select bg-dark bg-opacity-50 border-light text-white rounded-pill px-4 shadow-none @error('location_id') is-invalid @enderror" required>
                    <option value="" disabled selected>{{ __('-- Select Location --') }}</option>
                    @foreach($locations as $loc)
                      <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                    @endforeach
                  </select>
                  @error('location_id')<div class="invalid-feedback ms-3 d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12 mt-5 text-end">
                  <a href="{{ route('location-admins.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold me-2">{{ __('Cancel') }}</a>
                  <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-lg">
                    <i class="mdi mdi-check-circle-outline me-2"></i>{{ __('Create') }}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.letter-spacing-1 { letter-spacing: 1px; }
.form-select option { background-color: #1a1d21; color: white; }
</style>
@endsection
