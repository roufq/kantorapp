@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ __('Refine Colleague Profile') }}
          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;">{{ __('Update professional records and assignment details for accurate administration.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <a href="{{ route('employees.index') }}" class="btn btn-light border rounded-pill px-4 fw-bold text-muted shadow-sm">
            <i class="mdi mdi-arrow-left me-2"></i>{{ __('Back to Directory') }}
          </a>
        </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                <div class="card-header border-bottom border-light p-4 bg-transparent d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-account-edit-outline text-info me-2"></i>{{ __('Record Management') }}</h5>
                    <span class="badge badge-info">{{ $karyawan->email }}</span>
                </div>
                <div class="card-body p-4 p-lg-5">
                    @if($errors->any())
                        <div class="alert alert-soft-rose border-0 rounded-4 mb-5">
                            <ul class="mb-0 small fw-bold">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('employees.update', $karyawan) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Legal Name') }}</label>
                                <input type="text" name="nama" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" value="{{ old('nama', $karyawan->nama) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Official Email') }}</label>
                                <input type="email" name="email" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" value="{{ old('email', $karyawan->email) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Direct Contact') }}</label>
                                <input type="text" name="telepon" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" value="{{ old('telepon', $karyawan->telepon) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Assigned Position') }}</label>
                                <input type="text" name="jabatan" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" value="{{ old('jabatan', $karyawan->jabatan) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Primary Department') }}</label>
                                <input type="text" name="departemen" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" value="{{ old('departemen', $karyawan->departemen) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Onboarding Date') }}</label>
                                <input type="date" name="tanggal_masuk_kerja" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" value="{{ old('tanggal_masuk_kerja', optional($karyawan->tanggal_masuk_kerja)->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Division Stream') }}</label>
                                <select name="divisi_id" class="form-select rounded-pill px-4 border-light shadow-none fw-bold" required>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}" {{ old('divisi_id', $karyawan->divisi_id) == $division->id ? 'selected' : '' }}>{{ $division->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Deployment Site') }}</label>
                                @if(auth()->user()->hasRole('Location Admin'))
                                    <input type="text" class="form-control rounded-pill px-4 bg-light border-0 fw-bold" value="{{ optional(auth()->user()->location)->name }}" readonly>
                                    <input type="hidden" name="location_id" value="{{ auth()->user()->location_id }}">
                                @else
                                    <select name="location_id" class="form-select rounded-pill px-4 border-light shadow-none fw-bold" required>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('location_id', $karyawan->location_id) == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div class="col-12 mt-5 pt-3 border-top border-light text-end">
                                <a href="{{ route('employees.index') }}" class="btn btn-light rounded-pill px-5 fw-bold text-muted me-2 border">{{ __('Discard') }}</a>
                                <button type="submit" class="btn btn-dark rounded-pill px-5 fw-bold shadow-sm">
                                    {{ __('Apply Modifications') }}
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
</div>
@endsection
