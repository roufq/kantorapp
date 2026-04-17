<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Shift;
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
        // Provide shift lists for dropdown (global active)
        $allActiveShiftsSingle = Shift::query()->active()->where('shift_type', 'single')->orderBy('name')->get();
        $allActiveShiftsMultiple = Shift::query()->active()->where('shift_type', 'multiple')->orderBy('name')->get();

        return view('locations.create', compact('allActiveShiftsSingle', 'allActiveShiftsMultiple'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
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
            'default_shift_id' => 'nullable|exists:shifts,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // If super admin sets this as default, unset default on others first
        $isDefault = $user->hasRole('Super Admin') && $request->has('is_default');
        if ($isDefault) {
            Location::where('is_default', true)->update(['is_default' => false]);
        }

        $newLocation = Location::create([
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
            'is_default' => $isDefault,
            'settings' => [],
        ]);

        // Persist default shift selection (as a setting) if provided
        if ($request->filled('default_shift_id')) {
            $newLocation->setSetting('default_shift_id', (int) $request->input('default_shift_id'), 'number');
        }

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
        // Load assigned and global shifts for dropdowns
        $assignedShiftsSingle = $location->shifts()->active()->where('shift_type', 'single')->orderBy('name')->get();
        $assignedShiftsMultiple = $location->shifts()->active()->where('shift_type', 'multiple')->orderBy('name')->get();
        $allActiveShiftsSingle = Shift::query()->active()->where('shift_type', 'single')->orderBy('name')->get();
        $allActiveShiftsMultiple = Shift::query()->active()->where('shift_type', 'multiple')->orderBy('name')->get();

        $defaultShiftId = $location->getSetting('default_shift_id');

        return view('locations.edit', compact(
            'location',
            'assignedShiftsSingle',
            'assignedShiftsMultiple',
            'allActiveShiftsSingle',
            'allActiveShiftsMultiple',
            'defaultShiftId'
        ));
    }

    /**
     * Show the settings form for a location.
     */
    public function settings(Location $location)
    {
        $user = Auth::user();
        if ($user->hasRole('Location Admin') && (int) $user->location_id !== (int) $location->id) {
            abort(403, 'Unauthorized');
        }

        $location->load('locationSettings');
        $notificationSettings = [
            'notify_pending_approvals_time' => $location->getSetting('notify_pending_approvals_time', '00:00'),
            'notify_shift_h1_time' => $location->getSetting('notify_shift_h1_time', '00:00'),
            'notify_leave_monthly_summary_time' => $location->getSetting('notify_leave_monthly_summary_time', '00:00'),
            'notify_leave_monthly_reminder_time' => $location->getSetting('notify_leave_monthly_reminder_time', '00:00'),
        ];

        return view('locations.settings', compact('location', 'notificationSettings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $user = Auth::user();
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
            'default_shift_id' => 'nullable|exists:shifts,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle default location switch by Super Admin only
        $updateData = [
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
        ];

        if ($user->hasRole('Super Admin')) {
            $isDefault = $request->has('is_default');
            if ($isDefault) {
                Location::where('is_default', true)->where('id', '!=', $location->id)->update(['is_default' => false]);
            }
            $updateData['is_default'] = $isDefault;
        }

        $location->update($updateData);

        // Persist default shift selection (as a setting) if provided
        if ($request->filled('default_shift_id')) {
            $location->setSetting('default_shift_id', (int) $request->input('default_shift_id'), 'number');
        } else if (!$request->has('shift_enabled')) {
            // If shift not enabled and no selection, optionally clear setting
            // $location->setSetting('default_shift_id', null, 'number');
        }

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
        if ($user->hasRole('Location Admin') && (int) $user->location_id !== (int) $location->id) {
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

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Settings updated successfully']);
        }

        return redirect()->route('locations.settings', $location)->with('success', 'Settings updated successfully');
    }
}
