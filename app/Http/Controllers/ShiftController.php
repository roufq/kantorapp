<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Location;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Shift::with('locations');

        // Search by name or code
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        // Filter by active status
        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $shifts = $query->orderBy('created_at', 'desc')->paginate(15);
        $locations = Location::active()->orderBy('name')->get();

        return view('shifts.index', compact('shifts', 'locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('shifts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:shifts,code',
            'day' => 'nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'shift_type' => 'required|in:single,multiple',
            'time_slots' => 'required|array',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'locations' => 'nullable|array',
            'locations.*' => 'exists:locations,id',
        ]);

        // Additional validation based on shift type
        $validator->after(function ($validator) use ($request) {
            if ($request->shift_type === 'single') {
                // Single shift: must have start and end
                if (!isset($request->time_slots['start']) || !isset($request->time_slots['end'])) {
                    $validator->errors()->add('time_slots', 'Single shift must have start and end time.');
                }
            } elseif ($request->shift_type === 'multiple') {
                // Multiple shifts: must be array of time slots
                if (!is_array($request->time_slots) || count($request->time_slots) === 0) {
                    $validator->errors()->add('time_slots', 'Multiple shift must have at least one time slot.');
                }

                foreach ($request->time_slots as $index => $slot) {
                    if (!isset($slot['start']) || !isset($slot['end'])) {
                        $validator->errors()->add("time_slots.{$index}", "Time slot {$index} must have start and end time.");
                    }
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $shift = Shift::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'day' => $request->day,
            'shift_type' => $request->shift_type,
            'time_slots' => $request->time_slots,
            'is_active' => $request->has('is_active'),
            'description' => $request->description,
        ]);

        // Attach locations if provided
        if ($request->has('locations') && is_array($request->locations)) {
            $shift->locations()->attach($request->locations);
        }

        return redirect()->route('shifts.index')->with('success', 'Shift created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Shift $shift)
    {
        $shift->load(['locations', 'attendances' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return view('shifts.show', compact('shift'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shift $shift)
    {
        return view('shifts.edit', compact('shift'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shift $shift)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:shifts,code,' . $shift->id,
            'day' => 'nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'shift_type' => 'required|in:single,multiple',
            'time_slots' => 'required|array',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'locations' => 'nullable|array',
            'locations.*' => 'exists:locations,id',
        ]);

        // Additional validation based on shift type
        $validator->after(function ($validator) use ($request) {
            if ($request->shift_type === 'single') {
                // Single shift: must have start and end
                if (!isset($request->time_slots['start']) || !isset($request->time_slots['end'])) {
                    $validator->errors()->add('time_slots', 'Single shift must have start and end time.');
                }
            } elseif ($request->shift_type === 'multiple') {
                // Multiple shifts: must be array of time slots
                if (!is_array($request->time_slots) || count($request->time_slots) === 0) {
                    $validator->errors()->add('time_slots', 'Multiple shift must have at least one time slot.');
                }

                foreach ($request->time_slots as $index => $slot) {
                    if (!isset($slot['start']) || !isset($slot['end'])) {
                        $validator->errors()->add("time_slots.{$index}", "Time slot {$index} must have start and end time.");
                    }
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $shift->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'day' => $request->day,
            'shift_type' => $request->shift_type,
            'time_slots' => $request->time_slots,
            'is_active' => $request->has('is_active'),
            'description' => $request->description,
        ]);

        // Sync locations
        if ($request->has('locations') && is_array($request->locations)) {
            $shift->locations()->sync($request->locations);
        } else {
            $shift->locations()->detach();
        }

        return redirect()->route('shifts.index')->with('success', 'Shift updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        // Check if shift has attendances
        if ($shift->attendances()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete shift with associated attendances.');
        }

        $shift->delete();

        return redirect()->route('shifts.index')->with('success', 'Shift deleted successfully!');
    }
}
