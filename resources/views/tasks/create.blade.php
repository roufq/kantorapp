@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">{{ (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')) ? 'Buat / Assign Tugas' : 'Buat Tugas' }}</h1>
            <p class="text-muted mb-0">
                {{ (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')) ? 'Super Admin atau Admin Lokasi dapat menetapkan tugas ke karyawan/admin lokasi, atau diri sendiri.' : 'Buat tugas untuk diri sendiri dan pantau progresnya.' }}
            </p>
        </div>
        <div>
            <a href="{{ route('tasks.index') }}" class="text-decoration-none">Kembali</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')) ? 'Assign Task' : 'Create Task' }}</h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <div class="fw-semibold mb-1">Validasi gagal:</div>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="border rounded p-3 h-100">
                                <div class="fw-semibold mb-2">Informasi Tugas</div>
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" id="description" rows="10">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="border rounded p-3 h-100">
                                <div class="fw-semibold mb-2">Penugasan & Jadwal</div>
                                @if(auth()->user()->hasRole('Super Admin'))
                                <div class="mb-3">
                                    <label for="location_filter" class="form-label">Lokasi</label>
                                    <select id="location_filter" class="form-select">
                                        <option value="">-- Semua lokasi --</option>
                                        @foreach($locations ?? [] as $location)
                                            @php
                                                $shiftNames = $location->shifts->pluck('name')->map(fn ($n) => strtolower($n));
                                                $shiftLabel = $shiftNames->contains('factory multiple shifts')
                                                    ? 'Shift'
                                                    : ($shiftNames->contains('office standard shift') ? 'Non-shift' : 'Tidak diketahui');
                                            @endphp
                                            <option value="{{ $location->id }}">
                                                {{ $location->name }} ({{ $shiftLabel }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Pilih lokasi untuk memfilter user dan task catalog.</small>
                                </div>
                                @endif
                                <div class="mb-3">
                                    <label for="task_catalog_id" class="form-label">Task Catalog</label>
                                    <select name="task_catalog_id" id="task_catalog_id" class="form-select" required>
                                        <option value="">-- Pilih Task Catalog --</option>
                                        @foreach($catalogs ?? [] as $catalog)
                                            <option value="{{ $catalog->id }}" data-jobdesk-id="{{ $catalog->jobdesk_id }}" data-location-id="{{ $catalog->jobdesk->location_id ?? '' }}" data-unit="{{ $catalog->unit }}" data-value="{{ $catalog->value }}" @selected(old('task_catalog_id') == $catalog->id)>
                                                {{ $catalog->name }} ({{ $catalog->unit }} {{ $catalog->value }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Catalog akan difilter sesuai jobdesk karyawan yang dipilih.</small>
                                </div>
                                <div class="mb-3" id="durationField">
                                    <label for="duration_minutes" class="form-label">Durasi (menit)</label>
                                    <input type="number" name="duration_minutes" class="form-control" id="duration_minutes" min="1" placeholder="Misal 240 untuk 4 jam" value="{{ old('duration_minutes') }}">
                                    <small class="text-muted" id="durationHelp">Total menit yang akan dibagi ke slot progres. Due date tetap berlaku sebagai target akhir.</small>
                                </div>
                                @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi'))
                                <div class="mb-3">
                                    <label for="assigned_to" class="form-label">Assign To</label>
                                    <div class="assigned-to-dropdown position-relative">
                                        <input type="hidden" name="assigned_to" id="assigned_to" value="{{ old('assigned_to') }}" required>
                                        <button type="button" class="form-select text-start assigned-to-toggle" data-placeholder="-- Pilih user --">-- Pilih user --</button>
                                        <div class="assigned-to-panel card shadow-sm p-2 d-none" style="position:absolute; z-index:1000; width:100%; left:0; top:100%; border:1px solid #dee2e6;">
                                            <input type="text" class="form-control form-control-sm mb-2 assigned-to-filter" placeholder="Cari nama/email...">
                                            <div class="list-group assigned-to-list" style="max-height:220px; overflow:auto;">
                                                <button type="button" class="list-group-item list-group-item-action" data-user-id="" data-user-label="-- Pilih user --">-- Pilih user --</button>
                                                <button type="button" class="list-group-item list-group-item-action" data-user-id="{{ auth()->id() }}" data-location-id="{{ auth()->user()->location_id ?? '' }}" data-user-label="-- Assign to Myself ({{ auth()->user()->name }}) --">-- Assign to Myself ({{ auth()->user()->name }}) --</button>
                                                @foreach($users as $user)
                                                    @php
                                                        $jobdeskIds = $userJobdeskMap[$user->id] ?? [];
                                                    @endphp
                                                    <button type="button" class="list-group-item list-group-item-action"
                                                        data-user-id="{{ $user->id }}"
                                                        data-location-id="{{ $user->location_id ?? '' }}"
                                                        data-user-label="{{ $user->name }} @ {{ $user->email }}"
                                                        data-jobdesks="{{ implode(',', $jobdeskIds) }}">
                                                        {{ $user->name }} @ {{ $user->email }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-1">Klik untuk membuka dropdown, lalu ketik untuk mencari nama/email.</small>
                                </div>
                                @endif
                                <div class="mb-0">
                                    <label for="due_date" class="form-label">Due Date</label>
                                    <input type="date" name="due_date" class="form-control" id="due_date" value="{{ old('due_date') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted">Setelah dibuat, progres 0-100% diupdate lewat halaman detail tugas menggunakan bukti berupa link.</p>
                    <hr>
                    <h5 class="mb-2">Slot Progres (wajib, total % = 100%)</h5>
                    @php
                        $oldSlots = old('slots', [['name' => '', 'percentage' => '', 'minutes' => '', 'order' => 0]]);
                    @endphp
                    <div id="slotList" data-initial-count="{{ count($oldSlots) }}">
                        @foreach($oldSlots as $idx => $slot)
                            <div class="row g-2 mb-2 slot-row">
                                <div class="col-md-4">
                                    @if($idx === 0)
                                        <label class="form-label">Nama / Tujuan</label>
                                    @endif
                                    <input type="text" name="slots[{{ $idx }}][name]" class="form-control" placeholder="Contoh: Desain UI" required value="{{ $slot['name'] }}">
                                </div>
                                <div class="col-md-3">
                                    @if($idx === 0)
                                        <label class="form-label">Persentase (%)</label>
                                    @endif
                                    <input type="number" name="slots[{{ $idx }}][percentage]" class="form-control" min="0.01" max="100" step="0.01" placeholder="25" required value="{{ $slot['percentage'] }}">
                                </div>
                                <div class="col-md-3">
                                    @if($idx === 0)
                                        <label class="form-label">Menit</label>
                                    @endif
                                    <input type="number" name="slots[{{ $idx }}][minutes]" class="form-control" min="1" placeholder="60" required value="{{ $slot['minutes'] }}">
                                </div>
                                <div class="col-md-2">
                                    @if($idx === 0)
                                        <label class="form-label">Urutan</label>
                                    @endif
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="number" name="slots[{{ $idx }}][order]" class="form-control" min="0" value="{{ $slot['order'] ?? $idx }}">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-slot">X</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div id="slotSummary" class="small text-muted mb-2"></div>
                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addSlotBtn">Tambah Slot</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSlotsBtn">Hapus Semua Slot</button>
                        <span class="small text-muted ms-2">Minimal 1 slot. Total persentase harus 100%, total menit harus sama dengan durasi (jika diisi).</span>
                    </div>

                    <button type="submit" class="btn btn-primary">{{ (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')) ? 'Assign Task' : 'Create Task' }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
  window.userJobdeskMap = {!! json_encode($userJobdeskMap ?? []) !!};
</script>
<script src="{{ asset('js/tasks-create.js') }}"></script>
@endpush
@endsection
