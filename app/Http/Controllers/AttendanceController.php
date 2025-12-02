<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceRecapExport;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\Shift;
use App\Models\Overtime;
use App\Models\LocationChangeRequest;
use App\Models\EmployeeAbsence;
use App\Models\EmployeeLeave;
use App\Services\WorkdayService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelWriter;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $user = auth()->user();

        // Skip check-in on non-working days (weekly off / holiday / approved leave)
        if (\App\Services\WorkdayService::isWorkingDay($user, now()) === false) {
            return back()->withErrors(['message' => 'Hari ini adalah hari libur untuk Anda (mingguan/nasional/izin). Tidak perlu check-in.']);
        }

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

        // Validate location radius compliance (with fallback to approved location change for today)
        $attendanceLocationId = $user->location_id;
        $withinAssigned = $this->isWithinOfficeRadius($request->latitude, $request->longitude, $user->location, $request->accuracy);
        if (!$withinAssigned) {
            $approvedChangeRequest = LocationChangeRequest::where('user_id', $user->id)
                ->whereDate('request_date', Carbon::today())
                ->where('status', 'approved')
                ->orderByDesc('id')
                ->first();
            if ($approvedChangeRequest && $this->isWithinOfficeRadius($request->latitude, $request->longitude, $approvedChangeRequest->targetLocation, $request->accuracy)) {
                $attendanceLocationId = $approvedChangeRequest->target_location_id;
            } else {
                $radius = $user->location && $user->location->radius ? $user->location->radius : 0;
                return back()->withErrors(['message' => 'You must be within ' . $radius . ' meters of your assigned office location to check in.']);
            }
        }

        $now = now();
        $dayOfWeek = $now->dayOfWeek; // 0=Sunday, 1=Monday, ..., 6=Saturday
        $isLate = false;
        $shiftId = null;
        $usedAssignment = false;

        // Prefer shift assignment for today
        if (class_exists(\App\Models\ShiftAssignment::class)) {
            $assignment = \App\Models\ShiftAssignment::where('user_id', $user->id)
                ->whereDate('date', $now->setTimezone($user->location->timezone ?? 'UTC')->toDateString())
                ->with('shift')
                ->first();
            if ($assignment && $assignment->shift) {
                $assignedShift = $assignment->shift;
                if ($assignedShift->isSingleShift()) {
                    $shiftStartTime = Carbon::createFromFormat('H:i', $assignedShift->time_slots['start']);
                    $isLate = $now->gt($shiftStartTime);
                } else {
                    foreach ($assignedShift->time_slots as $slot) {
                        $slotStart = Carbon::createFromFormat('H:i', $slot['start']);
                        if ($now->gt($slotStart)) { $isLate = true; break; }
                    }
                }
                $shiftId = $assignedShift->id;
                $usedAssignment = true;
            }
        }

        

        // Find active shift for user's location at check-in time (fallback when no assignment)
        if (!$usedAssignment && $user->location_id) {
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
            'location_id' => $attendanceLocationId,
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
        $isValidLocation = $this->isWithinOfficeRadius($request->latitude, $request->longitude, $user->location, $request->accuracy);

        if (!$isValidLocation) {
            $approvedChangeRequest = LocationChangeRequest::where('user_id', $user->id)
                ->where('request_date', Carbon::today())
                ->where('status', 'approved')
                ->orderByDesc('id')
                ->first();

            if ($approvedChangeRequest) {
                $isValidLocation = $this->isWithinOfficeRadius($request->latitude, $request->longitude, $approvedChangeRequest->targetLocation, $request->accuracy);
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
        $isWorkingDay = \App\Services\WorkdayService::isWorkingDay($user, now());
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_time', Carbon::today())
            ->first();

        // Compute effective location for today (consider approved change requests)
        $effectiveLocation = $user->location;
        $effectiveTemporary = false;
        $approvedChangeToday = LocationChangeRequest::where('user_id', $user->id)
            ->whereDate('request_date', Carbon::today())
            ->where('status', 'approved')
            ->orderByDesc('id')
            ->first();
        if ($approvedChangeToday && $approvedChangeToday->targetLocation) {
            $effectiveLocation = $approvedChangeToday->targetLocation;
            $effectiveTemporary = !$approvedChangeToday->is_permanent;
        }

        return view('attendances.checkin', compact('todayAttendance', 'isWorkingDay', 'effectiveLocation', 'effectiveTemporary'));
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

        // For header notice: effective location for current user today
        $effectiveLocation = $user->location;
        $effectiveTemporary = false;
        $approvedChangeToday = LocationChangeRequest::where('user_id', $user->id)
            ->whereDate('request_date', Carbon::today())
            ->where('status', 'approved')
            ->orderByDesc('id')
            ->first();
        if ($approvedChangeToday && $approvedChangeToday->targetLocation) {
            $effectiveLocation = $approvedChangeToday->targetLocation;
            $effectiveTemporary = !$approvedChangeToday->is_permanent;
        }

        return view('attendances.report', compact('attendances', 'effectiveLocation', 'effectiveTemporary'));
    }

    public function absences(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\EmployeeAbsence::with('user');

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
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $absences = $query->orderBy('date', 'desc')->paginate(20)->withQueryString();

        return view('attendances.absences', compact('absences'));
    }

    public function recap(Request $request)
    {
        $data = $this->buildRecapData($request);

        return view('attendances.recap', [
            'rows' => $data['rows'],
            'start' => $data['start']->toDateString(),
            'end' => $data['end']->toDateString(),
            'locations' => $data['locations'],
        ]);
    }

    public function exportRecap(Request $request)
    {
        $data = $this->buildRecapData($request);

        $filename = sprintf(
            'attendance_recap_%s_%s.xlsx',
            $data['start']->format('Ymd'),
            $data['end']->format('Ymd')
        );

        return Excel::download(
            new AttendanceRecapExport($data['rows']),
            $filename,
            ExcelWriter::XLSX
        );
    }

    private function buildRecapData(Request $request): array
    {
        $auth = auth()->user();

        $start = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $end = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        if ($start->gt($end)) { [$start, $end] = [$end, $start]; }

        $usersQuery = \App\Models\User::query();
        if ($auth->hasRole('Admin Lokasi')) {
            $usersQuery->where('location_id', $auth->location_id);
        } elseif ($auth->hasRole('Karyawan')) {
            $usersQuery->where('id', $auth->id);
        }
        if ($request->filled('user_id')) {
            $usersQuery->where('id', $request->user_id);
        }
        if ($auth->hasRole('Super Admin') && $request->filled('location_id')) {
            $usersQuery->where('location_id', $request->location_id);
        }
        $users = $usersQuery->orderBy('name')->get();

        $rows = [];
        foreach ($users as $u) {
            $totalDays = 0;
            $holidayDays = 0;
            $weeklyOffDays = 0;
            $leaveDays = 0;
            $workingDays = 0;
            $presentDays = 0;

            foreach (CarbonPeriod::create($start->toDateString(), $end->toDateString()) as $date) {
                $totalDays++;
                $isHoliday = WorkdayService::isHolidayForUser($u, $date);
                $isWO = WorkdayService::isWeeklyOff($u, $date);
                $hasLeave = WorkdayService::hasApprovedLeave($u, $date);

                if ($isHoliday) { $holidayDays++; }
                if ($isWO) { $weeklyOffDays++; }
                if ($hasLeave) { $leaveDays++; }

                if (!$isHoliday && !$isWO && !$hasLeave) {
                    $workingDays++;
                    $hasAtt = Attendance::where('user_id', $u->id)
                        ->whereDate('check_in_time', $date->toDateString())
                        ->exists();
                    if ($hasAtt) { $presentDays++; }
                }
            }

            $alphaDays = max(0, $workingDays - $presentDays);

            $rows[] = [
                'user' => $u,
                'location' => $u->location,
                'totalDays' => $totalDays,
                'holidayDays' => $holidayDays,
                'weeklyOffDays' => $weeklyOffDays,
                'leaveDays' => $leaveDays,
                'workingDays' => $workingDays,
                'presentDays' => $presentDays,
                'alphaDays' => $alphaDays,
            ];
        }

        $locations = $auth->hasRole('Super Admin') ? Location::orderBy('name')->get() : collect();

        return [
            'rows' => $rows,
            'start' => $start,
            'end' => $end,
            'locations' => $locations,
        ];
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

        // Export format selection with environment fallback
        // auto: prefer XLSX if ZIP; else prefer XLS if supported; else CSV.
        $format = strtolower((string) ($request->get('format') ?: 'auto'));
        $supportsXls = defined('Maatwebsite\\Excel\\Excel::XLS');
        $writer = null;
        $filename = null;

        if ($format === 'csv') {
            $writer = \Maatwebsite\Excel\Excel::CSV;
            $filename = 'attendance_report.csv';
        } elseif ($format === 'xls') {
            if ($supportsXls) {
                $writer = constant('Maatwebsite\\Excel\\Excel::XLS');
                $filename = 'attendance_report.xls';
            } else {
                $writer = \Maatwebsite\Excel\Excel::CSV;
                $filename = 'attendance_report.csv';
            }
        } elseif ($format === 'xlsx' || ($format === 'auto' && extension_loaded('zip'))) {
            $writer = \Maatwebsite\Excel\Excel::XLSX;
            $filename = 'attendance_report.xlsx';
        } elseif ($supportsXls) {
            $writer = constant('Maatwebsite\\Excel\\Excel::XLS');
            $filename = 'attendance_report.xls';
        } else {
            $writer = \Maatwebsite\Excel\Excel::CSV;
            $filename = 'attendance_report.csv';
        }
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AttendanceExport($query), $filename, $writer);
    }

    private function isWithinOfficeRadius($latitude, $longitude, ?Location $location, $accuracy = null)
    {
        if (!$latitude || !$longitude) {
            return false;
        }

        if (!$location || !$location->latitude || !$location->longitude) {
            return false; // Location not configured with geo coordinates
        }

        $distance = $this->haversineDistance($latitude, $longitude, $location->latitude, $location->longitude);
        // Apply limited relief using reported accuracy to reduce false negatives on desktop
        $reliefCap = 100; // meters (default cap)
        if (is_array($location->settings) && isset($location->settings['accuracy_relief_m'])) {
            $reliefCap = max(0, (int) $location->settings['accuracy_relief_m']);
        }
        $reportedAccuracy = is_numeric($accuracy) ? (float) $accuracy : null;
        $relief = $reportedAccuracy !== null ? min($reportedAccuracy, $reliefCap) : 0;
        $effectiveDistance = max(0, $distance - $relief);
        return $effectiveDistance <= (float) $location->radius;
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
