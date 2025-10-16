<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\OfficeLocation;
use App\Models\Overtime;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $user = auth()->user();

        // Check if user already checked in today without check out
        $existing = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_time', Carbon::today())
            ->whereNull('check_out_time')
            ->first();

        if ($existing) {
            return back()->withErrors(['message' => 'You have already checked in today.']);
        }

        // Validate location
        if (!$this->isWithinOfficeRadius($request->latitude, $request->longitude)) {
            return back()->withErrors(['message' => 'You must be within 50 meters of an office location to check in.']);
        }

        $now = now();
        $dayOfWeek = $now->dayOfWeek; // 0=Sunday, 1=Monday, ..., 6=Saturday
        $isLate = false;

        // Determine if late based on day
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Monday to Friday
            $isLate = $now->hour > 9 || ($now->hour == 9 && $now->minute > 0);
        } elseif ($dayOfWeek == 6) { // Saturday
            $isLate = $now->hour > 8 || ($now->hour == 8 && $now->minute > 0);
        }
        // Sunday: no late check

        Attendance::create([
            'user_id' => $user->id,
            'check_in_time' => $now,
            'location' => $request->latitude . ',' . $request->longitude,
            'is_late' => $isLate,
        ]);

        $message = $isLate ? 'Checked in successfully (Late).' : 'Checked in successfully.';
        return back()->with('success', $message);
    }

    public function checkOut(Request $request)
    {
        $user = auth()->user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_time', Carbon::today())
            ->whereNull('check_out_time')
            ->first();

        if (!$attendance) {
            return back()->withErrors(['message' => 'No active check-in found for today.']);
        }

        // Validate location
        if (!$this->isWithinOfficeRadius($request->latitude, $request->longitude)) {
            return back()->withErrors(['message' => 'You must be within 50 meters of an office location to check out.']);
        }

        // Check for approved overtime
        $approvedOvertime = Overtime::where('user_id', $user->id)
            ->where('date', Carbon::today())
            ->where('status', 'approved')
            ->first();

        $now = now();
        $canCheckOut = true;

        if ($approvedOvertime) {
            $endTime = Carbon::createFromFormat('H:i:s', $approvedOvertime->end_time);
            if ($now->lt($endTime)) {
                $canCheckOut = false;
            }
        }

        if (!$canCheckOut) {
            return back()->withErrors(['message' => 'You cannot check out before your approved overtime ends.']);
        }

        $dayOfWeek = $now->dayOfWeek; // 0=Sunday, 1=Monday, ..., 6=Saturday
        $requiresApproval = false;

        // Determine if early check-out requires approval
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Monday to Friday
            $requiresApproval = $now->hour < 17 || ($now->hour == 17 && $now->minute < 0); // Before 17:00
        } elseif ($dayOfWeek == 6) { // Saturday
            $requiresApproval = $now->hour < 14 || ($now->hour == 14 && $now->minute < 0); // Before 14:00
        }
        // Sunday: no approval needed

        $attendance->update([
            'check_out_time' => $now,
            'location' => $request->latitude . ',' . $request->longitude,
            'requires_approval' => $requiresApproval,
            'approval_status' => $requiresApproval ? 'pending' : 'approved',
        ]);

        $message = $requiresApproval ? 'Checked out successfully. Early check-out requires master approval.' : 'Checked out successfully.';
        return back()->with('success', $message);
    }

    public function showCheckIn()
    {
        $user = auth()->user();
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_time', Carbon::today())
            ->first();

        return view('attendances.checkin', compact('todayAttendance'));
    }

    public function report(Request $request)
    {
        $query = Attendance::with('user.employee');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('check_in_time', [$request->start_date, $request->end_date]);
        }

        $attendances = $query->orderBy('check_in_time', 'desc')->paginate(20);

        return view('attendances.report', compact('attendances'));
    }

    public function updateApproval(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $request->validate([
            'approval_status' => 'required|in:pending,approved,rejected',
        ]);

        $attendance->update([
            'approval_status' => $request->approval_status,
        ]);

        return back()->with('success', 'Approval status updated successfully.');
    }

    private function isWithinOfficeRadius($latitude, $longitude)
    {
        if (!$latitude || !$longitude) {
            return false;
        }

        $locations = OfficeLocation::all();

        foreach ($locations as $location) {
            $distance = $this->haversineDistance($latitude, $longitude, $location->latitude, $location->longitude);
            if ($distance <= $location->radius) {
                return true;
            }
        }

        return false;
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth radius in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
