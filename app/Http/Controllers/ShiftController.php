<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Location;
use App\Models\LocationShift;
use App\Models\ShiftAssignment;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class ShiftController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return redirect()->route('shifts.scheduler');
    }

    public function schedulerForm(Request $request)
    {
        $locations = Location::active()->orderBy('name')->get();
        $locationId = $request->location_id ?: $locations->first()->id ?? null;
        $locationShifts = collect();
        $users = collect();
        if ($locationId) {
            $locationShifts = LocationShift::with('shift')
                ->where('location_id', $locationId)
                ->get();
            $users = User::role('Karyawan')->where('location_id', $locationId)->orderBy('name')->get();
        }

        return view('shifts.scheduler', compact('locations', 'locationId', 'locationShifts', 'users'));
    }

    public function schedulerGenerate(Request $request)
    {
        $data = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'location_shift_id' => 'required|exists:location_shifts,id',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'weekly_off_every' => 'nullable|integer|min:0',
            'weekly_off_label' => 'nullable|string|max:50',
        ]);

        $locationShift = LocationShift::with('shift')->findOrFail($data['location_shift_id']);
        if ($locationShift->location_id != $data['location_id']) {
            return back()->withErrors(['location_shift_id' => 'Shift tidak terikat ke lokasi ini.'])->withInput();
        }

        $users = User::whereIn('id', $data['user_ids'])->where('location_id', $data['location_id'])->get();
        if ($users->count() !== count($data['user_ids'])) {
            return back()->withErrors(['user_ids' => 'Terdapat user di luar lokasi ini.'])->withInput();
        }

        $start = \Carbon\Carbon::parse($data['date_start']);
        $end = \Carbon\Carbon::parse($data['date_end']);
        $weeklyOffEvery = $data['weekly_off_every'] ?? 0;
        $weeklyOffLabel = $data['weekly_off_label'] ?? 'OFF';

        $created = 0;
        $skipped = 0;

        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end->copy()->addDay()); // inclusive

        foreach ($users as $user) {
            $dayCounter = 0;
            foreach ($period as $day) {
                $dateStr = $day->format('Y-m-d');
                $dayCounter++;

                // Weekly off logic: if weekly_off_every > 0, skip every nth day and optionally mark OFF
                if ($weeklyOffEvery > 0 && $dayCounter % $weeklyOffEvery === 0) {
                    // insert OFF assignment marker (status = cancelled + note)
                    ShiftAssignment::firstOrCreate([
                        'user_id' => $user->id,
                        'date' => $dateStr,
                    ], [
                        'location_id' => $locationShift->location_id,
                        'shift_id' => $locationShift->shift_id,
                        'location_shift_id' => $locationShift->id,
                        'status' => 'cancelled',
                        'notes' => $weeklyOffLabel,
                    ]);
                    $created++;
                    continue;
                }

                $exists = ShiftAssignment::where('user_id', $user->id)
                    ->whereDate('date', $dateStr)
                    ->where('location_id', $locationShift->location_id)
                    ->exists();
                if ($exists) {
                    $skipped++;
                    continue;
                }

                ShiftAssignment::create([
                    'user_id' => $user->id,
                    'location_id' => $locationShift->location_id,
                    'shift_id' => $locationShift->shift_id,
                    'location_shift_id' => $locationShift->id,
                    'date' => $dateStr,
                    'status' => 'scheduled',
                    'notes' => null,
                ]);
                $created++;
            }
        }

        return redirect()->route('shifts.scheduler', ['location_id' => $data['location_id']])
            ->with('success', "Jadwal dibuat: {$created} entri, dilewati: {$skipped} (sudah ada)");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('shifts.scheduler');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return redirect()->route('shifts.scheduler');
    }

    /**
     * Display the specified resource.
     */
    public function show(Shift $shift)
    {
        return redirect()->route('shifts.scheduler');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shift $shift)
    {
        return redirect()->route('shifts.scheduler');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shift $shift)
    {
        return redirect()->route('shifts.scheduler');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        return redirect()->route('shifts.scheduler');
    }
}
