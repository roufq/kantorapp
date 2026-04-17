<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\LocationShift;
use App\Models\ShiftAssignment;
use App\Models\EmployeeLeave;
use App\Models\User;
use App\Models\WeeklyRoster;
use App\Models\WeeklyRosterEntry;
use App\Exports\WeeklyRosterExport;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;

class ShiftRosterController extends Controller
{
    private const MAX_DAILY_WORK_HOURS = 8;
    private const MAX_WEEKLY_WORK_HOURS = 40;

    public function index(Request $request)
    {
        $auth = auth()->user();
        $rostersQuery = WeeklyRoster::with(['location', 'locationShift.shift'])
            ->orderBy('week_start', 'desc');

        if ($auth->hasRole('Location Admin')) {
            $rostersQuery->where('location_id', $auth->location_id);
        }

        $rosters = $rostersQuery->paginate(15);

        return view('shifts.rosters.index', compact('rosters'));
    }

    public function calendar(Request $request)
    {
        $auth = auth()->user();
        $focusDate = $request->filled('week_start')
            ? Carbon::parse($request->week_start)
            : Carbon::now();
        $weekStart = $focusDate->copy()->startOfWeek();
        $weekEnd = $weekStart->copy()->addDays(6);
        $monthStart = $focusDate->copy()->startOfMonth();
        $monthEnd = $focusDate->copy()->endOfMonth();

        $locationId = $auth->hasRole('Super Admin')
            ? ($request->input('location_id') ?: $auth->location_id)
            : $auth->location_id;

        $locations = Location::active()
            ->when(!$auth->hasRole('Super Admin'), fn($q) => $q->where('id', $locationId))
            ->orderBy('name')
            ->get();

        if (!$locationId && $auth->hasRole('Super Admin') && $locations->isNotEmpty()) {
            $locationId = $locations->first()->id;
        }

        $locationModel = $locationId ? Location::with('shifts')->find($locationId) : null;
        if (!$locationModel) {
            return back()->withErrors(['location_id' => 'Lokasi tidak ditemukan atau tidak aktif.']);
        }

        $users = User::where('location_id', $locationId)->orderBy('name')->get();
        $locationCategories = $locationModel->shifts->map(function ($shift) {
            return $shift->pivot->category ?? $shift->category;
        })->filter()->unique()->values();
        $locationCategory = $locationCategories->first();

        $rosterEntries = WeeklyRosterEntry::with(['user', 'roster.locationShift.shift'])
            ->whereHas('roster', fn($q) => $q->where('location_id', $locationId))
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get()
            ->groupBy(fn($e) => $e->date->toDateString());

        $leaves = EmployeeLeave::with('user')
            ->where('status', 'approved')
            ->where(function ($q) use ($monthStart, $monthEnd) {
                $q->whereBetween('start_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                    ->orWhere(function ($sub) use ($monthStart, $monthEnd) {
                        $sub->whereDate('start_date', '<=', $monthEnd->toDateString())
                            ->whereDate('end_date', '>=', $monthStart->toDateString());
                    });
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
                Carbon::parse($start),
                new \DateInterval('P1D'),
                Carbon::parse($end)->copy()->addDay()
            );
            foreach ($period as $d) {
                $dateStr = $d->format('Y-m-d');
                if ($dateStr < $weekStart->toDateString() || $dateStr > $weekEnd->toDateString()) {
                    continue;
                }
                $leavesPerDate[$dateStr] = ($leavesPerDate[$dateStr] ?? collect())->push($leave);
            }
        }

        $calendarEvents = collect();

