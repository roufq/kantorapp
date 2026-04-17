@extends('layouts.appnew')

@section('content')
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ __('Holiday Schedules') }}
          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;">{{ __('Manage standard weekly rest days for teams and regional sites.') }}</p>
        </div>
      </div>

      <div class="row g-4">
        <!-- Add Weekly Off Form -->
        <div class="col-lg-5">
          <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="text-dark fw-bold mb-0"><i class="mdi mdi-calendar-plus text-info me-2"></i>{{ __('Define New Off-Day') }}</h5>
                </div>

                <form method="POST" action="{{ route('weekly-offs.store') }}">
                  @csrf
                  <div class="row g-4">
                    <div class="col-12">
                      <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Day Selection') }}</label>
                      <select class="form-select rounded-pill px-4 border-light shadow-none fw-bold" name="day_of_week" required>
                        @foreach(['sunday','monday','tuesday','wednesday','thursday','friday','saturday'] as $d)
                          <option value="{{ $d }}">{{ __(ucfirst($d)) }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-12">
                      <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Target Member (Optional)') }}</label>
                      @php
                        $usersQuery = \App\Models\User::orderBy('name');
                        if (auth()->user()->hasRole('Location Admin')) {
                          $usersQuery->where('location_id', auth()->user()->location_id);
                        }
                        $allUsers = $usersQuery->get();
                      @endphp
                      <select class="form-select rounded-pill px-4 border-light shadow-none fw-bold" name="user_id" id="weekly_off_user_id">
                        <option value="">{{ __('— Entire Site —') }}</option>
                        @foreach($allUsers as $u)
                          <option value="{{ $u->id }}" data-user-location="{{ $u->location_id }}">{{ $u->name }}</option>
                        @endforeach
                      </select>
                    </div>

                    @if(auth()->user()->hasRole('Super Admin'))
                    <div class="col-12">
                      <label class="form-label text-muted small fw-bold text-uppercase mb-2">{{ __('Operational Site') }}</label>
                      <select class="form-select rounded-pill px-4 border-light shadow-none fw-bold" name="location_id" id="weekly_off_location_id">
                        <option value="">{{ __('— None (Global Default) —') }}</option>
                        @foreach(\App\Models\Location::orderBy('name')->get() as $loc)
                          <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    @endif

                    <div class="col-12 mt-5">
                      <button type="submit" class="btn btn-dark w-100 rounded-pill py-3 fw-bold shadow-sm">
                        <i class="mdi mdi-content-save-outline me-2"></i>{{ __('Register Schedule') }}
                      </button>
                    </div>
                  </div>
                </form>
            </div>
          </div>
        </div>

        <!-- Weekly Off List -->
        <div class="col-lg-7">
          <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-calendar-range text-info me-2"></i>{{ __('Active Exemptions') }}</h5>
              <form method="GET" action="{{ route('weekly-offs.index') }}" class="d-flex gap-2">
                    <select name="day_of_week" class="form-select form-select-sm rounded-pill px-3 border-light shadow-none fw-bold smallest" style="width:auto">
                        <option value="">{{ __('All Days') }}</option>
                        @foreach(['sunday','monday','tuesday','wednesday','thursday','friday','saturday'] as $d)
                            <option value="{{ $d }}" @selected(request('day_of_week')===$d)>{{ __(ucfirst($d)) }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark rounded-pill px-3 fw-bold" type="submit">Go</button>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No</th>
                                <th>{{ __('Day') }}</th>
                                <th>{{ __('Scope') }}</th>
                                <th>{{ __('Site') }}</th>
                                <th class="pe-4 text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($offs as $o)
                            <tr>
                                <td class="ps-4 fw-bold text-muted" style="width: 60px;">{{ $loop->iteration + (method_exists($offs,'currentPage') ? ($offs->currentPage()-1)*$offs->perPage() : 0) }}</td>
                                <td><span class="badge badge-info px-3">{{ __(ucfirst($o->day_of_week)) }}</span></td>
                                <td>
                                    @if($o->user_id)
                                        <div class="fw-bold text-dark small">{{ optional(\App\Models\User::find($o->user_id))->name }}</div>
                                    @else
                                        <span class="text-muted smallest fw-bold text-uppercase opacity-50">{{ __('Team-wide') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($o->location_id)
                                        <span class="text-dark small fw-bold">{{ optional(\App\Models\Location::find($o->location_id))->name }}</span>
                                    @else
                                        <span class="text-muted smallest">{{ auth()->user()->hasRole('Location Admin') ? __('Regional') : 'Global' }}</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <form method="POST" action="{{ route('weekly-offs.destroy', $o) }}" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-danger shadow-none">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="py-5 opacity-25">
                                        <i class="mdi mdi-calendar-blank-outline fs-1 d-block mb-3"></i>
                                        <p class="mb-0">{{ __('No schedules defined.') }}</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if(method_exists($offs, 'links'))
                <div class="card-footer border-top border-light p-4 bg-transparent d-flex justify-content-end">
                    {{ $offs->links() }}
                </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
    .smallest { font-size: 0.7rem; }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const locSelect = document.getElementById('weekly_off_location_id');
    const userSelect = document.getElementById('weekly_off_user_id');
    if (!locSelect || !userSelect) return;
    const originalOptions = Array.from(userSelect.options);
    function filterUsers(){
      const locId = locSelect.value;
      userSelect.innerHTML = '';
      userSelect.appendChild(originalOptions[0].cloneNode(true));
      originalOptions.slice(1).forEach(opt => {
        const userLoc = opt.getAttribute('data-user-location');
        if (!locId || userLoc === locId) {
          userSelect.appendChild(opt.cloneNode(true));
        }
      });
      userSelect.value = '';
    }
    locSelect.addEventListener('change', filterUsers);
    filterUsers();
});
</script>
@endpush
