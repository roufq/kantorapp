<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\LocationShift;
use App\Models\ShiftAssignment;
use App\Models\User;
use App\Models\WeeklyRoster;
use App\Models\WeeklyRosterEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        // Ambil slot dari pivot; fallback ke shift->normalizedSlots()
        $slots = $locationShift->time_slots ?? [];
        if (empty($slots)) {
            $slots = $locationShift->shift?->normalizedSlots() ?? [];
        }
        if (isset($slots['start']) && isset($slots['end'])) {
            $slots = [ $slots ];
        }
        if (!is_array($slots) || empty($slots)) {
            return back()->withErrors(['location_shift_id' => 'Shift lokasi ini belum memiliki slot waktu.'])->withInput();
        }

        $userIndex = 0;
        $userCount = $users->count();

        $period = new \DatePeriod($weekStart, new \DateInterval('P1D'), $weekEnd->copy()->addDay());
        foreach ($period as $day) {
            $dateStr = $day->format('Y-m-d');
            $dayNumber = Carbon::parse($day)->diffInDays($weekStart) + 1;

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
            foreach ($slots as $slotIdx => $slot) {
                $user = $workingUsers[$userIndex % $workCount];
                $userIndex++;

                // Buat / ambil shift assignment aktual
                $assignment = ShiftAssignment::updateOrCreate(
                    [
                        'user_id' => $user->id,
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

                WeeklyRosterEntry::create([
                    'weekly_roster_id' => $roster->id,
                    'shift_assignment_id' => $assignment->id,
                    'user_id' => $user->id,
                    'date' => $dateStr,
                    'slot_index' => $slotIdx,
                    'status' => 'scheduled',
                    'notes' => null,
                ]);
            }
        }

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
        $slots = $roster->locationShift->time_slots ?? [];
        if (empty($slots)) {
            $slots = $roster->locationShift->shift?->normalizedSlots() ?? [];
        }
        if (isset($slots['start']) && isset($slots['end'])) {
            $slots = [ $slots ];
        }
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
        $slots = $roster->locationShift->time_slots ?? [];
        if (empty($slots)) {
            $slots = $roster->locationShift->shift?->normalizedSlots() ?? [];
        }
        if (isset($slots['start']) && isset($slots['end'])) {
            $slots = [ $slots ];
        }
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
}
