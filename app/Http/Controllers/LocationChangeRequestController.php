<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\LocationChangeRequest;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationChangeRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LocationChangeRequest::with(['user', 'originalLocation', 'targetLocation']);

        // Scope baseline by role
        if ($user->hasRole('Location Admin')) {
            $query->where('original_location_id', $user->location_id);
        } elseif ($user->hasRole('Employee')) {
            $query->where('user_id', $user->id);
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('request_date')) {
            $query->whereDate('request_date', $request->request_date);
        }
        if ($request->filled('original_location_id')) {
            $query->where('original_location_id', (int) $request->original_location_id);
        }
        if ($request->filled('target_location_id')) {
            $query->where('target_location_id', (int) $request->target_location_id);
        }

        $requests = $query->orderBy('request_date', 'desc')->paginate(15)->appends($request->query());

        $locations = \App\Models\Location::orderBy('name')->get();
        return view('location_change_requests.index', compact('requests', 'locations'));
    }

    public function create()
    {
        $auth = Auth::user();
        $locations = Location::where('is_active', true)
            ->when($auth->location_id, fn($q) => $q->where('id', '!=', $auth->location_id))
            ->orderBy('name')
            ->get();

        // Location Admin / Super Admin can create on behalf of a user
        $users = null;
        if ($auth->hasRole('Super Admin')) {
            $users = \App\Models\User::orderBy('name')->get();
        } elseif ($auth->hasRole('Location Admin')) {
            $users = \App\Models\User::where('location_id', $auth->location_id)->orderBy('name')->get();
        }

        return view('location_change_requests.create', compact('locations', 'users'));
    }

    public function store(Request $request)
    {
        $auth = Auth::user();

        $rules = [
            'target_location_id' => 'required|exists:locations,id',
            'reason' => 'required|string|max:255',
            'is_permanent' => 'nullable|boolean',
            'request_date' => 'required|date',
        ];
        // Admin/Super can choose user
        if ($auth->hasRole('Super Admin') || $auth->hasRole('Location Admin')) {
            $rules['user_id'] = 'required|exists:users,id';
        }
        $validated = $request->validate($rules);

        // Determine requester and original location
        $targetUser = $auth;
        if (isset($validated['user_id'])) {
            $targetUser = \App\Models\User::findOrFail($validated['user_id']);
            if ($auth->hasRole('Location Admin') && (int)$targetUser->location_id !== (int)$auth->location_id) {
                abort(403, 'You can only request for users in your location');
            }
        }

        $autoApprove = ($auth->hasRole('Super Admin') || $auth->hasRole('Location Admin')) && $request->boolean('approve_now');

        $requestData = [
            'user_id' => $targetUser->id,
            'original_location_id' => $targetUser->location_id,
            'target_location_id' => $validated['target_location_id'],
            'reason' => $validated['reason'],
            'request_date' => $validated['request_date'],
            'status' => $autoApprove ? 'approved' : 'pending',
            'is_permanent' => (bool)($validated['is_permanent'] ?? false),
        ];
        if ($autoApprove) {
            $requestData['approved_by'] = $auth->id;
        }

        $lcr = LocationChangeRequest::create($requestData);

        // If auto-approved and permanent, immediately update user's location
        if ($autoApprove && $lcr->is_permanent) {
            $targetUser->update(['location_id' => $lcr->target_location_id]);
        }

        if ($autoApprove) {
            AuditLogger::record('location_change_auto_approved', $lcr, null, [
                'status' => $lcr->status,
                'user_id' => $lcr->user_id,
                'original_location_id' => $lcr->original_location_id,
                'target_location_id' => $lcr->target_location_id,
                'is_permanent' => $lcr->is_permanent,
            ]);
        }

        return redirect()->route('location-change-requests.index')->with('success', 'Request submitted successfully.');
    }

    public function updateStatus(Request $request, LocationChangeRequest $locationChangeRequest)
    {
        $this->authorize('update', $locationChangeRequest);

        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $before = $locationChangeRequest->only(['status', 'approved_by']);
        $locationChangeRequest->update([
            'status' => $request->status,
            'approved_by' => Auth::id(),
        ]);

        // Immediate transfer on approval only if permanent
        if ($request->status === 'approved' && $locationChangeRequest->is_permanent) {
            $locationChangeRequest->user->update([
                'location_id' => $locationChangeRequest->target_location_id,
            ]);
        }

        AuditLogger::record('location_change_status_updated', $locationChangeRequest, $before, [
            'status' => $locationChangeRequest->status,
            'approved_by' => $locationChangeRequest->approved_by,
        ], [
            'user_id' => $locationChangeRequest->user_id,
            'original_location_id' => $locationChangeRequest->original_location_id,
            'target_location_id' => $locationChangeRequest->target_location_id,
            'is_permanent' => $locationChangeRequest->is_permanent,
        ]);

        return back()->with('success', 'Request status updated successfully.');
    }
}
