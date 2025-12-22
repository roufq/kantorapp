@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Detail Pesan</h3>
        <p class="text-muted mb-0">Chat dengan {{ $user->name }}</p>
    </div>
    <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Chat dengan {{ $user->name }}</h3>
            </div>
            <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                @foreach($messages as $message)
                    <div class="message mb-3 {{ $message->sender_id == auth()->id() ? 'text-end' : '' }}">
                        <div class="d-inline-block p-2 rounded {{ $message->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
                            <p class="mb-1">{{ $message->message }}</p>
                            <small class="text-muted">{{ $message->created_at->format('d M Y H:i') }}</small>
                            @if(auth()->user()->role === 'master')
                                <form action="{{ route('messages.destroy', $message->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this message?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger ms-2">Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card-footer">
                <form action="{{ route('messages.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                    <div class="input-group">
                        <textarea name="message" class="form-control" rows="2" placeholder="Type your message..." required></textarea>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
