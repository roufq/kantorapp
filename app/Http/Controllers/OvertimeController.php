<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Overtime;
use App\Models\OvertimeApproval;
use App\Models\User;

class OvertimeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'master') {
            // Masters can see all overtime requests
            $query = Overtime::with('user', 'approvals.master');
        } else {
            // Employees see only their own requests
            $query = Overtime::where('user_id', $user->id)->with('user', 'approvals.master');
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $overtimes = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('overtime.index', compact('overtimes'));
    }

    public function create()
    {
        $masters = User::where('role', 'master')->get();
        return view('overtime.create', compact('masters'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:1000',
            'selected_masters' => 'required|array|size:2',
            'selected_masters.*' => 'exists:users,id',
        ]);

        // Calculate duration
        $start = strtotime($request->start_time);
        $end = strtotime($request->end_time);
        $duration = ($end - $start) / 3600; // hours

        $overtime = Overtime::create([
            'user_id' => $user->id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_hours' => $duration,
            'reason' => $request->reason,
            'selected_masters' => $request->selected_masters,
        ]);

        // Create approval records for selected masters
        foreach ($request->selected_masters as $masterId) {
            OvertimeApproval::create([
                'overtime_request_id' => $overtime->id,
                'master_id' => $masterId,
            ]);
        }

        return redirect()->route('overtime.index')->with('success', 'Overtime request submitted successfully!');
    }

    public function show(Overtime $overtime)
    {
        $user = Auth::user();

        if ($user->role === 'employee' && $overtime->user_id !== $user->id) {
            abort(403, 'You can only view your own overtime requests');
        }

        $overtime->load('user', 'approvals.master');

        return view('overtime.show', compact('overtime'));
    }

    public function approve(Request $request, Overtime $overtime)
    {
        $user = Auth::user();

        if ($user->role !== 'master') {
            abort(403, 'Only masters can approve overtime requests');
        }

        // Check if this master is selected for this request
        if (!in_array($user->id, $overtime->selected_masters)) {
            abort(403, 'You are not authorized to approve this request');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:500',
        ]);

        $approval = OvertimeApproval::where('overtime_request_id', $overtime->id)
            ->where('master_id', $user->id)
            ->first();

        if (!$approval) {
            abort(404, 'Approval record not found');
        }

        $approval->update([
            'status' => $request->status,
            'approved_at' => now(),
            'notes' => $request->notes,
        ]);

        // Update overall status
        $overtime->updateOverallStatus();

        $message = $request->status === 'approved' ? 'Overtime request approved!' : 'Overtime request rejected!';
        return redirect()->route('overtime.show', $overtime)->with('success', $message);
    }
}
