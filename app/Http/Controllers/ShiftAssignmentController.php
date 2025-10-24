<?php

namespace App\Http\Controllers;

use App\Models\ShiftAssignment;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $auth = Auth::user();

        $query = ShiftAssignment::with(['user', 'shift']);
        if ($auth->hasRole('Admin Lokasi')) {
            $query->forLocation($auth->location_id);
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

        return view('shift-assignments.index', compact('assignments'));
    }

    public function create()
    {
        $auth = Auth::user();
        $users = User::when($auth->hasRole('Admin Lokasi'), function ($q) use ($auth) {
            $q->where('location_id', $auth->location_id);
        })->orderBy('name')->get();

        $shifts = Shift::active()
            ->when($auth->hasRole('Admin Lokasi'), function ($q) use ($auth) {
                $q->forLocation($auth->location_id);
            })->orderBy('name')->get();

        return view('shift-assignments.create', compact('users', 'shifts'));
    }

    public function store(Request $request)
    {
        $auth = Auth::user();
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'date' => 'required|date|after_or_equal:today',
            'status' => 'nullable|in:scheduled,cancelled,completed',
            'notes' => 'nullable|string|max:255',
            'handover_required' => 'nullable|boolean',
            'handover_note' => 'nullable|string',
        ]);

        if ($auth->hasRole('Admin Lokasi')) {
            $userOk = User::where('id', $request->user_id)->where('location_id', $auth->location_id)->exists();
            $shiftOk = Shift::forLocation($auth->location_id)->where('id', $request->shift_id)->exists();
            if (!$userOk || !$shiftOk) {
                abort(403, 'User/Shift must be within your location');
            }
        }

        // prevent duplicate/overlap per user/date (basic + overnight)
        if ($this->hasOverlap($request->user_id, $request->shift_id, $request->date)) {
            return back()->withErrors(['date' => 'User has overlapping assignment around this date'])->withInput();
        }

        ShiftAssignment::create([
            'user_id' => $request->user_id,
            'shift_id' => $request->shift_id,
            'date' => $request->date,
            'status' => $request->status ?: 'scheduled',
            'notes' => $request->notes,
            'handover_required' => (bool) $request->handover_required,
            'handover_note' => $request->handover_note,
        ]);

        return redirect()->route('shift-assignments.index')->with('success', 'Shift assignment created');
    }

    public function edit(ShiftAssignment $shift_assignment)
    {
        $auth = Auth::user();
        if ($auth->hasRole('Admin Lokasi')) {
            if (optional($shift_assignment->user)->location_id !== $auth->location_id) {
                abort(403);
            }
        }
        $users = User::when($auth->hasRole('Admin Lokasi'), function ($q) use ($auth) {
            $q->where('location_id', $auth->location_id);
        })->orderBy('name')->get();
        $shifts = Shift::active()->when($auth->hasRole('Admin Lokasi'), function ($q) use ($auth) {
            $q->forLocation($auth->location_id);
        })->orderBy('name')->get();

        return view('shift-assignments.edit', ['assignment' => $shift_assignment, 'users' => $users, 'shifts' => $shifts]);
    }

    public function update(Request $request, ShiftAssignment $shift_assignment)
    {
        $auth = Auth::user();
        if ($auth->hasRole('Admin Lokasi')) {
            if (optional($shift_assignment->user)->location_id !== $auth->location_id) {
                abort(403);
            }
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'date' => 'required|date|after_or_equal:today',
            'status' => 'nullable|in:scheduled,cancelled,completed',
            'notes' => 'nullable|string|max:255',
            'handover_required' => 'nullable|boolean',
            'handover_note' => 'nullable|string',
        ]);

        if ($auth->hasRole('Admin Lokasi')) {
            $userOk = User::where('id', $request->user_id)->where('location_id', $auth->location_id)->exists();
            $shiftOk = Shift::forLocation($auth->location_id)->where('id', $request->shift_id)->exists();
            if (!$userOk || !$shiftOk) {
                abort(403);
            }
        }

        if ($this->hasOverlap($request->user_id, $request->shift_id, $request->date, $shift_assignment->id)) {
            return back()->withErrors(['date' => 'User has overlapping assignment around this date'])->withInput();
        }

        $shift_assignment->update([
            'user_id' => $request->user_id,
            'shift_id' => $request->shift_id,
            'date' => $request->date,
            'status' => $request->status ?: 'scheduled',
            'notes' => $request->notes,
            'handover_required' => (bool) $request->handover_required,
            'handover_note' => $request->handover_note,
        ]);

        return redirect()->route('shift-assignments.index')->with('success', 'Shift assignment updated');
    }

    private function hasOverlap(int $userId, int $shiftId, string $date, ?int $ignoreId = null): bool
    {
        // Prepare interval for proposed assignment based on shift time_slots
        $shift = \App\Models\Shift::find($shiftId);
        if (!$shift) { return false; }
        $slots = $shift->isMultipleShift() ? $shift->time_slots : [ $shift->time_slots ];

        $proposedIntervals = [];
        foreach ($slots as $slot) {
            if (!isset($slot['start']) || !isset($slot['end'])) { continue; }
            $start = \Carbon\Carbon::parse($date.' '.$slot['start'], 'Asia/Jakarta');
            $end = \Carbon\Carbon::parse($date.' '.$slot['end'], 'Asia/Jakarta');
            if ($end->lessThan($start)) { $end->addDay(); }
            $proposedIntervals[] = [$start, $end];
        }

        // Check against existing assignments on same date and adjacent day
        $existing = ShiftAssignment::where('user_id', $userId)
            ->whereBetween('date', [\Carbon\Carbon::parse($date)->subDay()->toDateString(), \Carbon\Carbon::parse($date)->addDay()->toDateString()])
            ->when($ignoreId, function ($q) use ($ignoreId) { $q->where('id', '!=', $ignoreId); })
            ->with('shift')
            ->get();

        foreach ($existing as $a) {
            $slots2 = $a->shift && $a->shift->isMultipleShift() ? $a->shift->time_slots : [ optional($a->shift)->time_slots ];
            foreach ($slots2 as $slot) {
                if (!isset($slot['start']) || !isset($slot['end'])) { continue; }
                $start2 = \Carbon\Carbon::parse($a->date.' '.$slot['start'], 'Asia/Jakarta');
                $end2 = \Carbon\Carbon::parse($a->date.' '.$slot['end'], 'Asia/Jakarta');
                if ($end2->lessThan($start2)) { $end2->addDay(); }
                foreach ($proposedIntervals as [$s1,$e1]) {
                    // overlap if s1 < e2 && s2 < e1
                    if ($s1->lt($end2) && $start2->lt($e1)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function destroy(ShiftAssignment $shift_assignment)
    {
        $auth = Auth::user();
        if ($auth->hasRole('Admin Lokasi')) {
            if (optional($shift_assignment->user)->location_id !== $auth->location_id) {
                abort(403);
            }
        }
        $shift_assignment->delete();
        return redirect()->route('shift-assignments.index')->with('success', 'Shift assignment deleted');
    }

    public function export(Request $request)
    {
        $auth = Auth::user();
        $query = ShiftAssignment::with(['user', 'shift']);
        if ($auth->hasRole('Admin Lokasi')) {
            $query->forLocation($auth->location_id);
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

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ShiftAssignmentsExport($query), 'shift_assignments.xlsx');
    }
}