        if ($locationCategory === 'office') {
            $locationShifts = LocationShift::with('shift')
                ->where('location_id', $locationId)
                ->where('category', 'office')
                ->get();

            $period = new \DatePeriod(
                $monthStart,
                new \DateInterval('P1D'),
                $monthEnd->copy()->addDay()
            );

            foreach ($period as $d) {
                $dateStr = $d->format('Y-m-d');
                $dayKey = strtolower($d->format('l'));
                foreach ($locationShifts as $locationShift) {
                    $slots = $locationShift->normalizedSlots();
                    foreach ($slots as $slot) {
                        if (!empty($slot['days'])) {
                            $dayList = array_map('strtolower', $slot['days']);
                            if (!in_array($dayKey, $dayList, true)) {
                                continue;
                            }
                        }
                        $time = ($slot['start'] ?? '?') . ' - ' . ($slot['end'] ?? '?');
                        $calendarEvents->push([
                            'title' => $locationShift->shift?->name ?? 'Office Shift',
                            'start' => $dateStr,
                            'allDay' => true,
                            'className' => 'fc-event-on',
                            'notes' => null,
                            'status' => 'on',
                            'time' => $time,
                        ]);
                    }
                }
            }
        } else {
            foreach ($rosterEntries as $date => $entries) {
                foreach ($entries as $entry) {
                    $slots = $entry->roster?->locationShift?->normalizedSlots() ?? [];
                    $slot = $slots[$entry->slot_index] ?? null;
                    $time = $entry->status === 'off'
                        ? __('Weekly Off')
                        : ($slot ? (($slot['start'] ?? '?') . ' - ' . ($slot['end'] ?? '?')) : '-');

                    $calendarEvents->push([
                        'title' => ($entry->user->name ?? '-') . ' • ' . ($entry->roster?->locationShift?->shift?->name ?? 'Duty'),
                        'start' => $entry->date->toDateString(),
                        'allDay' => true,
                        'className' => match ($entry->status) {
                            'off' => 'fc-event-off',
                            'leave' => 'fc-event-leave',
                            'missing' => 'fc-event-missing',
                            default => 'fc-event-on',
                        },
                        'user_id' => $entry->user_id,
                        'user_name' => $entry->user?->name,
                        'notes' => $entry->notes,
                        'status' => $entry->status,
                        'time' => $time,
                    ]);
                }
            }
        }

        foreach ($leavesPerDate as $date => $leavesForDate) {
            foreach ($leavesForDate as $leave) {
                $calendarEvents->push([
                    'title' => ($leave->user?->name ?? '-') . ' • Cuti/Izin',
                    'start' => $date,
                    'allDay' => true,
                    'className' => 'fc-event-leave',
                    'notes' => $leave->type ? ($leave->type . ($leave->reason ? ' - ' . $leave->reason : '')) : $leave->reason,
                    'status' => 'leave',
                    'time' => 'Cuti/Izin',
                ]);
            }
        }

        $datesWithEvents = $calendarEvents->pluck('start')->unique();
        $period = new \DatePeriod(
            $monthStart,
            new \DateInterval('P1D'),
            $monthEnd->copy()->addDay()
        );
        foreach ($period as $d) {
            $dateStr = $d->format('Y-m-d');
            if (!$datesWithEvents->contains($dateStr)) {
                $calendarEvents->push([
                    'title' => __('No schedule found'),
                    'start' => $dateStr,
                    'allDay' => true,
                    'className' => 'fc-event-missing',
                    'status' => 'missing',
                    'time' => null,
                    'notes' => null,
                ]);
            }
        }

