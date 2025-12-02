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
                        <div class="recipient-dropdown position-relative">
                            <input type="hidden" name="receiver_id" id="receiver_id" required>
                            <button type="button" class="form-select text-start recipient-toggle" data-placeholder="-- Pilih user --">-- Pilih user --</button>
                            <div class="recipient-panel card shadow-sm p-2 d-none">
                                <input type="text" class="form-control form-control-sm mb-2 recipient-filter" placeholder="Cari nama/email...">
                                <div class="list-group recipient-list" style="max-height:220px; overflow:auto;">
                                    <button type="button" class="list-group-item list-group-item-action" data-user-id="" data-user-label="-- Pilih user --">-- Pilih user --</button>
                                    <button type="button" class="list-group-item list-group-item-action" data-user-id="{{ auth()->id() }}" data-user-label="— Assign to Myself ({{ auth()->user()->name }}) —">— Assign to Myself ({{ auth()->user()->name }}) —</button>
                                    @foreach($users as $user)
                                        <button type="button" class="list-group-item list-group-item-action" data-user-id="{{ $user->id }}" data-user-label="{{ $user->name }} @ {{ $user->email }}">{{ $user->name }} @ {{ $user->email }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1">Klik untuk membuka dropdown, lalu ketik untuk mencari nama/email.</small>
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
@push('styles')
<style>
  .recipient-panel { position: absolute; z-index: 1000; width: 100%; left: 0; top: 100%; border: 1px solid #dee2e6; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const dropdown = document.querySelector('.recipient-dropdown');
  if (!dropdown) return;

  const hiddenInput = dropdown.querySelector('#receiver_id');
  const toggle = dropdown.querySelector('.recipient-toggle');
  const panel = dropdown.querySelector('.recipient-panel');
  const filter = dropdown.querySelector('.recipient-filter');
  const list = dropdown.querySelector('.recipient-list');

  const closePanel = () => panel.classList.add('d-none');
  const openPanel = () => {
    panel.classList.remove('d-none');
    filter.focus();
  };

  const updateToggle = (id) => {
    const btn = list.querySelector('[data-user-id="' + id + '"]');
    toggle.textContent = btn ? btn.getAttribute('data-user-label') : (toggle.getAttribute('data-placeholder') || '-- Pilih user --');
  };
  updateToggle(hiddenInput.value);

  toggle.addEventListener('click', () => {
    panel.classList.contains('d-none') ? openPanel() : closePanel();
  });

  filter.addEventListener('input', function () {
    const term = this.value.toLowerCase();
    list.querySelectorAll('[data-user-id]').forEach(btn => {
      const text = btn.textContent.toLowerCase();
      btn.classList.toggle('d-none', term && !text.includes(term));
    });
  });

  list.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-user-id]');
    if (!btn) return;
    hiddenInput.value = btn.getAttribute('data-user-id');
    updateToggle(hiddenInput.value);
    closePanel();
  });

  document.addEventListener('click', function (e) {
    if (!dropdown.contains(e.target)) {
      closePanel();
    }
  });
});
</script>
@endpush
@endsection
