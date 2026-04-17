@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;">{{ __('Location Tasks') }}</h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;">{{ __('List of tasks assigned to your location.') }}</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
            <a href="{{ route('tasks.progress.approvals') }}" class="btn btn-warning rounded-pill px-4 fw-bold shadow-lg">
              <i class="mdi mdi-check-decagram-outline me-2"></i>{{ __('Approval Progress') }}
            </a>
            <a href="{{ route('location-admin-tasks.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
              <i class="mdi mdi-plus-circle-outline me-2"></i>{{ __('Assign Task') }}
            </a>
          </div>
        </div>
      </div>

      <div class="row mb-4">
        <div class="col-12">
          <div class="card shadow-sm border-0 p-4 rounded-4 shadow-sm border border-light">
            <form method="GET" action="{{ route('location-admin-tasks.index') }}" class="row g-3">
              <div class="col-md-9">
                <div class="input-group">
                  <span class="input-group-text bg-transparent border-end-0 text-muted ps-3"><i class="mdi mdi-magnify fs-5"></i></span>
                  <input type="text" name="search" class="form-control bg-transparent border-start-0 ps-0 text-white" placeholder="{{ __('Search by task title or assignee name...') }}" value="{{ request('search') }}">
                </div>
              </div>
              <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-info w-100 rounded-3 fw-bold">{{ __('Search') }}</button>
                @if(request('search'))
                  <a href="{{ route('location-admin-tasks.index') }}" class="btn btn-outline-secondary w-100 rounded-3 fw-bold">{{ __('Clear') }}</a>
                @endif
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="row g-4">
        @forelse($tasks as $task)
          <div class="col-xl-4 col-md-6">
            <div class="card h-100 shadow-sm border-0 bg-transparent card shadow-sm border-0-hover" style="border-radius: 20px !important; overflow: hidden; transition: transform 0.3s ease;">
              <div class="card bg-white bg-opacity-5 border-0 h-100">
                <div class="card-header border-bottom border-light p-4 bg-transparent">
                  <div class="d-flex justify-content-between align-items-start">
                    <h5 class="card-title mb-0"><a href="{{ route('location-admin-tasks.show', $task) }}" class="text-dark text-decoration-none fw-bold hover-info">{{ $task->title }}</a></h5>
                    @php($statusColor = $task->status === 'completed' ? 'success' : ($task->status === 'in progress' ? 'info' : 'warning'))
                    <span class="badge rounded-pill bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }} border border-{{ $statusColor }} border-opacity-25 px-2 py-1 smaller">{{ __(ucfirst($task->status)) }}</span>
                  </div>
                </div>
                <div class="card-body p-4">
                  <p class="text-muted small mb-4 line-clamp-2" style="min-height: 38px;">{{ $task->description }}</p>
                  
                  <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <span class="text-dark small fw-bold opacity-75">{{ __('Progress') }}</span>
                      <span class="text-info small fw-bold">{{ $task->progress ?? 0 }}%</span>
                    </div>
                    <div class="progress bg-white bg-opacity-10" style="height: 10px; border-radius: 5px;">
                      <div class="progress-bar bg-info shadow-sm" role="progressbar" style="width: {{ $task->progress ?? 0 }}%; border-radius: 5px;" aria-valuenow="{{ $task->progress ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>

                  <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm me-2 bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="mdi mdi-calendar-clock text-info opacity-75"></i>
                    </div>
                    <div>
                        <div class="text-muted smallest text-uppercase">{{ __('Due') }}</div>
                        <div class="text-dark small">{{ $task->due_date ? $task->due_date->format('d M Y') : __('No due date') }}</div>
                    </div>
                  </div>

                  <div class="d-flex align-items-center">
                    <div class="avatar-sm me-2 bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="mdi mdi-account-cog-outline text-warning opacity-75"></i>
                    </div>
                    <div>
                        <div class="text-muted smallest text-uppercase">{{ __('Assigned to') }}</div>
                        <div class="text-dark small fw-bold">{{ optional($task->assignee)->name ?? __('System') }}</div>
                    </div>
                  </div>
                </div>
                <div class="card-footer border-top border-light p-4 bg-transparent d-flex justify-content-end gap-2">
                  <a href="{{ route('location-admin-tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                    <i class="mdi mdi-pencil-outline me-1"></i>{{ __('Edit') }}
                  </a>
                  <form action="{{ route('location-admin-tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">
                      <i class="mdi mdi-trash-can-outline me-1"></i>{{ __('Delete') }}
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12 text-center py-5">
            <div class="card shadow-sm border-0 p-5 rounded-4">
              <i class="mdi mdi-clipboard-text-off-outline fs-1 text-white opacity-25 d-block mb-3"></i>
              <h5 class="text-dark fw-bold mb-1">{{ __('No tasks found.') }}</h5>
              <p class="text-muted mb-0">{{ __('Create a new task to get started.') }}</p>
            </div>
          </div>
        @endforelse
      </div>

      @if($tasks->hasPages())
        <div class="card-footer border-top border-light p-4 bg-transparent">
          {{ $tasks->appends(request()->query())->links() }}
        </div>
      @endif
    </div>
  </div>
</div>

<style>
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.smaller { font-size: 0.75rem; }
.smallest { font-size: 0.65rem; }
.hover-info:hover { color: #0dcaf0 !important; }
.card shadow-sm border-0-hover:hover { transform: translateY(-5px); }
</style>
@endsection
