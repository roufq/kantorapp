<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LocationShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Location::with(['shifts' => function ($query) {
            $query->orderBy('name');
        }]);

        // Filter by location
        if ($request->has('location_id') && !empty($request->location_id)) {
            $query->where('id', $request->location_id);
        }

        // Search by location name or code
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        $locations = $query->orderBy('name')->paginate(15);
        $allLocations = Location::active()->orderBy('name')->get();

        return view('location-shifts.index', compact('locations', 'allLocations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $locations = Location::active()->orderBy('name')->get();
        $shifts = Shift::active()->orderBy('name')->get();

        return view('location-shifts.create', compact('locations', 'shifts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_id' => 'required|exists:locations,id',
            'shift_ids' => 'required|array|min:1',
            'shift_ids.*' => 'exists:shifts,id',
            'shift_category' => 'array',
            'shift_category.*' => 'nullable|in:office,non_office',
            'shift_times' => 'array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $location = Location::findOrFail($request->location_id);
        $syncData = $this->buildPivotSyncData($request);
        $location->shifts()->sync($syncData);

        return redirect()->route('location-shifts.index')
            ->with('success', 'Location shifts updated successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        $location->load(['shifts' => function ($query) {
            $query->orderBy('name');
        }]);

        return view('location-shifts.show', compact('location'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        $location->load(['shifts' => function ($query) {
            $query->orderBy('name');
        }]);

        $allShifts = Shift::active()->orderBy('name')->get();
        $assignedShiftIds = $location->shifts->pluck('id')->toArray();
        $pivotCategories = $location->shifts->mapWithKeys(function ($shift) {
            return [$shift->id => $shift->pivot->category ?? $shift->category];
        });
        $pivotSlots = $location->shifts->mapWithKeys(function ($shift) {
            return [$shift->id => $shift->pivot->time_slots ?? []];
        });
        $pivotDefaults = $location->shifts->mapWithKeys(function ($shift) {
            return [$shift->id => (bool) ($shift->pivot->is_default ?? false)];
        });

        return view('location-shifts.edit', compact('location', 'allShifts', 'assignedShiftIds', 'pivotCategories', 'pivotSlots', 'pivotDefaults'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $validator = Validator::make($request->all(), [
            'shift_ids' => 'required|array|min:1',
            'shift_ids.*' => 'exists:shifts,id',
            'shift_category' => 'array',
            'shift_category.*' => 'nullable|in:office,non_office',
            'shift_times' => 'array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $syncData = $this->buildPivotSyncData($request);

        // Sync shifts for this location
        $location->shifts()->sync($syncData);

        return redirect()->route('location-shifts.index')
            ->with('success', 'Location shifts updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        // Remove all shifts from this location
        $location->shifts()->detach();

        return redirect()->route('location-shifts.index')
            ->with('success', 'All shifts removed from location successfully!');
    }

    /**
     * Remove specific shift from location
     */
    public function detachShift(Location $location, Shift $shift)
    {
        $location->shifts()->detach($shift->id);

        return redirect()->back()
            ->with('success', 'Shift removed from location successfully!');
    }

    /**
     * Add specific shift to location
     */
    public function attachShift(Request $request, Location $location)
    {
        $validator = Validator::make($request->all(), [
            'shift_id' => 'required|exists:shifts,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!$location->shifts()->where('shift_id', $request->shift_id)->exists()) {
            $location->shifts()->attach($request->shift_id);
        }

        return response()->json(['message' => 'Shift added to location successfully']);
    }

    /**
     * Build pivot sync payload for location_shifts with category and time slots.
     */
    private function buildPivotSyncData(Request $request): array
    {
        $syncData = [];
        $categoryInput = $request->input('shift_category', []);
        $timesInput = $request->input('shift_times', []);
        $defaultShiftId = $request->input('default_shift_id');

        foreach ($request->shift_ids as $shiftId) {
            /** @var Shift $shift */
            $shift = Shift::find($shiftId);

            // Determine category per shift (fallback to master)
            $category = $categoryInput[$shiftId] ?? ($shift->category ?? 'office');

            // Collect time slots per shift (fallback to master shift slots)
            $slots = $timesInput[$shiftId] ?? [];
            $normalizedSlots = [];
            if (is_array($slots)) {
                foreach ($slots as $slot) {
                    if (!empty($slot['start']) && !empty($slot['end'])) {
                        $days = [];
                        if (isset($slot['days']) && is_array($slot['days'])) {
                            $days = array_values(array_filter($slot['days'], fn($d) => !empty($d)));
                        } elseif (!empty($slot['day'])) {
                            $days = [$slot['day']];
                        }
                        $normalizedSlots[] = [
                            'start' => $slot['start'],
                            'end' => $slot['end'],
                            'days' => $days,
                        ];
                    }
                }
            }
            if (empty($normalizedSlots)) {
                $rawSlots = $shift->time_slots ?? [];
                // Normalize master slots to ensure array of slots
                if (is_array($rawSlots) && isset($rawSlots['start'])) {
                    $rawSlots = [ $rawSlots ];
                }
                if (is_array($rawSlots)) {
                    foreach ($rawSlots as $slot) {
                        if (!empty($slot['start']) && !empty($slot['end'])) {
                            $days = [];
                            if (isset($slot['days']) && is_array($slot['days'])) {
                                $days = array_values(array_filter($slot['days'], fn($d) => !empty($d)));
                            } elseif (!empty($slot['day'])) {
                                $days = [$slot['day']];
                            }
                            $normalizedSlots[] = [
                                'start' => $slot['start'],
                                'end' => $slot['end'],
                                'days' => $days,
                            ];
                        }
                    }
                }
            }

            $syncData[$shiftId] = [
                'category' => $category,
                'time_slots' => $normalizedSlots,
                'is_default' => ($category === 'office' && (string) $defaultShiftId === (string) $shiftId),
            ];
        }

        return $syncData;
    }
}
