@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;">{{ __('Users') }}</h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;">{{ __('Manage user accounts, roles, and employee associations.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="{{ route('users.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
            <i class="mdi mdi-account-plus-outline me-2"></i>{{ __('Add User') }}
          </a>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card shadow-sm border-0" style="border-radius: 24px !important;">
            <div class="card-header border-bottom border-light p-4 bg-transparent">
              <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-account-group text-info me-2 fs-4"></i>{{ __('User Management') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-white">
                        <thead class="bg-white bg-opacity-5">
                            <tr>
                                <th class="ps-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1" style="width: 70px">{{ __('No') }}</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('Name') }}</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('Email') }}</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('Role') }}</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('Employee') }}</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">{{ __('Location') }}</th>
                                <th class="pe-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1 text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                <tr class="border-bottom border-white border-opacity-5">
                                    <td class="ps-4 text-muted fw-bold">{{ $loop->iteration + ($employees->currentPage()-1)*$employees->perPage() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3 rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                <i class="mdi mdi-account-outline"></i>
                                            </div>
                                            <span class="fw-bold">{{ $employee->name }}</span>
                                        </div>
                                    </td>
                                    <td><span class="text-info small fw-medium">{{ $employee->email }}</span></td>
                                    <td>
                                      @php($roles = $employee->getRoleNames())
                                      @if($roles->isNotEmpty())
                                          @foreach($roles as $role)
                                              <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 smaller mb-1">{{ __($role) }}</span>
                                          @endforeach
                                      @else
                                          <span class="text-muted smaller italic">-</span>
                                      @endif
                                    </td>
                                    <td>
                                        <span class="text-dark small">{{ $employee->employee ? $employee->employee->nama : '-' }}</span>
                                    </td>
                                    <td>
                                        @if($employee->location)
                                            <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-1 smaller">
                                                {{ $employee->location->name }}
                                            </span>
                                        @else
                                            <span class="text-muted smaller italic">-</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="{{ route('users.show', $employee) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold shadow-none">
                                                <i class="mdi mdi-eye-outline me-1"></i>{{ __('View') }}
                                            </a>
                                            <a href="{{ route('users.edit', $employee) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-none">
                                                <i class="mdi mdi-pencil-outline me-1"></i>{{ __('Edit') }}
                                            </a>
                                            <form action="{{ route('users.destroy', $employee) }}" method="POST" onsubmit="return confirm('{{ __('Delete this user?') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold shadow-none">
                                                    <i class="mdi mdi-trash-can-outline me-1"></i>{{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="mdi mdi-account-off-outline fs-1 text-white opacity-25 d-block mb-2"></i>
                                        <span class="text-muted italic">{{ __('No data yet') }}</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($employees->hasPages())
                <div class="card-footer border-top border-light p-4 bg-transparent">
                    {{ $employees->links() }}
                </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
