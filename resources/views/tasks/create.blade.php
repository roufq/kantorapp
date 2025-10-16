@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ auth()->user()->role === 'master' ? 'Assign Task' : 'Create Task' }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" id="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" id="description" rows="3"></textarea>
                    </div>
                    @if(auth()->user()->role === 'master')
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign To</label>
                        <select name="assigned_to" class="form-control" id="assigned_to" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" id="due_date">
                    </div>
                    <button type="submit" class="btn btn-primary">{{ auth()->user()->role === 'master' ? 'Assign Task' : 'Create Task' }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
