<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\LocationChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationChangeRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = LocationChangeRequest::with(['user', 'originalLocation', 'targetLocation']);

        if ($user->hasRole('Admin Lokasi')) {
            $query->where('original_location_id', $user->location_id);
        } elseif ($user->hasRole('Karyawan')) {
            $query->where('user_id', $user->id);
        }

        $requests = $query->orderBy('request_date', 'desc')->get();

        return view('location_change_requests.index', compact('requests'));
    }

    public function create()
    {
        $locations = Location::where('is_active', true)->where('id', '!=', Auth::user()->location_id)->get();
        return view('location_change_requests.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'target_location_id' => 'required|exists:locations,id',
            'reason' => 'required|string|max:255',
        ]);

        LocationChangeRequest::create([
            'user_id' => Auth::id(),
            'original_location_id' => Auth::user()->location_id,
            'target_location_id' => $request->target_location_id,
            'reason' => $request->reason,
            'request_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        return redirect()->route('location-change-requests.index')->with('success', 'Request submitted successfully.');
    }

    public function updateStatus(Request $request, LocationChangeRequest $locationChangeRequest)
    {
        $this->authorize('update', $locationChangeRequest);

        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $locationChangeRequest->update([
            'status' => $request->status,
            'approved_by' => Auth::id(),
        ]);

        // Immediate transfer on approval
        if ($request->status === 'approved') {
            $locationChangeRequest->user->update([
                'location_id' => $locationChangeRequest->target_location_id,
            ]);
        }

        return back()->with('success', 'Request status updated successfully.');
    }
}
