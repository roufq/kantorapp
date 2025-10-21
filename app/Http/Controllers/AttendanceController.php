<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\Shift;
use App\Models\Overtime;
use App\Models\LocationChangeRequest;
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

        // Validate user has an assigned location
        if (!$user->location) {
            return back()->withErrors(['message' => 'You are not assigned to any location.']);
        }

        // Validate location radius compliance
        if (!$this->isWithinOfficeRadius($request->latitude, $request->longitude, $user->location)) {
            $radius = $user->location && $user->location->radius ? $user->location->radius : 0;
            return back()->withErrors(['message' => 'You must be within ' . $radius . ' meters of your assigned office location to check in.']);
        }

        $now = now();
        $dayOfWeek = $now->dayOfWeek; // 0=Sunday, 1=Monday, ..., 6=Saturday
        $isLate = false;
        $shiftId = null;

        // Find active shift for user's location at check-in time
        if ($user->location_id) {
            $location = Location::find($user->location_id);
            if ($location && $location->shift_enabled) {
                $activeShift = $location->shifts()
                    ->where('is_active', true)
                    ->where(function ($query) use ($now) {
                        $currentTime = $now->format('H:i');
                        $query->where(function ($q) use ($currentTime) {
                            // For single shifts
                            $q->where('shift_type', 'single')
                              ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(time_slots, '$.start')) <= ?", [$currentTime])
                              ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(time_slots, '$.end')) >= ?", [$currentTime]);
                        })->orWhere(function ($q) use ($currentTime) {
                            // For multiple shifts - check if any slot is active
                            $q->where('shift_type', 'multiple')
                              ->whereRaw("EXISTS (
                                  SELECT 1 FROM JSON_TABLE(time_slots, '$[*]' COLUMNS (
                                      start_time VARCHAR(5) PATH '$.start',
                                      end_time VARCHAR(5) PATH '$.end'
                                  )) slots
                                  WHERE slots.start_time <= ? AND slots.end_time >= ?
                              )", [$currentTime, $currentTime]);
                        });
                    })
                    ->first();

                if ($activeShift) {
                    // Use shift's start time for late calculation
                    if ($activeShift->isSingleShift()) {
                        $shiftStartTime = Carbon::createFromFormat('H:i', $activeShift->time_slots['start']);
                        $isLate = $now->gt($shiftStartTime);
                    } else {
                        // For multiple shifts, check if current time is after any start time
                        $isLate = false;
                        foreach ($activeShift->time_slots as $slot) {
                            $slotStart = Carbon::createFromFormat('H:i', $slot['start']);
                            if ($now->gt($slotStart)) {
                                $isLate = true;
                                break;
                            }
                        }
                    }
                    $shiftId = $activeShift->id;
                }
            }
        }

        // Fallback to day-based rules if no active shift found
        if (!$shiftId) {
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Monday to Friday
                $isLate = $now->hour > 9 || ($now->hour == 9 && $now->minute > 0);
            } elseif ($dayOfWeek == 6) { // Saturday
                $isLate = $now->hour > 8 || ($now->hour == 8 && $now->minute > 0);
            }
            // Sunday: no late check
        }

        Attendance::create([
            'user_id' => $user->id,
            'location_id' => $user->location_id,
            'shift_id' => $shiftId,
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
        $isValidLocation = $this->isWithinOfficeRadius($request->latitude, $request->longitude, $user->location);

        if (!$isValidLocation) {
            $approvedChangeRequest = LocationChangeRequest::where('user_id', $user->id)
                ->where('request_date', Carbon::today())
                ->where('status', 'approved')
                ->first();

            if ($approvedChangeRequest) {
                $isValidLocation = $this->isWithinOfficeRadius($request->latitude, $request->longitude, $approvedChangeRequest->targetLocation);
            }

            if (!$isValidLocation) {
                return back()->withErrors(['message' => 'You must be within the radius of your assigned or approved location to check out.']);
            }
        }


        $now = now();

        // Check for approved overtime
        $approvedOvertime = Overtime::where('user_id', $user->id)
            ->where('date', Carbon::today())
            ->where('status', 'approved')
            ->first();

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

        $message = $requiresApproval ? 'Checked out successfully. Early check-out requires admin approval.' : 'Checked out successfully.';
        return back()->with('success', $message);
    }

    public function showCheckIn()
    {
        $this->authorize('viewAny', Attendance::class);

        $user = auth()->user();
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_time', Carbon::today())
            ->first();

        return view('attendances.checkin', compact('todayAttendance'));
    }

    public function report(Request $request)
    {
        $this->authorize('viewAny', Attendance::class);

        $user = auth()->user();
        $query = Attendance::with('user.employee');

        // Filter by role
        if ($user->hasRole('Admin Lokasi')) {
            $query->where('location_id', $user->location_id);
        } elseif ($user->hasRole('Karyawan')) {
            $query->where('user_id', $user->id);
        }

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
        $this->authorize('update', $attendance);

        $request->validate([
            'approval_status' => 'required|in:pending,approved,rejected',
        ]);

        $attendance->update([
            'approval_status' => $request->approval_status,
        ]);

        return back()->with('success', 'Approval status updated successfully.');
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', Attendance::class);

        $user = auth()->user();
        $query = Attendance::with('user.employee');

        // Filter by role
        if ($user->hasRole('Admin Lokasi')) {
            $query->where('location_id', $user->location_id);
        } elseif ($user->hasRole('Karyawan')) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('check_in_time', [$request->start_date, $request->end_date]);
        }

        $query->orderBy('check_in_time', 'desc');

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AttendanceExport($query), 'attendance_report.xlsx');
    }

    private function isWithinOfficeRadius($latitude, $longitude, ?Location $location)
    {
        if (!$latitude || !$longitude) {
            return false;
        }

        if (!$location || !$location->latitude || !$location->longitude) {
            return false; // Location not configured with geo coordinates
        }

        $distance = $this->haversineDistance($latitude, $longitude, $location->latitude, $location->longitude);
        return $distance <= $location->radius;
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
