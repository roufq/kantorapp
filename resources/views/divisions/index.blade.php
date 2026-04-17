@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ __('Divisions') }}
          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;">{{ __('Organize your company departments and team structures.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <a href="{{ route('divisions.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
            <i class="mdi mdi-plus-circle-outline me-2"></i>{{ __('Add Division') }}
          </a>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-sitemap-outline text-info me-2"></i>{{ __('Departmental Organization') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No</th>
                                <th>{{ __('Division Name') }}</th>
                                <th class="pe-4 text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($divisions as $division)
                            <tr>
                                <td class="ps-4 fw-bold text-muted" style="width: 60px;">{{ $loop->iteration + ($divisions->currentPage()-1)*$divisions->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-light border rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px;">
                                            <i class="mdi mdi-account-group-outline"></i>
                                        </div>
                                        <span class="fw-bold text-dark fs-6">{{ $division->nama }}</span>
                                    </div>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('divisions.show', $division) }}" class="btn btn-sm btn-light border rounded-pill px-3 shadow-none fw-bold text-info"><i class="mdi mdi-eye-outline me-1"></i>View</a>
                                        <a href="{{ route('divisions.edit', $division) }}" class="btn btn-sm btn-light border rounded-pill px-3 shadow-none fw-bold text-primary"><i class="mdi mdi-pencil-outline me-1"></i>Edit</a>
                                        <form action="{{ route('divisions.destroy', $division) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border rounded-pill px-3 shadow-none fw-bold text-danger"><i class="mdi mdi-trash-can-outline me-1"></i>Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <div class="py-5 opacity-25">
                                        <i class="mdi mdi-folder-outline fs-1 d-block mb-3"></i>
                                        <p class="mb-0">{{ __('No entries yet.') }}</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($divisions->hasPages())
                <div class="card-footer border-top border-light p-4 bg-transparent d-flex justify-content-end">
                    {{ $divisions->links() }}
                </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
