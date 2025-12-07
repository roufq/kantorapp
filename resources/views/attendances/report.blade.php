@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Attendance Report') }}</div>

                <div class="card-body">
                    @auth
                        @php
                            $effectiveName = isset($effectiveLocation) && $effectiveLocation ? ($effectiveLocation->name ?? null) : (auth()->user()->location->name ?? null);
                        @endphp
                        @if(auth()->user()->hasRole('Admin Lokasi'))
                            <div class="alert alert-info">
                                Anda melihat laporan sebagai <strong>Admin Lokasi</strong>{{ $effectiveName ? ' - ' . e($effectiveName) : '' }}.
                                @if(!empty($effectiveTemporary))
                                    <br><small class="text-muted">Catatan: perubahan lokasi sementara untuk hari ini.</small>
                                @endif
                            </div>
                        @elseif(auth()->user()->hasRole('Karyawan'))
                            <div class="alert alert-secondary">
                                Anda melihat laporan sebagai <strong>Karyawan</strong>{{ $effectiveName ? ' di lokasi ' . e($effectiveName) : '' }}.
                                @if(!empty($effectiveTemporary))
                                    <br><small class="text-muted">Catatan: perubahan lokasi sementara untuk hari ini.</small>
                                @endif
                            </div>
                        @endif
                    @endauth
                    {{-- Filters --}}
                    <form method="GET" action="{{ route('attendance.report') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="user_id" class="form-label">User</label>
                                <select name="user_id" id="user_id" class="form-control">
                                    <option value="">All Users</option>
                                    @foreach(\App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->employee->nama ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="shift_id" class="form-label">Shift</label>
                                <select name="shift_id" id="shift_id" class="form-control">
                                    <option value="">All Shifts</option>
                                    @foreach(\App\Models\Shift::active()->orderBy('name')->get() as $shift)
                                        <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>
                                            {{ $shift->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">Filter</button>
                            </div>
                        </div>
                    </form>

                    {{-- Export Button --}}
                    <div class="mb-3">
                        <a href="{{ route('attendance.export', request()->query()) }}" class="btn btn-success">
                            <i class="fas fa-download"></i> Export to Excel
                        </a>
                    </div>

                    {{-- Table --}}
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Employee Name</th>
                                <th>Shift</th>
                                <th>Roster (Slot)</th>
                                <th>Roster Status</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Location</th>
                                <th>Late</th>
                                <th>Approval Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->user->name }}</td>
                                    <td>{{ $attendance->user->employee->nama ?? 'N/A' }}</td>
                                    <td>{{ optional($attendance->shift)->name ?? '-' }}</td>
                                    @php
                                        $rKey = $attendance->user_id . '|' . $attendance->check_in_time->toDateString();
                                        $rCollection = $rosterEntries[$rKey] ?? collect();
                                        // pilih entri yang match shift_assignment jika ada, else pertama
                                        $rEntry = $rCollection->firstWhere('shift_assignment_id', $attendance->shift_assignment_id) ?? $rCollection->first();
                                        $slot = $rEntry?->slot_index;
                                        $slotRange = null;
                                        if ($rEntry && $rEntry->roster && $rEntry->roster->locationShift) {
                                            $slots = $rEntry->roster->locationShift->time_slots ?? [];
                                            if (isset($slots['start'], $slots['end'])) {
                                                $slots = [ $slots ];
                                            }
                                            if (isset($slots[$slot])) {
                                                $slotRange = ($slots[$slot]['start'] ?? '?') . ' - ' . ($slots[$slot]['end'] ?? '?');
                                            }
                                        }
                                    @endphp
                                    <td>
                                        @if($slotRange)
                                            Slot {{ ($slot ?? 0)+1 }}<br><small class="text-muted">{{ $slotRange }}</small>
                                        @elseif($rEntry)
                                            Slot {{ ($slot ?? 0)+1 }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rEntry)
                                            @if($rEntry->status === 'off')
                                                <span class="badge bg-secondary">OFF</span>
                                            @else
                                                <span class="badge bg-success">Scheduled</span>
                                            @endif
                                        @else
                                            <span class="badge bg-light text-muted">No roster</span>
                                        @endif
                                    </td>
                                    <td>{{ $attendance->check_in_time->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $attendance->check_out_time ? $attendance->check_out_time->format('Y-m-d H:i:s') : 'Not checked out' }}</td>
                                    <td>
                                        @php $locStr = $attendance->location; @endphp
                                        @if($locStr)
                                            {{ $locStr }}
                                            @php $p = explode(',', $locStr); @endphp
                                            @if(count($p) === 2)
                                                @php $plat = trim($p[0]); $plng = trim($p[1]); @endphp
                                                <br>
                                                <a href="https://www.google.com/maps?q={{ $plat }},{{ $plng }}" target="_blank" rel="noopener">Lihat di Peta</a>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $attendance->is_late ? 'Yes' : 'No' }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('attendance.update.approval', $attendance->id) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <select name="approval_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="pending" {{ $attendance->approval_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="approved" {{ $attendance->approval_status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="rejected" {{ $attendance->approval_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                        </form>
                                    </td>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    {{ $attendances->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
