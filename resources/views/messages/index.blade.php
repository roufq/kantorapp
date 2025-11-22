@extends('layouts.app')
@section('title')
<div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Messages</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Messages</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
@endsection
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Conversations</h3>
            </div>
            <div class="card-body">
                @foreach($conversations as $userId => $messages)
                    <div class="conversation mb-3">
                        <a href="{{ route('messages.show', $userId) }}" class="text-decoration-none">
                            <h5>{{ $messages->first()->sender_id == auth()->id() ? $messages->first()->receiver->name : $messages->first()->sender->name }}</h5>
                            <p>{{ $messages->first()->message }}</p>
                            <small>{{ $messages->first()->created_at->diffForHumans() }}</small>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Send Message</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('messages.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="receiver_id" class="form-label">To</label>
                        <select name="receiver_id" class="form-select" id="receiver_id" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea name="message" class="form-control" id="message" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
