<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Location::query();

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

        $locations = $query->orderBy('name')->paginate(10);

        return view('locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('locations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:locations,code',
            'address' => 'nullable|string',
            'timezone' => 'nullable|string|timezone',
            'brand_name' => 'nullable|string|max:255',
            'brand_logo_url' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'custom_css_url' => 'nullable|string|max:255',
            'custom_js_url' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:10000',
            'is_active' => 'boolean',
            'shift_enabled' => 'boolean',
            // Make schedule fields optional to match current schema usage
            'schedule_type' => 'nullable|in:daily,shifts',
            'daily_schedule' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Location::create([
            'name' => $request->name,
            'brand_name' => $request->brand_name,
            'brand_logo_url' => $request->brand_logo_url,
            'code' => strtoupper($request->code),
            'address' => $request->address,
            'timezone' => $request->timezone ?? 'Asia/Jakarta',
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius ?? 50,
            'custom_css_url' => $request->custom_css_url,
            'custom_js_url' => $request->custom_js_url,
            'is_active' => $request->has('is_active'),
            'shift_enabled' => $request->has('shift_enabled'),
            'schedule_type' => $request->schedule_type,
            'daily_schedule' => $request->daily_schedule ? json_decode($request->daily_schedule, true) : null,
            'settings' => [],
        ]);

        return redirect()->route('locations.index')->with('success', 'Location created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        $location->load(['users', 'attendances' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return view('locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    /**
     * Show the settings form for a location.
     */
    public function settings(Location $location)
    {
        $user = Auth::user();
        if ($user->hasRole('Admin Lokasi') && (int) $user->location_id !== (int) $location->id) {
            abort(403, 'Unauthorized');
        }

        $location->load('locationSettings');
        return view('locations.settings', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:locations,code,' . $location->id,
            'address' => 'nullable|string',
            'timezone' => 'nullable|string|timezone',
            'brand_name' => 'nullable|string|max:255',
            'brand_logo_url' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'custom_css_url' => 'nullable|string|max:255',
            'custom_js_url' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:10000',
            'is_active' => 'boolean',
            'shift_enabled' => 'boolean',
            // Make schedule fields optional to match current schema usage
            'schedule_type' => 'nullable|in:daily,shifts',
            'daily_schedule' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $location->update([
            'name' => $request->name,
            'brand_name' => $request->brand_name,
            'brand_logo_url' => $request->brand_logo_url,
            'code' => strtoupper($request->code),
            'address' => $request->address,
            'timezone' => $request->timezone ?? 'Asia/Jakarta',
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius ?? 50,
            'custom_css_url' => $request->custom_css_url,
            'custom_js_url' => $request->custom_js_url,
            'is_active' => $request->has('is_active'),
            'shift_enabled' => $request->has('shift_enabled'),
            'schedule_type' => $request->schedule_type,
            'daily_schedule' => $request->daily_schedule ? json_decode($request->daily_schedule, true) : null,
        ]);

        return redirect()->route('locations.index')->with('success', 'Location updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        // Check if location has users or attendances
        if ($location->users()->count() > 0 || $location->attendances()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete location with associated users or attendances.');
        }

        $location->delete();

        return redirect()->route('locations.index')->with('success', 'Location deleted successfully!');
    }

    /**
     * Update location settings
     */
    public function updateSettings(Request $request, Location $location)
    {
        $user = Auth::user();
        if ($user->hasRole('Admin Lokasi') && (int) $user->location_id !== (int) $location->id) {
            abort(403, 'Unauthorized');
        }
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required',
            'settings.*.type' => 'required|in:string,number,boolean,json',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        foreach ($request->settings as $setting) {
            $location->setSetting($setting['key'], $setting['value'], $setting['type']);
        }

        return response()->json(['message' => 'Settings updated successfully']);
    }
}
