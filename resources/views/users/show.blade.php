@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">User Details</h3>
        <p class="text-muted mb-0">{{ $user->name }}</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Employee Details</h3>
                <div class="card-tools">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary">Edit</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> {{ $user->id }}</p>
                        <p><strong>Name:</strong> {{ $user->name }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Role:</strong>
                          @php($roles = $user->getRoleNames())
                          @if($roles->isNotEmpty())
                            <span class="badge text-bg-secondary">{{ $roles->implode(', ') }}</span>
                          @else
                            <span class="badge text-bg-light">-</span>
                          @endif
                        </p>
                        <p><strong>Location:</strong> {{ $user->location ? $user->location->name : '-' }}</p>
                        <p><strong>Created:</strong> {{ $user->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
        @can('update-user-location', $user)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Transfer User to Another Location</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.transfer', $user) }}">
                    @csrf
                    @method('PATCH')
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">New Location</label>
                            <select name="location_id" class="form-select" required>
                                <option value="" disabled selected>-- Select Location --</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" @selected($user->location_id == $location->id)>{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Transfer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endcan

        @if(auth()->user()->hasRole('Super Admin'))
        <div class="card">
            <div class="card-header"><h3 class="card-title">Role Management</h3></div>
            <div class="card-body">
                @php($roles = $user->getRoleNames())
                @if($roles->contains('Location Admin'))
                    <form action="{{ route('users.demote.employee', $user) }}" method="POST" onsubmit="return confirm('Demote this user to Employee?')">
                        @csrf
                        <button type="submit" class="btn btn-warning">Demote to Employee</button>
                    </form>
                @else
                    <form action="{{ route('users.promote.location-admin', $user) }}" method="POST">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label">Assign Location</label>
                                <select name="location_id" class="form-select" required>
                                    <option value="" disabled selected>-- Select Location --</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" @selected($user->location_id == $location->id)>{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Promote to Location Admin</button>
                            </div>
                        </div>
                    </form>
                @endif

                <hr class="my-3" />

                @if($roles->contains('HR'))
                    <form action="{{ route('users.demote.employee', $user) }}" method="POST" onsubmit="return confirm('Demote this user to Employee?')">
                        @csrf
                        <button type="submit" class="btn btn-warning">Demote HR to Employee</button>
                    </form>
                @else
                    <form action="{{ route('users.promote.hr', $user) }}" method="POST" onsubmit="return confirm('Promote this user to HR? This will remove Location Admin / Employee roles.')">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary">Promote to HR</button>
                    </form>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
