@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Edit Location Admin</h3></div>
      <div class="card-body">
        <form action="{{ route('location-admins.update', $admin) }}" method="POST">
          @csrf
          @method('PATCH')
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $admin->name) }}" required>
            @error('name')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $admin->email) }}" required>
            @error('email')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Password (leave blank to keep)</label>
            <input type="password" name="password" class="form-control">
            @error('password')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Location</label>
            <select name="location_id" class="form-select" required>
              @foreach($locations as $loc)
                <option value="{{ $loc->id }}" {{ $admin->location_id == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
              @endforeach
            </select>
            @error('location_id')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <button type="submit" class="btn btn-primary">Update</button>
          <a href="{{ route('location-admins.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