        return view('shifts.rosters.calendar', [
            'locations' => $locations,
            'locationId' => $locationId,
            'focusDate' => $focusDate,
            'monthStart' => $monthStart,
            'monthEnd' => $monthEnd,
            'calendarEvents' => $calendarEvents->values(),
        ]);
    }

    public function create(Request $request)
    {
        $auth = auth()->user();
        $locationsQuery = Location::active()->orderBy('name');
        if ($auth->hasRole('Location Admin')) {
            $locationsQuery->where('id', $auth->location_id);
        }
        $locations = $locationsQuery->get();
        $locationId = $request->location_id ?: $locations->first()->id ?? null;
        $locationShifts = collect();
        $users = collect();
        if ($locationId) {
            $locationShifts = LocationShift::with('shift')
                ->where('location_id', $locationId)
                ->whereHas('shift', function ($q) {
                    $q->where('category', 'non_office');
                })
                ->get();
            $users = User::role('Employee')->where('location_id', $locationId)->orderBy('name')->get();
        }

        $weekStart = Carbon::parse($request->week_start ?? Carbon::now()->startOfWeek());
        $weekEnd = $weekStart->copy()->addDays(6);

        return view('shifts.rosters.create', compact('locations', 'locationId', 'locationShifts', 'users', 'weekStart', 'weekEnd'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'location_shift_id' => 'required|exists:location_shifts,id',
            'week_start' => 'required|date',
            'week_end' => 'required|date|after_or_equal:week_start',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'weekly_off_every' => 'required|integer|min:1',
            'weekly_off_label' => 'nullable|string|max:50',
        ]);

        // Jangan buat roster jika sudah ada di minggu ini
        if (WeeklyRoster::where('location_id', $data['location_id'])
            ->where('week_start', $data['week_start'])
            ->exists()) {
            return back()->withErrors(['week_start' => 'Roster minggu ini sudah ada untuk lokasi tersebut.'])->withInput();
        }

        $auth = auth()->user();
        $locationShift = LocationShift::with('shift')->findOrFail($data['location_shift_id']);
        if ($locationShift->location_id != $data['location_id']) {
            return back()->withErrors(['location_shift_id' => 'Shift tidak terikat ke lokasi ini.'])->withInput();
        }
        if ($auth->hasRole('Location Admin') && $auth->location_id != $data['location_id']) {
            return back()->withErrors(['location_id' => 'Anda hanya boleh membuat roster untuk lokasi Anda.'])->withInput();
        }

        $users = User::whereIn('id', $data['user_ids'])
            ->where('location_id', $data['location_id'])
            ->get();
        if ($users->count() !== count($data['user_ids'])) {
            return back()->withErrors(['user_ids' => 'Terdapat user di luar lokasi ini.'])->withInput();
        }

        $weekStart = Carbon::parse($data['week_start']);
        $weekEnd = Carbon::parse($data['week_end']);
        $weeklyOffEvery = $data['weekly_off_every'];
        $weeklyOffLabel = $data['weekly_off_label'] ?? 'OFF';
        $limits = $this->resolveWorkHourLimits($locationShift->location);

        $roster = WeeklyRoster::create([
            'location_id' => $data['location_id'],
            'location_shift_id' => $data['location_shift_id'],
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'locked' => false,
            'meta' => ['weekly_off_every' => $weeklyOffEvery, 'weekly_off_label' => $weeklyOffLabel],
        ]);

        // Ambil slot ter-normalisasi dari pivot; fallback ke shift->normalizedSlots()
        $slots = $locationShift->normalizedSlots();
        if (empty($slots)) {
            return back()->withErrors(['location_shift_id' => 'Shift lokasi ini belum memiliki slot waktu.'])->withInput();
        }
        // Pastikan slot memiliki start/end valid dan reindex
        $slots = collect($slots)
            ->filter(fn ($slot) => !empty($slot['start']) && !empty($slot['end']))
            ->values()
            ->all();
        if (empty($slots)) {
            return back()->withErrors(['location_shift_id' => 'Slot shift tidak valid (start/end kosong).'])->withInput();
        }

        $userIndex = 0;
        $userCount = $users->count();
        $rotationPointer = 0;
        $weeklyCounts = [];
        $lastSlotByUser = [];
        $lastNightMetaByUser = [];
        $assignmentLog = [];
        $dailyHours = [];
        $weeklyHours = [];
        $existingAssignmentsByUser = [];

        $assignmentWindowStart = $weekStart->copy()->subDay()->toDateString();
        $assignmentWindowEnd = $weekEnd->copy()->addDay()->toDateString();
        $existingAssignments = ShiftAssignment::with(['locationShift.location', 'shift', 'location'])
            ->whereIn('user_id', $users->pluck('id'))
            ->whereBetween('date', [$assignmentWindowStart, $assignmentWindowEnd])
            ->where('status', '!=', 'cancelled')
            ->get()
            ->groupBy('user_id');

        foreach ($users as $user) {
            $dailyHours[$user->id] = [];
            $weeklyHours[$user->id] = 0;
            $existingAssignmentsByUser[$user->id] = $existingAssignments[$user->id] ?? collect();
            foreach ($existingAssignmentsByUser[$user->id] as $assignment) {
                $hours = $this->calculateAssignmentHoursFromModel($assignment);
                $assignmentDate = $assignment->date instanceof Carbon
                    ? $assignment->date->toDateString()
                    : Carbon::parse($assignment->date)->toDateString();
                $dateKey = $assignmentDate;
                $dailyHours[$user->id][$dateKey] = ($dailyHours[$user->id][$dateKey] ?? 0) + $hours;
                if ($assignmentDate >= $weekStart->toDateString() && $assignmentDate <= $weekEnd->toDateString()) {
                    $weeklyHours[$user->id] += $hours;
                }
            }
        }

        $period = new \DatePeriod($weekStart, new \DateInterval('P1D'), $weekEnd->copy()->addDay());
        foreach ($period as $day) {
            $dateStr = $day->format('Y-m-d');
            $dayNumber = Carbon::parse($day)->diffInDays($weekStart) + 1;
            $maxPerSlot = 2; // kapasitas per slot per hari (boleh 2 orang per slot)

            // OFF day per user tiap weekly_off_every hari (round robin)
            $offUsers = [];
            foreach ($users as $idx => $user) {
                if ($weeklyOffEvery > 0 && ($dayNumber + $idx) % $weeklyOffEvery === 0) {
                    WeeklyRosterEntry::create([
                        'weekly_roster_id' => $roster->id,
                        'user_id' => $user->id,
                        'date' => $dateStr,
                        'slot_index' => 0,
                        'status' => 'off',
                        'notes' => $weeklyOffLabel,
                    ]);
                    $offUsers[$user->id] = true;
                }
            }

            // Assign slot per hari: round robin user & slot
            $workingUsers = $users->filter(fn($u) => !isset($offUsers[$u->id]))->values();
            if ($workingUsers->isEmpty() || empty($slots)) {
                continue;
            }
            $workCount = $workingUsers->count();

            // Putar antrean berdasarkan pointer (carry-over antar hari)
            $rotationOffset = $rotationPointer % $workCount;
            $rotated = $workingUsers->slice($rotationOffset)->concat($workingUsers->take($rotationOffset))->values();
            $rotationIndex = [];
            foreach ($rotated as $idx => $u) {
                $rotationIndex[$u->id] = $idx;
            }

            // Urutkan kandidat berdasar fairness (jumlah shift) + posisi rotasi
            $candidates = $rotated->sortBy(function ($u) use ($weeklyCounts, $rotationIndex) {
                $cnt = $weeklyCounts[$u->id] ?? 0;
                $rot = $rotationIndex[$u->id] ?? 0;
                return sprintf('%05d-%05d', $cnt, $rot);
            })->values();

            $assignedToday = [];
            $slotUsage = array_fill(0, count($slots), 0);
            $slotCursor = 0;

            foreach ($candidates as $candidate) {
                if (isset($assignedToday[$candidate->id])) {
                    continue;
                }
                // cari slot yang belum penuh
                $chosenSlotIdx = null;
                $chosenSlot = null;
                $chosenHours = 0;
                for ($i = 0; $i < count($slots); $i++) {
                    $idx = ($slotCursor + $i) % count($slots);
                    if (!isset($slots[$idx])) {
                        continue;
                    }
                    if ($slotUsage[$idx] >= $maxPerSlot) {
                        continue;
                    }
                    $slot = $slots[$idx];
                    $prevSlot = $lastSlotByUser[$candidate->id] ?? null;
                    $nightMeta = $lastNightMetaByUser[$candidate->id] ?? ['count' => 0, 'last_date' => null];
                    $nightCount = $nightMeta['count'] ?? 0;
                    $nightLastDate = $nightMeta['last_date'] ?? null;
                    $isNightSlot = $this->isNightSlot($slot);
                    if ($isNightSlot && $nightLastDate) {
                        $diff = Carbon::parse($dateStr)->diffInDays(Carbon::parse($nightLastDate));
                        if ($diff > 1) {
                            $nightCount = 0;
                        }
                    }
                    if ($isNightSlot && $nightCount >= 2) {
                        continue;
                    }
                    if ($this->isNightToMorningTransition($prevSlot, $slot)) {
                        continue;
                    }
                    $slotInterval = $this->buildSlotIntervalForDate($slot, Carbon::parse($dateStr), $locationShift->location);
                    if (!$slotInterval) {
                        $assignmentLog[] = [
                            'date' => $dateStr,
                            'slot_index' => $idx,
                            'user_id' => $candidate->id,
                            'reason' => 'slot_invalid_day',
                        ];
                        continue;
                    }
                    if ($this->hasTimeConflictForUser($slotInterval, $existingAssignmentsByUser[$candidate->id] ?? collect())) {
                        $assignmentLog[] = [
                            'date' => $dateStr,
                            'slot_index' => $idx,
                            'user_id' => $candidate->id,
                            'reason' => 'time_conflict',
                        ];
                        continue;
                    }
                    $slotHours = $this->intervalHours($slotInterval);
                    $existingDailyHours = $dailyHours[$candidate->id][$dateStr] ?? 0;
                    $existingWeeklyHours = $weeklyHours[$candidate->id] ?? 0;
                    if ($existingDailyHours + $slotHours > $limits['daily']) {
                        $assignmentLog[] = [
                            'date' => $dateStr,
                            'slot_index' => $idx,
                            'user_id' => $candidate->id,
                            'reason' => 'limit_daily',
                        ];
                        continue;
                    }
                    if ($existingWeeklyHours + $slotHours > $limits['weekly']) {
                        $assignmentLog[] = [
                            'date' => $dateStr,
                            'slot_index' => $idx,
                            'user_id' => $candidate->id,
                            'reason' => 'limit_weekly',
                        ];
                        continue;
                    }
                    $chosenSlotIdx = $idx;
                    $chosenSlot = $slot;
                    $chosenHours = $slotHours;
                    $slotCursor = $idx + 1;
                    break;
                }

                if ($chosenSlotIdx === null) {
                    continue; // tidak ada slot tersedia
                }

                $slot = $chosenSlot ?? $slots[$chosenSlotIdx];

                // Buat / ambil shift assignment aktual (deduplikasi per user+date)
                $assignment = ShiftAssignment::updateOrCreate(
                    [
                        'user_id' => $candidate->id,
                        'date' => $dateStr,
                        'location_id' => $locationShift->location_id,
                        'location_shift_id' => $locationShift->id,
                    ],
                    [
                        'shift_id' => $locationShift->shift_id,
                        'status' => 'scheduled',
                        'notes' => null,
                    ]
                );

                // Deduplikasi entri roster per user+date+slot_index
                WeeklyRosterEntry::updateOrCreate(
                    [
                        'weekly_roster_id' => $roster->id,
                        'user_id' => $candidate->id,
                        'date' => $dateStr,
                        'slot_index' => $chosenSlotIdx,
                    ],
                    [
                        'shift_assignment_id' => $assignment->id,
                        'status' => 'scheduled',
                        'notes' => null,
                    ]
                );

                $weeklyCounts[$candidate->id] = ($weeklyCounts[$candidate->id] ?? 0) + 1;
                $lastSlotByUser[$candidate->id] = $slot;
                $assignedToday[$candidate->id] = true;
                $slotUsage[$chosenSlotIdx] += 1;

                $existingAssignmentsByUser[$candidate->id] = ($existingAssignmentsByUser[$candidate->id] ?? collect())->push($assignment);
                $dailyHours[$candidate->id][$dateStr] = ($dailyHours[$candidate->id][$dateStr] ?? 0) + $chosenHours;
                $weeklyHours[$candidate->id] = ($weeklyHours[$candidate->id] ?? 0) + $chosenHours;

                $prevNightMeta = $lastNightMetaByUser[$candidate->id] ?? ['count' => 0, 'last_date' => null];
                $nightCount = 0;
                if ($isNightSlot) {
                    $prevDate = $prevNightMeta['last_date'] ?? null;
                    $prevCount = $prevNightMeta['count'] ?? 0;
                    if ($prevDate && Carbon::parse($dateStr)->diffInDays(Carbon::parse($prevDate)) <= 1) {
                        $nightCount = $prevCount + 1;
                    } else {
                        $nightCount = 1;
                    }
                    $lastNightMetaByUser[$candidate->id] = ['count' => $nightCount, 'last_date' => $dateStr];
                } else {
                    $lastNightMetaByUser[$candidate->id] = ['count' => 0, 'last_date' => $prevNightMeta['last_date'] ?? null];
                }

                $assignmentLog[] = [
                    'date' => $dateStr,
                    'slot_index' => $chosenSlotIdx,
                    'user_id' => $candidate->id,
                    'reason' => 'fair_rotation',
                ];
            }

            // Pointer bergeser sebanyak slot valid yang digunakan hari ini
            $rotationPointer += count($slots);
        }

        // Simpan meta distribusi untuk audit
        $roster->meta = array_merge($roster->meta ?? [], [
            'rotation_pointer' => $rotationPointer,
            'weekly_shift_counts' => $weeklyCounts,
            'assignment_log' => $assignmentLog,
        ]);
        $roster->save();

        AuditLogger::record('weekly_roster_created', $roster, null, [
            'location_id' => $roster->location_id,
            'location_shift_id' => $roster->location_shift_id,
            'week_start' => $roster->week_start?->toDateString(),
            'week_end' => $roster->week_end?->toDateString(),
            'entries' => $roster->entries()->count(),
        ]);

        return redirect()->route('shifts.rosters.show', $roster)->with('success', 'Roster minggu dibuat.');
    }

    public function show(WeeklyRoster $roster)
    {
        $auth = auth()->user();
        if ($auth->hasRole('Location Admin') && $auth->location_id != $roster->location_id) {
            abort(403);
        }
        $roster->load(['location', 'locationShift.shift', 'entries.user']);
        $slotMap = [];
        $slots = $roster->locationShift->normalizedSlots();
        foreach ($slots as $idx => $slot) {
            $slotMap[$idx] = $slot;
        }
        return view('shifts.rosters.show', compact('roster', 'slotMap'));
    }

    public function edit(WeeklyRoster $roster)
    {
        $auth = auth()->user();
        if ($auth->hasRole('Location Admin') && $auth->location_id != $roster->location_id) {
            abort(403);
        }
        $roster->load(['location', 'locationShift.shift', 'entries.user']);
        $slotMap = [];
        $slots = $roster->locationShift->normalizedSlots();
        foreach ($slots as $idx => $slot) {
            $slotMap[$idx] = $slot;
        }
        $rosterUsers = $roster->entries->pluck('user')->filter()->unique('id')->sortBy('name')->values();

        return view('shifts.rosters.edit', compact('roster', 'slotMap', 'rosterUsers'));
    }

    public function update(Request $request, WeeklyRoster $roster)
    {
        $auth = auth()->user();
        if ($auth->hasRole('Location Admin') && $auth->location_id != $roster->location_id) {
            abort(403);
        }
        $data = $request->validate([
            'swap_date' => 'required|date',
            'swap_user_a' => 'required|integer|different:swap_user_b',
            'swap_user_b' => 'required|integer',
        ]);

        $entries = $roster->entries()->whereDate('date', $data['swap_date'])->get();
        $entryA = $entries->firstWhere('user_id', $data['swap_user_a']);
        $entryB = $entries->firstWhere('user_id', $data['swap_user_b']);
        if (!$entryA || !$entryB) {
            return back()->withErrors(['swap_user_a' => 'Employee yang dipilih tidak memiliki jadwal pada tanggal tersebut.']);
        }

        $locationShift = $roster->locationShift;
        $slots = $locationShift->normalizedSlots();
        $slotA = $slots[$entryA->slot_index] ?? null;
        $slotB = $slots[$entryB->slot_index] ?? null;
        if (!$slotA || !$slotB) {
            return back()->withErrors(['swap_user_a' => 'Slot shift tidak ditemukan untuk jadwal yang dipilih.']);
        }

        $dateObj = Carbon::parse($data['swap_date']);
        $intervalA = $this->buildSlotIntervalForDate($slotA, $dateObj, $locationShift->location);
        $intervalB = $this->buildSlotIntervalForDate($slotB, $dateObj, $locationShift->location);
        if (!$intervalA || !$intervalB) {
            return back()->withErrors(['swap_user_a' => 'Slot shift tidak berlaku untuk tanggal tersebut.']);
        }

        $userA = User::findOrFail($data['swap_user_a']);
        $userB = User::findOrFail($data['swap_user_b']);
        $ignoreIds = array_filter([$entryA->shift_assignment_id, $entryB->shift_assignment_id]);
        $existingAssignments = ShiftAssignment::with(['locationShift.location', 'shift', 'location'])
            ->whereIn('user_id', [$userA->id, $userB->id])
            ->whereBetween('date', [$dateObj->copy()->subDay()->toDateString(), $dateObj->copy()->addDay()->toDateString()])
            ->where('status', '!=', 'cancelled')
            ->get()
            ->groupBy('user_id');

        if ($this->hasTimeConflictForUser($intervalB, $existingAssignments[$userA->id] ?? collect(), $ignoreIds)) {
            return back()->withErrors(['swap_user_a' => 'Jadwal user A bentrok dengan slot baru.']);
        }
        if ($this->hasTimeConflictForUser($intervalA, $existingAssignments[$userB->id] ?? collect(), $ignoreIds)) {
            return back()->withErrors(['swap_user_b' => 'Jadwal user B bentrok dengan slot baru.']);
        }

        // Bypass batas jam kerja untuk swap agar tidak memblokir rolling employee.

        // Tukar user antar entri
        $tmpUser = $entryA->user_id;
        $entryA->user_id = $entryB->user_id;
        $entryB->user_id = $tmpUser;
        $entryA->save();
        $entryB->save();

        // Refresh shift assignments to align with swapped users
        $applyAssignment = function (WeeklyRosterEntry $entry) use ($locationShift) {
            $assignment = ShiftAssignment::updateOrCreate(
                [
                    'user_id' => $entry->user_id,
                    'date' => $entry->date->toDateString(),
                    'location_id' => $locationShift->location_id,
                    'location_shift_id' => $locationShift->id,
                ],
                [
                    'shift_id' => $locationShift->shift_id,
                    'status' => 'scheduled',
                ]
            );
            $entry->shift_assignment_id = $assignment->id;
            $entry->save();
        };

        $applyAssignment($entryA);
        $applyAssignment($entryB);

        AuditLogger::record('weekly_roster_swapped', $roster, [
            'swap_date' => $data['swap_date'],
            'user_a' => $data['swap_user_a'],
            'user_b' => $data['swap_user_b'],
            'slot_a' => $entryA->slot_index,
            'slot_b' => $entryB->slot_index,
        ], [
            'swap_date' => $data['swap_date'],
            'user_a' => $entryA->user_id,
            'user_b' => $entryB->user_id,
            'slot_a' => $entryA->slot_index,
            'slot_b' => $entryB->slot_index,
        ]);

        return back()->with('success', 'Rolling employee berhasil.');
    }

    public function destroy(WeeklyRoster $roster)
    {
        $auth = auth()->user();
        if ($auth->hasRole('Location Admin') && $auth->location_id != $roster->location_id) {
            abort(403);
        }
        $before = [
            'location_id' => $roster->location_id,
            'location_shift_id' => $roster->location_shift_id,
            'week_start' => $roster->week_start?->toDateString(),
            'week_end' => $roster->week_end?->toDateString(),
        ];
        $roster->delete();
        AuditLogger::record('weekly_roster_deleted', $roster, $before, null);
        return redirect()->route('shifts.rosters.index')->with('success', 'Roster dihapus.');
    }

    private function slotCrossesMidnight(array $slot): bool
    {
        $start = Arr::get($slot, 'start');
        $end = Arr::get($slot, 'end');
        if (!$start || !$end) {
            return false;
        }
        return $end < $start;
    }

    private function isNightToMorningTransition(?array $prevSlot, array $currentSlot): bool
    {
        if (!$prevSlot) {
            return false;
        }
        $prevNight = $this->slotCrossesMidnight($prevSlot) || (Arr::get($prevSlot, 'start') >= '21:00');
        if (!$prevNight) {
            return false;
        }
        $currentStart = Arr::get($currentSlot, 'start');
        if (!$currentStart) {
            return false;
        }
        // Lindungi transisi malam ke shift pagi (<= 08:00)
        return $currentStart <= '08:00';
    }

    private function isNightSlot(array $slot): bool
    {
        $start = Arr::get($slot, 'start');
        if (!$start) {
            return false;
        }
        return $this->slotCrossesMidnight($slot) || $start >= '22:00';
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

    private function buildSlotIntervalForDate(array $slot, Carbon $date, ?Location $location): ?array
    {
        if (!empty($slot['days'])) {
            $dayKey = strtolower($date->format('l'));
            $dayList = array_map('strtolower', $slot['days']);
            if (!in_array($dayKey, $dayList, true)) {
                return null;
            }
        }
        $tz = $location?->timezone ?? config('app.timezone', 'UTC');
        $start = Carbon::parse($date->toDateString() . ' ' . $slot['start'], $tz);
        $end = Carbon::parse($date->toDateString() . ' ' . $slot['end'], $tz);
        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }
        return [$start, $end];
    }

    private function intervalHours(array $interval): float
    {
        return $interval[0]->diffInMinutes($interval[1]) / 60;
    }

    private function assignmentIntervals(ShiftAssignment $assignment): array
    {
        $location = $assignment->locationShift?->location ?? $assignment->location;
        $tz = $location?->timezone ?? config('app.timezone', 'UTC');
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
            return [];
        }
        return $pivot->slotIntervalsForDate($dateObj);
    }

    private function hasTimeConflictForUser(array $proposedInterval, $assignments, array $ignoreIds = []): bool
    {
        $ignoreIds = array_filter($ignoreIds);
        foreach ($assignments as $assignment) {
            if (!empty($ignoreIds) && in_array($assignment->id, $ignoreIds, true)) {
                continue;
            }
            $intervals = $this->assignmentIntervals($assignment);
            foreach ($intervals as [$start, $end]) {
                if ($proposedInterval[0]->lt($end) && $start->lt($proposedInterval[1])) {
                    return true;
                }
            }
        }
        return false;
    }

    private function calculateAssignmentHoursFromModel(ShiftAssignment $assignment): float
    {
        $intervals = $this->assignmentIntervals($assignment);
        $minutes = 0;
        foreach ($intervals as [$start, $end]) {
            $minutes += $start->diffInMinutes($end);
        }
        return $minutes / 60;
    }

    private function checkWorkHourLimits(User $user, string $date, float $assignmentHours, array $ignoreIds = []): ?string
    {
        $limits = $this->resolveWorkHourLimits($user->location);
        $dateObj = Carbon::parse($date);
        $weekStart = $dateObj->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $dateObj->copy()->endOfWeek(Carbon::SUNDAY);

        $dailyAssignments = ShiftAssignment::with(['locationShift.location', 'shift', 'location'])
            ->where('user_id', $user->id)
            ->whereDate('date', $date)
            ->when(!empty($ignoreIds), fn($q) => $q->whereNotIn('id', $ignoreIds))
            ->where('status', '!=', 'cancelled')
            ->get();

        $weeklyAssignments = ShiftAssignment::with(['locationShift.location', 'shift', 'location'])
            ->where('user_id', $user->id)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->when(!empty($ignoreIds), fn($q) => $q->whereNotIn('id', $ignoreIds))
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

    private function summarizeWorkHours(User $user, string $date): array
    {
        $dateObj = Carbon::parse($date);
        $weekStart = $dateObj->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $dateObj->copy()->endOfWeek(Carbon::SUNDAY);

        $dailyAssignments = ShiftAssignment::with(['locationShift.location', 'shift', 'location'])
            ->where('user_id', $user->id)
            ->whereDate('date', $date)
            ->where('status', '!=', 'cancelled')
            ->get();

        $weeklyAssignments = ShiftAssignment::with(['locationShift.location', 'shift', 'location'])
            ->where('user_id', $user->id)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->where('status', '!=', 'cancelled')
            ->get();

        $dailyHours = $dailyAssignments->sum(fn($a) => $this->calculateAssignmentHoursFromModel($a));
        $weeklyHours = $weeklyAssignments->sum(fn($a) => $this->calculateAssignmentHoursFromModel($a));

        return ['daily' => $dailyHours, 'weekly' => $weeklyHours];
    }

    public function export(WeeklyRoster $roster)
    {
        $auth = auth()->user();
        if ($auth->hasRole('Location Admin') && $auth->location_id != $roster->location_id) {
            abort(403);
        }

        $filename = sprintf('roster_%s_%s.xlsx', $roster->location->code ?? 'loc', $roster->week_start->format('Ymd'));
        return Excel::download(new WeeklyRosterExport($roster), $filename);
    }
}
