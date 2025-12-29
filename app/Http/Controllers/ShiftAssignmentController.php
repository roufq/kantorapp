<?php

namespace App\Http\Controllers;

use App\Models\ShiftAssignment;
use App\Models\User;
use App\Models\Location;
use App\Models\LocationShift;
use App\Models\Shift;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftAssignmentController extends Controller
{
    private const MAX_DAILY_WORK_HOURS = 8;
    private const MAX_WEEKLY_WORK_HOURS = 40;

    public function rotate(Request $request)
    {
        $auth = Auth::user();
        $request->validate([
            'location_id' => 'nullable|exists:locations,id',
            'days' => 'nullable|integer|min:1|max:60',
        ]);

        $locationId = $auth->hasRole('Admin Lokasi') ? $auth->location_id : ($request->location_id ?: $auth->location_id);
        $days = (int) ($request->days ?: 14);

        if (!$locationId) {
            return back()->withErrors(['location_id' => 'Pilih lokasi untuk rotasi shift']);
        }

        $result = $this->generateRotation($locationId, $days);

        if ($result['status'] === 'error') {
            return back()->withErrors(['location_id' => $result['message']]);
        }

        return redirect()->route('shift-assignments.index', ['location_id' => $locationId])
            ->with('success', $result['message']);
    }

    public function index(Request $request)
    {
        $auth = Auth::user();

        $query = ShiftAssignment::with(['user', 'shift', 'location', 'locationShift.shift']);
        if ($auth->hasRole('Admin Lokasi')) {
            $query->forLocation($auth->location_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('date', [$request->date_from, $request->date_to]);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assignments = $query->orderBy('date', 'desc')->paginate(15);

        $locations = Location::active()->orderBy('name')->get();

        return view('shift-assignments.index', compact('assignments', 'locations'));
    }

    public function create()
    {
        $auth = Auth::user();
        $users = User::when($auth->hasRole('Admin Lokasi'), function ($q) use ($auth) {
            $q->where('location_id', $auth->location_id);
        })->orderBy('name')->get();

        $locationsQuery = Location::active()->orderBy('name')->with(['shifts' => function ($q) {
            $q->active()->orderBy('name');
        }]);
        if ($auth->hasRole('Admin Lokasi')) {
            $locationsQuery->where('id', $auth->location_id);
        }
        $locations = $locationsQuery->get();

        $shiftOptions = $this->buildShiftOptions($locations);
        $userLocations = $users->mapWithKeys(fn ($u) => [$u->id => $u->location_id]);
        $locationNames = $locations->mapWithKeys(fn ($loc) => [$loc->id => $loc->name]);

        return view('shift-assignments.create', compact('users', 'shiftOptions', 'userLocations', 'locationNames'));
    }

    public function store(Request $request)
    {
        $auth = Auth::user();
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'location_shift_id' => 'nullable|exists:location_shifts,id',
            'date' => 'required|date|after_or_equal:today',
            'status' => 'nullable|in:scheduled,cancelled,completed',
            'notes' => 'nullable|string|max:255',
            'handover_required' => 'nullable|boolean',
            'handover_note' => 'nullable|string',
        ]);

        $user = User::findOrFail($request->user_id);
        $locationShift = null;
        if ($request->filled('location_shift_id')) {
            $locationShift = LocationShift::with(['location', 'shift'])->findOrFail($request->location_shift_id);
        } elseif ($request->filled('shift_id')) {
            $locationShift = LocationShift::with(['location', 'shift'])
                ->where('shift_id', $request->shift_id)
                ->where('location_id', $user->location_id)
                ->first();
            if (!$locationShift) {
                return back()->withErrors(['location_shift_id' => 'Shift is not part of the user location'])->withInput();
            }
        } else {
            return back()->withErrors(['location_shift_id' => 'The location shift id field is required.'])->withInput();
        }

        if (!$user->location_id) {
            return back()->withErrors(['user_id' => 'User must be assigned to a location'])->withInput();
        }

        if ($auth->hasRole('Admin Lokasi') && $locationShift->location_id !== $auth->location_id) {
            abort(403, 'User/Shift must be within your location');
        }

        if ($locationShift->location_id !== $user->location_id) {
            return back()->withErrors(['location_shift_id' => 'Shift is not part of the user location'])->withInput();
        }

        if ($locationShift->shift && !$locationShift->shift->is_active) {
            return back()->withErrors(['location_shift_id' => 'Shift is inactive'])->withInput();
        }

        if ($this->hasOverlap($user->id, $locationShift, $request->date)) {
            return back()->withErrors(['date' => 'User has overlapping assignment around this date'])->withInput();
        }

        $assignmentHours = $this->calculateAssignmentHours($locationShift, $request->date);
        $limitError = $this->checkWorkHourLimits($user, $request->date, $assignmentHours);
        if ($limitError) {
            return back()->withErrors(['date' => $limitError])->withInput();
        }

        $assignment = ShiftAssignment::create([
            'user_id' => $user->id,
            'location_id' => $locationShift->location_id,
            'shift_id' => $locationShift->shift_id,
            'location_shift_id' => $locationShift->id,
            'date' => $request->date,
            'status' => $request->status ?: 'scheduled',
            'notes' => $request->notes,
            'handover_required' => (bool) $request->handover_required,
            'handover_note' => $request->handover_note,
        ]);

        AuditLogger::record('shift_assignment_created', $assignment, null, [
            'user_id' => $assignment->user_id,
            'location_id' => $assignment->location_id,
            'shift_id' => $assignment->shift_id,
            'location_shift_id' => $assignment->location_shift_id,
            'date' => $assignment->date,
            'status' => $assignment->status,
        ]);

        return redirect()->route('shift-assignments.index')->with('success', 'Shift assignment created');
    }

    public function edit(ShiftAssignment $shift_assignment)
    {
        $auth = Auth::user();
        $shift_assignment->load(['locationShift.shift', 'location', 'user']);
        if ($auth->hasRole('Admin Lokasi') && $shift_assignment->location_id !== $auth->location_id) {
            abort(403);
        }
        $users = User::when($auth->hasRole('Admin Lokasi'), function ($q) use ($auth) {
            $q->where('location_id', $auth->location_id);
        })->orderBy('name')->get();

        $locationsQuery = Location::query()->orderBy('name')->with(['shifts' => function ($q) {
            $q->active()->orderBy('name');
        }]);
        if ($auth->hasRole('Admin Lokasi')) {
            $locationsQuery->where('id', $auth->location_id);
        } else {
            $locationsQuery->where(function ($q) use ($shift_assignment) {
                $q->where('is_active', true)
                  ->orWhere('id', $shift_assignment->location_id);
            });
        }
        $locations = $locationsQuery->get();

        $shiftOptions = $this->buildShiftOptions($locations);
        $userLocations = $users->mapWithKeys(fn ($u) => [$u->id => $u->location_id]);
        $locationNames = $locations->mapWithKeys(fn ($loc) => [$loc->id => $loc->name]);

        return view('shift-assignments.edit', [
            'assignment' => $shift_assignment,
            'users' => $users,
            'shiftOptions' => $shiftOptions,
            'userLocations' => $userLocations,
            'locationNames' => $locationNames,
        ]);
    }

    public function update(Request $request, ShiftAssignment $shift_assignment)
    {
        $auth = Auth::user();
        if ($auth->hasRole('Admin Lokasi')) {
            if ($shift_assignment->location_id !== $auth->location_id) {
                abort(403);
            }
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'location_shift_id' => 'nullable|exists:location_shifts,id',
            'date' => 'required|date|after_or_equal:today',
            'status' => 'nullable|in:scheduled,cancelled,completed',
            'notes' => 'nullable|string|max:255',
            'handover_required' => 'nullable|boolean',
            'handover_note' => 'nullable|string',
        ]);

        $user = User::findOrFail($request->user_id);
        if (!$user->location_id) {
            return back()->withErrors(['user_id' => 'User must be assigned to a location'])->withInput();
        }

        $locationShift = null;
        if ($request->filled('location_shift_id')) {
            $locationShift = LocationShift::with(['location', 'shift'])->findOrFail($request->location_shift_id);
        } elseif ($request->filled('shift_id')) {
            $locationShift = LocationShift::with(['location', 'shift'])
                ->where('shift_id', $request->shift_id)
                ->where('location_id', $user->location_id)
                ->first();
            if (!$locationShift) {
                return back()->withErrors(['location_shift_id' => 'Shift is not part of the user location'])->withInput();
            }
        } else {
            return back()->withErrors(['location_shift_id' => 'The location shift id field is required.'])->withInput();
        }

        if ($auth->hasRole('Admin Lokasi') && $locationShift->location_id !== $auth->location_id) {
            abort(403);
        }

        if (!$user->location_id || $locationShift->location_id !== $user->location_id) {
            return back()->withErrors(['location_shift_id' => 'Shift is not part of the user location'])->withInput();
        }

        if ($locationShift->shift && !$locationShift->shift->is_active) {
            return back()->withErrors(['location_shift_id' => 'Shift is inactive'])->withInput();
        }

        if ($this->hasOverlap($user->id, $locationShift, $request->date, $shift_assignment->id)) {
            return back()->withErrors(['date' => 'User has overlapping assignment around this date'])->withInput();
        }

        $assignmentHours = $this->calculateAssignmentHours($locationShift, $request->date);
        $limitError = $this->checkWorkHourLimits($user, $request->date, $assignmentHours, $shift_assignment->id);
        if ($limitError) {
            return back()->withErrors(['date' => $limitError])->withInput();
        }

        $before = $shift_assignment->only([
            'user_id',
            'location_id',
            'shift_id',
            'location_shift_id',
            'date',
            'status',
            'notes',
            'handover_required',
            'handover_note',
        ]);

        $shift_assignment->update([
            'user_id' => $user->id,
            'location_id' => $locationShift->location_id,
            'shift_id' => $locationShift->shift_id,
            'location_shift_id' => $locationShift->id,
            'date' => $request->date,
            'status' => $request->status ?: 'scheduled',
            'notes' => $request->notes,
            'handover_required' => (bool) $request->handover_required,
            'handover_note' => $request->handover_note,
        ]);

        $after = $shift_assignment->only([
            'user_id',
            'location_id',
            'shift_id',
            'location_shift_id',
            'date',
            'status',
            'notes',
            'handover_required',
            'handover_note',
        ]);
        AuditLogger::record('shift_assignment_updated', $shift_assignment, $before, $after);

        return redirect()->route('shift-assignments.index')->with('success', 'Shift assignment updated');
    }

    public function calendar(Request $request)
    {
        $auth = Auth::user();
        $weekStart = $request->filled('week_start')
            ? Carbon::parse($request->week_start)
            : Carbon::now()->startOfWeek();
        $weekEnd = $weekStart->copy()->addDays(6);

        // Lokasi pilihan (Super Admin bebas pilih, selain itu fix lokasi user)
        $locationId = $auth->hasRole('Super Admin')
            ? ($request->input('location_id') ?: $auth->location_id)
            : $auth->location_id;

        $locations = Location::active()
            ->when(!$auth->hasRole('Super Admin'), fn($q) => $q->where('id', $locationId))
            ->orderBy('name')
            ->get();

        // Jika Super Admin belum memilih lokasi atau tidak punya default, pakai lokasi pertama yang aktif
        if (!$locationId && $auth->hasRole('Super Admin') && $locations->isNotEmpty()) {
            $locationId = $locations->first()->id;
        }

        // Ambil detail lokasi + shift untuk deteksi kategori (handle jika lokasi tidak ditemukan)
        $locationModel = $locationId ? Location::with('shifts')->find($locationId) : null;
        if (!$locationModel) {
            return back()->withErrors(['location_id' => 'Lokasi tidak ditemukan atau tidak aktif.']);
        }

        $users = User::where('location_id', $locationId)->orderBy('name')->get();

        // Ambil roster entries (prioritas)
        $rosterEntries = \App\Models\WeeklyRosterEntry::with(['user', 'roster.locationShift.shift'])
            ->whereHas('roster', fn($q) => $q->where('location_id', $locationId))
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->get()
            ->groupBy(fn($e) => $e->date->toDateString());

        // Ambil assignments untuk fallback
        // Disabled assignment fallback: kalender hanya mengikuti roster.
        $assignments = collect();

        // Ambil cuti/izin yang disetujui
        $leaves = \App\Models\EmployeeLeave::with('user')
            ->where('status', 'approved')
            ->whereBetween('start_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->orWhere(function ($q) use ($weekStart, $weekEnd) {
                $q->where('status', 'approved')
                  ->whereDate('start_date', '<=', $weekEnd->toDateString())
                  ->whereDate('end_date', '>=', $weekStart->toDateString());
            })
            ->get()
            ->filter(function ($leave) use ($locationId) {
                return $leave->location_id ? $leave->location_id == $locationId : ($leave->user?->location_id == $locationId);
            });
        $leavesPerDate = collect();
        foreach ($leaves as $leave) {
            $start = $leave->start_date->toDateString();
            $end = $leave->end_date->toDateString();
            $period = new \DatePeriod(
                \Carbon\Carbon::parse($start),
                new \DateInterval('P1D'),
                \Carbon\Carbon::parse($end)->addDay()
            );
            foreach ($period as $d) {
                $dateStr = $d->format('Y-m-d');
                if ($dateStr < $weekStart->toDateString() || $dateStr > $weekEnd->toDateString()) {
                    continue;
                }
                $leavesPerDate[$dateStr] = ($leavesPerDate[$dateStr] ?? collect())->push($leave);
            }
        }

        // Deteksi kategori non_office (factory)
        $hasNonOffice = false;
        if ($locationModel) {
            $hasNonOffice = $locationModel->shifts->contains(function ($s) {
                $cat = $s->pivot->category ?? $s->category ?? '';
                $normalized = strtolower(str_replace([' ', '-'], '_', $cat));
                return $normalized === 'non_office' || str_contains($normalized, 'factory');
            });
        }

        $weekData = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i)->toDateString();
            $rows = collect();

            // Hanya roster; jika tidak ada, tampilkan missing
            if ($rosterEntries->has($date)) {
                $rows = $rosterEntries[$date]->map(function ($e) {
                    $slots = $e->roster?->locationShift?->normalizedSlots() ?? [];
                    $slot = $slots[$e->slot_index] ?? null;
                    $time = $slot ? (($slot['start'] ?? '?') . ' - ' . ($slot['end'] ?? '?')) : '-';
                    return [
                        'user' => $e->user,
                        'status' => $e->status,
                        'time' => $e->status === 'off' ? 'Hari libur' : $time,
                        'notes' => $e->notes,
                    ];
                });
            }

            // Tambahkan baris cuti/izin jika ada
            if ($leavesPerDate->has($date)) {
                $leaveRows = $leavesPerDate[$date]->map(function ($leave) {
                    return [
                        'user' => $leave->user,
                        'status' => 'leave',
                        'time' => 'Cuti/Izin',
                        'notes' => $leave->type ? ($leave->type . ($leave->reason ? ' - ' . $leave->reason : '')) : $leave->reason,
                    ];
                });
                $rows = $rows->merge($leaveRows);
            }

            if ($rows->isEmpty()) {
                $rows = collect([[
                    'user' => null,
                    'status' => 'missing',
                    'time' => 'Shift belum ada',
                    'notes' => null,
                ]]);
            }

            $weekData[$date] = $rows;
        }

        return view('shift-assignments.calendar', [
            'locations' => $locations,
            'locationId' => $locationId,
            'weekStart' => $weekStart,
            'weekData' => $weekData,
        ]);
    }

    private function buildShiftOptions($locations): array
    {
        $options = [];
        foreach ($locations as $location) {
            $options[$location->id] = $options[$location->id] ?? [];
            foreach ($location->shifts as $shift) {
                $pivot = $shift->pivot;
                $options[$location->id][] = [
                    'location_shift_id' => $pivot->id ?? null,
                    'shift_id' => $shift->id,
                    'name' => $shift->name,
                    'category' => $pivot->category ?? $shift->category,
                    'slots' => $pivot->time_slots ?? $shift->time_slots ?? [],
                ];
            }
        }

        return $options;
    }

    private function generateRotation(int $locationId, int $days): array
    {
        $location = Location::with(['shifts' => function ($q) {
            $q->active()->orderBy('location_shifts.id');
        }])->find($locationId);

        if (!$location) {
            return ['status' => 'error', 'message' => 'Lokasi tidak ditemukan'];
        }

        $users = User::role('Karyawan')->where('location_id', $locationId)->orderBy('id')->get();
        if ($users->isEmpty()) {
            return ['status' => 'error', 'message' => 'Tidak ada karyawan di lokasi ini'];
        }

        $locationShifts = $location->shifts->map(function ($shift) {
            $shift->pivot_model = $shift->pivot;
            return $shift;
        });

        if ($locationShifts->isEmpty()) {
            return ['status' => 'error', 'message' => 'Tidak ada shift aktif untuk lokasi ini'];
        }

        $today = Carbon::today($location->timezone ?? config('app.timezone', 'UTC'));
        $created = 0;
        for ($d = 0; $d < $days; $d++) {
            $date = $today->copy()->addDays($d)->toDateString();
            foreach ($users as $idx => $u) {
                $shift = $locationShifts[($idx + $d) % $locationShifts->count()];
                $pivot = $shift->pivot_model;
                if (!$pivot || !$pivot->id) {
                    continue;
                }

                if (!ShiftAssignment::where('user_id', $u->id)->whereDate('date', $date)->exists()) {
                    $assignmentHours = $this->calculateAssignmentHours($pivot, $date);
                    $limitError = $this->checkWorkHourLimits($u, $date, $assignmentHours);
                    if ($limitError) {
                        continue;
                    }
                    ShiftAssignment::create([
                        'user_id' => $u->id,
                        'location_id' => $locationId,
                        'shift_id' => $shift->id,
                        'location_shift_id' => $pivot->id,
                        'date' => $date,
                        'status' => 'scheduled',
                        'notes' => 'Generated rotation',
                    ]);
                    $created++;
                }
            }
        }

        return ['status' => 'ok', 'message' => "Rotasi dibuat: {$created} assignment untuk {$days} hari"];
    }

    private function hasOverlap(int $userId, LocationShift $locationShift, string $date, ?int $ignoreId = null): bool
    {
        $targetDate = Carbon::parse($date, optional($locationShift->location)->timezone ?? config('app.timezone', 'UTC'));
        $proposedIntervals = $locationShift->slotIntervalsForDate($targetDate);

        if (empty($proposedIntervals)) {
            return false;
        }

        $existing = ShiftAssignment::where('user_id', $userId)
            ->whereBetween('date', [Carbon::parse($date)->subDay()->toDateString(), Carbon::parse($date)->addDay()->toDateString()])
            ->when($ignoreId, function ($q) use ($ignoreId) { $q->where('id', '!=', $ignoreId); })
            ->with(['locationShift.location', 'locationShift.shift', 'shift', 'location'])
            ->get();

        foreach ($existing as $assignment) {
            $pivot = $assignment->locationShift;
            if (!$pivot && $assignment->shift) {
                $pivot = new LocationShift([
                    'location_id' => $assignment->location_id,
                    'shift_id' => $assignment->shift_id,
                    'category' => $assignment->shift->category ?? null,
                    'time_slots' => $assignment->shift->time_slots ?? [],
                ]);
                $pivot->setRelation('shift', $assignment->shift);
                if ($assignment->location) {
                    $pivot->setRelation('location', $assignment->location);
                }
            }

            if (!$pivot) {
                continue;
            }

            $dateForExisting = Carbon::parse($assignment->date, optional($pivot->location)->timezone ?? config('app.timezone', 'UTC'));
            $existingIntervals = $pivot->slotIntervalsForDate($dateForExisting);

            foreach ($proposedIntervals as [$s1, $e1]) {
                foreach ($existingIntervals as [$s2, $e2]) {
                    if ($s1->lt($e2) && $s2->lt($e1)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function resolveWorkHourLimits(?Location $location): array
    {
        $daily = self::MAX_DAILY_WORK_HOURS;
        $weekly = self::MAX_WEEKLY_WORK_HOURS;
        if ($location && is_array($location->settings)) {
            if (!empty($location->settings['max_daily_work_hours'])) {
                $daily = (float) $location->settings['max_daily_work_hours'];
            }
            if (!empty($location->settings['max_weekly_work_hours'])) {
                $weekly = (float) $location->settings['max_weekly_work_hours'];
            }
        }
        return ['daily' => $daily, 'weekly' => $weekly];
    }

    private function calculateAssignmentHours(LocationShift $locationShift, string $date): float
    {
        $tz = optional($locationShift->location)->timezone ?? config('app.timezone', 'UTC');
        $dateObj = Carbon::parse($date, $tz);
        $intervals = $locationShift->slotIntervalsForDate($dateObj);
        $minutes = 0;
        foreach ($intervals as [$start, $end]) {
            $minutes += $start->diffInMinutes($end);
        }
        return $minutes / 60;
    }

    private function calculateAssignmentHoursFromModel(ShiftAssignment $assignment): float
    {
        $location = $assignment->locationShift?->location ?? $assignment->location;
        $tz = optional($location)->timezone ?? config('app.timezone', 'UTC');
        $dateObj = Carbon::parse($assignment->date, $tz);
        $pivot = $assignment->locationShift;
        if (!$pivot && $assignment->shift) {
            $pivot = new LocationShift([
                'location_id' => $assignment->location_id,
                'shift_id' => $assignment->shift_id,
                'category' => $assignment->shift->category ?? null,
                'time_slots' => $assignment->shift->time_slots ?? [],
            ]);
            $pivot->setRelation('shift', $assignment->shift);
            if ($location) {
                $pivot->setRelation('location', $location);
            }
        }
        if (!$pivot) {
            return 0;
        }
        $intervals = $pivot->slotIntervalsForDate($dateObj);
        $minutes = 0;
        foreach ($intervals as [$start, $end]) {
            $minutes += $start->diffInMinutes($end);
        }
        return $minutes / 60;
    }

    private function checkWorkHourLimits(User $user, string $date, float $assignmentHours, ?int $ignoreId = null): ?string
    {
        $limits = $this->resolveWorkHourLimits($user->location);
        $dateObj = Carbon::parse($date);
        $weekStart = $dateObj->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $dateObj->copy()->endOfWeek(Carbon::SUNDAY);

        $dailyAssignments = ShiftAssignment::with(['locationShift.location', 'shift', 'location'])
            ->where('user_id', $user->id)
            ->whereDate('date', $date)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where('status', '!=', 'cancelled')
            ->get();

        $weeklyAssignments = ShiftAssignment::with(['locationShift.location', 'shift', 'location'])
            ->where('user_id', $user->id)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where('status', '!=', 'cancelled')
            ->get();

        $dailyHours = $dailyAssignments->sum(fn($a) => $this->calculateAssignmentHoursFromModel($a));
        $weeklyHours = $weeklyAssignments->sum(fn($a) => $this->calculateAssignmentHoursFromModel($a));

        if ($dailyHours + $assignmentHours > $limits['daily']) {
            return 'Melebihi batas jam kerja harian (' . $limits['daily'] . ' jam).';
        }
        if ($weeklyHours + $assignmentHours > $limits['weekly']) {
            return 'Melebihi batas jam kerja mingguan (' . $limits['weekly'] . ' jam).';
        }

        return null;
    }

    public function destroy(ShiftAssignment $shift_assignment)
    {
        $auth = Auth::user();
        if ($auth->hasRole('Admin Lokasi')) {
            if ($shift_assignment->location_id !== $auth->location_id) {
                abort(403);
            }
        }
        $before = $shift_assignment->only([
            'user_id',
            'location_id',
            'shift_id',
            'location_shift_id',
            'date',
            'status',
        ]);
        $shift_assignment->delete();
        AuditLogger::record('shift_assignment_deleted', $shift_assignment, $before, null);
        return redirect()->route('shift-assignments.index')->with('success', 'Shift assignment deleted');
    }

    public function export(Request $request)
    {
        $auth = Auth::user();
        $query = ShiftAssignment::with(['user', 'shift', 'location', 'locationShift.shift']);
        if ($auth->hasRole('Admin Lokasi')) {
            $query->forLocation($auth->location_id);
        }
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('date', [$request->date_from, $request->date_to]);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Export format selection with environment fallback
        $format = strtolower((string) ($request->get('format') ?: 'auto'));
        $supportsXls = defined('Maatwebsite\\Excel\\Excel::XLS');
        $writer = null;
        $filename = null;

        if ($format === 'csv') {
            $writer = \Maatwebsite\Excel\Excel::CSV;
            $filename = 'shift_assignments.csv';
        } elseif ($format === 'xls') {
            if ($supportsXls) {
                $writer = constant('Maatwebsite\\Excel\\Excel::XLS');
                $filename = 'shift_assignments.xls';
            } else {
                $writer = \Maatwebsite\Excel\Excel::CSV;
                $filename = 'shift_assignments.csv';
            }
        } elseif ($format === 'xlsx' || ($format === 'auto' && extension_loaded('zip'))) {
            $writer = \Maatwebsite\Excel\Excel::XLSX;
            $filename = 'shift_assignments.xlsx';
        } elseif ($supportsXls) {
            $writer = constant('Maatwebsite\\Excel\\Excel::XLS');
            $filename = 'shift_assignments.xls';
        } else {
            $writer = \Maatwebsite\Excel\Excel::CSV;
            $filename = 'shift_assignments.csv';
        }
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ShiftAssignmentsExport($query), $filename, $writer);
    }
}
