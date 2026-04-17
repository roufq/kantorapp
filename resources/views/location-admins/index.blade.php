@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;">{{ __('Location Admins') }}</h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;">{{ __('Manage location admin accounts and their assignments.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="{{ route('location-admins.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
            <i class="mdi mdi-account-plus-outline me-2"></i>{{ __('Add Admin') }}
          </a>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card shadow-sm border-0" style="border-radius: 24px !important;">
            <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-shield-account-outline text-info me-2 fs-4"></i>{{ __('Admin List') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-white">
                        <thead class="bg-white bg-opacity-5">
                            <tr>
                                <th class="ps-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1" style="width: 80px">{{ __('No') }}</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('Name') }}</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('Email') }}</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('Location') }}</th>
                                <th class="pe-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1 text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admins as $admin)
                                <tr class="border-bottom border-white border-opacity-5">
                                    <td class="ps-4 fw-bold text-muted">{{ $loop->iteration + ($admins->currentPage()-1)*$admins->perPage() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3 rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                <i class="mdi mdi-account-star-outline"></i>
                                            </div>
                                            <span class="fw-bold">{{ $admin->name }}</span>
                                        </div>
                                    </td>
                                    <td><span class="text-info small fw-medium">{{ $admin->email }}</span></td>
                                    <td>
                                        @if($admin->location)
                                            <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-1 smaller">
                                                {{ $admin->location->name }}
                                            </span>
                                        @else
                                            <span class="text-muted small italic text-opacity-50">-</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('location-admins.show', $admin) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold shadow-none" title="{{ __('View') }}">
                                                <i class="mdi mdi-eye-outline me-1"></i>{{ __('View') }}
                                            </a>
                                            <a href="{{ route('location-admins.edit', $admin) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-none" title="{{ __('Edit') }}">
                                                <i class="mdi mdi-pencil-outline me-1"></i>{{ __('Edit') }}
                                            </a>
                                            
                                            <form action="{{ route('users.demote.employee', $admin) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Demote this admin to Employee?') }}')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold shadow-none" title="{{ __('Demote') }}">
                                                    <i class="mdi mdi-account-arrow-down-outline me-1"></i>{{ __('Demote') }}
                                                </button>
                                            </form>
    
                                            <form action="{{ route('location-admins.destroy', $admin) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold shadow-none" title="{{ __('Delete') }}">
                                                    <i class="mdi mdi-trash-can-outline me-1"></i>{{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="mdi mdi-account-off-outline fs-1 text-white opacity-25 d-block mb-2"></i>
                                        <span class="text-muted italic">{{ __('No admins found.') }}</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($admins->hasPages())
                <div class="card-footer border-top border-light p-4 bg-transparent">
                    {{ $admins->links() }}
                </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.italic { font-style: italic; }
.smaller { font-size: 0.75rem; }
.letter-spacing-1 { letter-spacing: 1px; }
.table-hover tbody tr:hover { background-color: rgba(255,255,255,0.02) !important; }
</style>
@endsection
