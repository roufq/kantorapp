@extends('layouts.appnew')
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
@php
  $activeUserId = request('user_id') ?? ($contacts->keys()->first() ?? null);
  $activeMessages = ($activeUserId && $conversations->has($activeUserId)) ? $conversations[$activeUserId] : collect();
  $activeContact = $activeUserId && $contacts->has($activeUserId) ? $contacts[$activeUserId] : null;
  $activeDisplayName = $activeContact['user']->name ?? null;
@endphp
<div class="row">
    <div class="col-12 col-lg-4 col-xl-3 mb-3">
        <div class="card conversation-card h-100">
            <div class="card-header bg-transparent border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Chats</h3>
                    <span class="badge bg-primary-subtle text-primary">{{ $contacts->count() }} aktif</span>
                </div>
                <div class="mt-3">
                    <input type="text" class="form-control form-control-sm" id="conversation-search" placeholder="Cari percakapan...">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="chat-list" id="conversation-list">
                    @forelse($contacts as $contactUserId => $contact)
                        @php
                          $displayName = $contact['user']->name;
                          $last = $contact['last_message'];
                          $lastText = $last?->message ?? 'Belum ada pesan';
                          $lastTime = $last?->created_at?->diffForHumans() ?? '-';
                        @endphp
                        <a href="{{ route('messages.index', ['user_id' => $contactUserId]) }}" class="chat-tile text-decoration-none {{ $contactUserId == $activeUserId ? 'active' : '' }}" data-name="{{ strtolower($displayName) }}" data-text="{{ strtolower($lastText) }}">
                            <div class="chat-avatar">{{ strtoupper(substr($displayName, 0, 1)) }}</div>
                            <div class="chat-meta">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="chat-name">{{ $displayName }}</span>
                                    <span class="chat-time">{{ $lastTime }}</span>
                                </div>
                                <div class="chat-preview text-truncate">{{ $lastText }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center text-muted py-4">Belum ada percakapan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-8 col-xl-9">
        <div class="card send-card h-100">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $activeDisplayName ?? 'Pilih percakapan' }}</h5>
                        @if($activeDisplayName)
                          <small class="text-muted">Sedang aktif</small>
                        @endif
                    </div>
                    @if($activeUserId)
                      <span class="badge bg-success-subtle text-success">Online</span>
                    @endif
                </div>
            </div>
            <div class="card-body d-flex flex-column chat-window">
                <div class="chat-thread flex-grow-1" id="chat-thread">
                  @if($activeMessages->isEmpty())
                    <div class="d-flex h-100 align-items-center justify-content-center text-muted">Pilih percakapan di kiri.</div>
                  @else
                    @foreach($activeMessages as $msg)
                      @php $isMine = $msg->sender_id == auth()->id(); @endphp
                      <div class="chat-bubble {{ $isMine ? 'mine' : 'theirs' }}">
                        <div class="bubble-body">{{ $msg->message }}</div>
                        <div class="bubble-meta">{{ $msg->created_at->format('H:i') }}</div>
                      </div>
                    @endforeach
                  @endif
                </div>
                <form action="{{ route('messages.store') }}" method="POST" class="chat-composer mt-3">
                    @csrf
                    <input type="hidden" name="receiver_id" id="receiver_id" value="{{ $activeUserId }}" required>
                    <div class="input-group">
                        <textarea name="message" class="form-control" id="message" rows="2" required placeholder="Tulis pesan..."></textarea>
                        <button type="submit" class="btn btn-primary px-4">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('styles')
<style>
  .conversation-card { background: #f8fafc; border-radius: 12px; }
  .chat-list { max-height: 75vh; overflow-y: auto; }
  .chat-tile {
    display: flex;
    gap: 12px;
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
    transition: background 0.2s ease;
    color: inherit;
  }
  .chat-tile:hover { background: #eef2ff; }
  .chat-avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4f46e5, #22c55e);
    color: #fff; display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 18px;
  }
  .chat-meta { flex: 1; min-width: 0; }
  .chat-name { font-weight: 700; color: #0b5ed7; display: inline-block; }
  .chat-time { font-size: 12px; color: #9ca3af; margin-left: 8px; white-space: nowrap; }
  .chat-preview { font-size: 13px; color: #4b5563; }
  .chat-tile.active { background: #eef2ff; border-left: 4px solid #4f46e5; }
  .send-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
  }
  .send-card .card-header {
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
  }
  .chat-preview-pane {
    padding: 12px;
    background: #f8fafc;
    border: 1px dashed #d5d8de;
    border-radius: 10px;
  }
  .chat-window { min-height: 60vh; }
  .chat-thread {
    background: #f8fafc;
    padding: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    overflow-y: auto;
  }
  .chat-bubble {
    max-width: 70%;
    padding: 10px 12px;
    border-radius: 12px;
    position: relative;
    font-size: 14px;
  }
  .chat-bubble.mine {
    align-self: flex-end;
    background: #dbeafe;
    border: 1px solid #bfdbfe;
  }
  .chat-bubble.theirs {
    align-self: flex-start;
    background: #fff;
    border: 1px solid #e5e7eb;
  }
  .bubble-body { color: #1f2937; }
  .bubble-meta { font-size: 12px; color: #6b7280; margin-top: 4px; text-align: right; }
  .chat-composer .form-control { resize: none; }
  .recipient-panel { position: absolute; z-index: 1000; width: 100%; left: 0; top: 100%; border: 1px solid #dee2e6; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const convoSearch = document.getElementById('conversation-search');
  const convoList = document.getElementById('conversation-list');
  const thread = document.getElementById('chat-thread');

  if (convoSearch && convoList) {
    convoSearch.addEventListener('input', function () {
      const term = this.value.toLowerCase();
      convoList.querySelectorAll('.chat-tile').forEach(tile => {
        const name = tile.getAttribute('data-name') || '';
        const text = tile.getAttribute('data-text') || '';
        const match = name.includes(term) || text.includes(term);
        tile.classList.toggle('d-none', term && !match);
      });
    });
  }

  if (thread) {
    thread.scrollTop = thread.scrollHeight;
  }
});
</script>
@endpush
@endsection
