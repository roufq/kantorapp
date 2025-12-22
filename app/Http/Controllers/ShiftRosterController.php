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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;

class ShiftRosterController extends Controller
{
    public function index(Request $request)
    {
        $auth = auth()->user();
        $rostersQuery = WeeklyRoster::with(['location', 'locationShift.shift'])
            ->orderBy('week_start', 'desc');

        if ($auth->hasRole('Admin Lokasi')) {
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

        $rosterEntries = collect();
        if ($locationCategory !== 'office') {
            $rosterEntries = WeeklyRosterEntry::with(['user', 'roster.locationShift.shift'])
                ->whereHas('roster', fn($q) => $q->where('location_id', $locationId))
                ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->get()
                ->groupBy(fn($e) => $e->date->toDateString());
        }

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
                        ? 'Hari libur'
                        : ($slot ? (($slot['start'] ?? '?') . ' - ' . ($slot['end'] ?? '?')) : '-');

                    $calendarEvents->push([
                        'title' => ($entry->user->name ?? '-') . ' - ' . $time,
                        'start' => $entry->date->toDateString(),
                        'allDay' => true,
                        'className' => match ($entry->status) {
                            'off' => 'fc-event-off',
                            'leave' => 'fc-event-leave',
                            'missing' => 'fc-event-missing',
                            default => 'fc-event-on',
                        },
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
                    'title' => 'Belum ada jadwal',
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
        if ($auth->hasRole('Admin Lokasi')) {
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
            $users = User::role('Karyawan')->where('location_id', $locationId)->orderBy('name')->get();
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
        if ($auth->hasRole('Admin Lokasi') && $auth->location_id != $data['location_id']) {
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
                    $chosenSlotIdx = $idx;
                    $slotCursor = $idx + 1;
                    break;
                }

                if ($chosenSlotIdx === null) {
                    continue; // tidak ada slot tersedia
                }

                $slot = $slots[$chosenSlotIdx];

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

        return redirect()->route('shifts.rosters.show', $roster)->with('success', 'Roster minggu dibuat.');
    }

    public function show(WeeklyRoster $roster)
    {
        $auth = auth()->user();
        if ($auth->hasRole('Admin Lokasi') && $auth->location_id != $roster->location_id) {
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
        if ($auth->hasRole('Admin Lokasi') && $auth->location_id != $roster->location_id) {
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
        if ($auth->hasRole('Admin Lokasi') && $auth->location_id != $roster->location_id) {
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
            return back()->withErrors(['swap_user_a' => 'Karyawan yang dipilih tidak memiliki jadwal pada tanggal tersebut.']);
        }

        // Tukar user antar entri
        $tmpUser = $entryA->user_id;
        $entryA->user_id = $entryB->user_id;
        $entryB->user_id = $tmpUser;
        $entryA->save();
        $entryB->save();

        // Refresh shift assignments to align with swapped users
        $locationShift = $roster->locationShift;
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

        return back()->with('success', 'Rolling karyawan berhasil.');
    }

    public function destroy(WeeklyRoster $roster)
    {
        $auth = auth()->user();
        if ($auth->hasRole('Admin Lokasi') && $auth->location_id != $roster->location_id) {
            abort(403);
        }
        $roster->delete();
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

    public function export(WeeklyRoster $roster)
    {
        $auth = auth()->user();
        if ($auth->hasRole('Admin Lokasi') && $auth->location_id != $roster->location_id) {
            abort(403);
        }

        $filename = sprintf('roster_%s_%s.xlsx', $roster->location->code ?? 'loc', $roster->week_start->format('Ymd'));
        return Excel::download(new WeeklyRosterExport($roster), $filename);
    }
}
