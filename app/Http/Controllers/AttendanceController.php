<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceRecapExport;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\Shift;
use App\Models\LocationShift;
use App\Models\ShiftAssignment;
use App\Models\WeeklyRosterEntry;
use App\Models\Overtime;
use App\Models\LocationChangeRequest;
use App\Models\EmployeeAbsence;
use App\Models\EmployeeLeave;
use App\Services\WorkdayService;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelWriter;

class AttendanceController extends Controller
{
    private const MAX_GPS_ACCURACY_M = 60;

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

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'required|numeric',
            'device_id' => 'required|string|max:128',
            'check_in_photo' => 'nullable|image|max:2048',
        ]);

        $maxAccuracy = $this->resolveMaxAccuracy($user->location);
        if ($request->accuracy > $maxAccuracy) {
            return back()->withErrors(['message' => 'Akurasi GPS terlalu rendah (' . round($request->accuracy) . 'm). Coba lagi hingga <= ' . $maxAccuracy . 'm.']);
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
        $tz = $user->location->timezone ?? config('app.timezone', 'UTC');
        $nowInLocation = $now->copy()->setTimezone($tz);
        $dayOfWeek = $nowInLocation->dayOfWeek; // 0=Sunday, 1=Monday, ..., 6=Saturday
        $isLate = false;
        $shiftId = null;
        $shiftAssignmentId = null;
        $usedAssignment = false;
        $slotIntervals = [];

        // Prefer shift assignment (today, fallback overnight dari hari sebelumnya)
        $resolvedAssignment = $this->resolveAssignmentForNow($user, $nowInLocation);
        if ($resolvedAssignment) {
            $assignment = $resolvedAssignment['assignment'];
            $pivot = $resolvedAssignment['pivot'] ?? null;
            $slotIntervals = $resolvedAssignment['intervals'];
            $shiftId = $pivot?->shift_id ?? $assignment?->shift_id;
            $shiftAssignmentId = $assignment?->id;
            $isLate = $this->isLateFromIntervals($slotIntervals, $nowInLocation);
            $usedAssignment = true;
            if ($assignment && $assignment->location_id) {
                $attendanceLocationId = $assignment->location_id;
            }
        }

        // Find active shift for user's location at check-in time (fallback when no assignment)
        if (!$usedAssignment && $user->location_id) {
            $location = Location::with(['shifts' => function ($q) {
                $q->where('is_active', true);
            }])->find($user->location_id);
            if ($location && $location->shift_enabled) {
                $active = $this->findActiveLocationShift($location, $nowInLocation);
                if ($active) {
                    $shiftId = $active['shift']->id;
                    $slotIntervals = $active['intervals'] ?? [];
                    $isLate = $active['start'] ? $nowInLocation->gt($active['start']) : false;
                }
            }
        }

        // Cek roster OFF
        $rosterEntry = \App\Models\WeeklyRosterEntry::where('user_id', $user->id)
            ->whereDate('date', $nowInLocation->toDateString())
            ->first();
        if ($rosterEntry && $rosterEntry->status === 'off') {
            return back()->withErrors(['message' => 'Anda sedang OFF pada tanggal ini.']);
        }

        // Wajib ada jadwal slot
        if (empty($slotIntervals)) {
            return back()->withErrors(['message' => 'Jadwal shift untuk hari ini tidak ditemukan. Hubungi admin.']);
        }

        // Pastikan masih dalam jendela jam kerja (grace early 30 menit)
        $graceEarlyMinutes = 30;
        $insideSlot = false;
        foreach ($slotIntervals as [$start, $end]) {
            $startWithGrace = $start->copy()->subMinutes($graceEarlyMinutes);
            if ($nowInLocation->betweenIncluded($startWithGrace, $end)) {
                $insideSlot = true;
                $isLate = $nowInLocation->gt($start);
                break;
            }
        }
        if (!$insideSlot) {
            $ranges = collect($slotIntervals)->map(fn($i) => $i[0]->format('H:i') . ' - ' . $i[1]->format('H:i'))->implode(', ');
            return back()->withErrors(['message' => 'Di luar jam kerja. Jadwal hari ini: ' . $ranges]);
        }

        $checkInPhotoPath = null;
        if ($request->file('check_in_photo')) {
            $checkInPhotoPath = $request->file('check_in_photo')->store('attendances/checkin', 'public');
        } elseif ($request->filled('check_in_selfie_data')) {
            $checkInPhotoPath = $this->storeSelfieImage($request->input('check_in_selfie_data'), 'attendances/checkin');
        }
        if (!$checkInPhotoPath) {
            return back()->withErrors(['message' => 'Selfie check-in wajib diambil melalui kamera atau unggah foto.']);
        }

        Attendance::create([
            'user_id' => $user->id,
            'location_id' => $attendanceLocationId,
            'shift_id' => $shiftId,
            'shift_assignment_id' => $shiftAssignmentId,
            'check_in_time' => $now,
            'location' => $request->latitude . ',' . $request->longitude,
            'device_id' => $request->device_id,
            'device_user_agent' => (string) $request->userAgent(),
            'gps_accuracy_in' => $request->accuracy,
            'check_in_photo_path' => $checkInPhotoPath,
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

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'required|numeric',
            'device_id' => 'required|string|max:128',
            'check_out_photo' => 'nullable|image|max:2048',
        ]);

        $maxAccuracy = $this->resolveMaxAccuracy($user->location);
        if ($request->accuracy > $maxAccuracy) {
            return back()->withErrors(['message' => 'Akurasi GPS terlalu rendah (' . round($request->accuracy) . 'm). Coba lagi hingga <= ' . $maxAccuracy . 'm.']);
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
        $nowInLocation = $now->copy()->setTimezone($user->location->timezone ?? config('app.timezone', 'UTC'));

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

        // Validasi jam sesuai shift/roster
        $slotIntervals = [];
        $assignment = null;
        if ($attendance->shift_assignment_id) {
            $assignment = \App\Models\ShiftAssignment::with(['locationShift.location'])->find($attendance->shift_assignment_id);
        }
        if (!$assignment) {
            $assignment = \App\Models\ShiftAssignment::with(['locationShift.location'])
                ->where('user_id', $user->id)
                ->whereDate('date', $nowInLocation->toDateString())
                ->first();
        }
        if ($assignment && $assignment->locationShift) {
            $slotIntervals = $assignment->locationShift->slotIntervalsForDate($nowInLocation);
        }
        if (empty($slotIntervals) && $attendance->shift_id && $user->location) {
            // fallback pakai master shift
            $pivot = $this->buildPivotFromShift(Shift::find($attendance->shift_id), $user->location);
            $slotIntervals = $pivot->slotIntervalsForDate($nowInLocation);
        }

        // Tentukan slot yang dipakai (berdasarkan waktu check-in jika memungkinkan)
        $targetInterval = null;
        if (!empty($slotIntervals)) {
            foreach ($slotIntervals as $intv) {
                if ($attendance->check_in_time->betweenIncluded($intv[0], $intv[1])) {
                    $targetInterval = $intv;
                    break;
                }
            }
            if (!$targetInterval) {
                // pilih interval paling awal sebagai fallback
                usort($slotIntervals, fn($a,$b) => $a[0]->gt($b[0]) ? 1 : -1);
                $targetInterval = $slotIntervals[0];
            }
        }

        if ($targetInterval) {
            $plannedEnd = $targetInterval[1];
            if ($nowInLocation->lt($plannedEnd)) {
                return back()->withErrors(['message' => 'Belum waktunya check-out. Shift berakhir: ' . $plannedEnd->format('H:i')]);
            }
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

        $checkOutPhotoPath = null;
        if ($request->file('check_out_photo')) {
            $checkOutPhotoPath = $request->file('check_out_photo')->store('attendances/checkout', 'public');
        } elseif ($request->filled('check_out_selfie_data')) {
            $checkOutPhotoPath = $this->storeSelfieImage($request->input('check_out_selfie_data'), 'attendances/checkout');
        }
        if (!$checkOutPhotoPath) {
            return back()->withErrors(['message' => 'Selfie check-out wajib diambil melalui kamera atau unggah foto.']);
        }

        $attendance->update([
            'check_out_time' => $now,
            'location' => $request->latitude . ',' . $request->longitude,
            'device_id' => $request->device_id,
            'device_user_agent' => (string) $request->userAgent(),
            'gps_accuracy_out' => $request->accuracy,
            'check_out_photo_path' => $checkOutPhotoPath,
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
        $query = Attendance::with(['user.employee', 'shift', 'location', 'shiftAssignment']);

        // Filter by role
        if ($user->hasRole('Location Admin')) {
            $query->where('location_id', $user->location_id);
        } elseif ($user->hasRole('Employee')) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('check_in_time', [$request->start_date, $request->end_date]);
        }

        $attendances = $query->orderBy('check_in_time', 'desc')->paginate(20);

        // Ambil roster untuk baris yang ditampilkan agar sinkron dengan jadwal Weekly Rosters
        $mapKeys = $attendances->getCollection()->map(function ($a) {
            return [
                'user_id' => $a->user_id,
                'date' => $a->check_in_time->toDateString(),
            ];
        });
        $rosterEntries = collect();
        if ($mapKeys->isNotEmpty()) {
            $userIds = $mapKeys->pluck('user_id')->unique();
            $dates = $mapKeys->pluck('date')->unique();
            $rosterEntries = \App\Models\WeeklyRosterEntry::with(['roster.locationShift.shift'])
                ->whereIn('user_id', $userIds)
                ->whereIn('date', $dates)
                ->get()
                ->groupBy(function ($e) {
                    return $e->user_id . '|' . $e->date->toDateString();
                });
        }

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

        return view('attendances.report', [
            'attendances' => $attendances,
            'effectiveLocation' => $effectiveLocation,
            'effectiveTemporary' => $effectiveTemporary,
            'rosterEntries' => $rosterEntries,
        ]);
    }

    public function absences(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\EmployeeAbsence::with('user');

        // Filter by role
        if ($user->hasRole('Location Admin')) {
            $query->where('location_id', $user->location_id);
        } elseif ($user->hasRole('Employee')) {
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

        $format = strtolower((string) ($request->get('format') ?: 'auto'));
        $supportsXls = defined('Maatwebsite\\Excel\\Excel::XLS');
        $writer = null;
        $filename = null;

        if ($format === 'csv') {
            $writer = ExcelWriter::CSV;
            $filename = sprintf(
                'attendance_recap_%s_%s.csv',
                $data['start']->format('Ymd'),
                $data['end']->format('Ymd')
            );
        } elseif ($format === 'xls') {
            if ($supportsXls) {
                $writer = constant('Maatwebsite\\Excel\\Excel::XLS');
                $filename = sprintf(
                    'attendance_recap_%s_%s.xls',
                    $data['start']->format('Ymd'),
                    $data['end']->format('Ymd')
                );
            } else {
                $writer = ExcelWriter::CSV;
                $filename = sprintf(
                    'attendance_recap_%s_%s.csv',
                    $data['start']->format('Ymd'),
                    $data['end']->format('Ymd')
                );
            }
        } elseif ($format === 'xlsx' || ($format === 'auto' && extension_loaded('zip'))) {
            $writer = ExcelWriter::XLSX;
            $filename = sprintf(
                'attendance_recap_%s_%s.xlsx',
                $data['start']->format('Ymd'),
                $data['end']->format('Ymd')
            );
        } elseif ($supportsXls) {
            $writer = constant('Maatwebsite\\Excel\\Excel::XLS');
            $filename = sprintf(
                'attendance_recap_%s_%s.xls',
                $data['start']->format('Ymd'),
                $data['end']->format('Ymd')
            );
        } else {
            $writer = ExcelWriter::CSV;
            $filename = sprintf(
                'attendance_recap_%s_%s.csv',
                $data['start']->format('Ymd'),
                $data['end']->format('Ymd')
            );
        }

        return Excel::download(
            new AttendanceRecapExport($data['rows']),
            $filename,
            $writer
        );
    }

    private function buildRecapData(Request $request): array
    {
        $auth = auth()->user();

        $start = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $end = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        if ($start->gt($end)) { [$start, $end] = [$end, $start]; }

        $usersQuery = \App\Models\User::query();
        if ($auth->hasRole('Location Admin')) {
            $usersQuery->where('location_id', $auth->location_id);
        } elseif ($auth->hasRole('Employee')) {
            $usersQuery->where('id', $auth->id);
        }
        if ($request->filled('user_id')) {
            $usersQuery->where('id', $request->user_id);
        }
        if ($auth->hasRole('Super Admin') && $request->filled('location_id')) {
            $usersQuery->where('location_id', $request->location_id);
        }
        $users = $usersQuery->orderBy('name')->get();

        // Ambil roster non-office (Weekly Rosters) dalam rentang tanggal, dikelompokkan per user|date
        $rosterEntries = \App\Models\WeeklyRosterEntry::with('roster')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('user_id', $users->pluck('id'))
            ->get()
            ->groupBy(function ($e) {
                return $e->user_id . '|' . $e->date->toDateString();
            });

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
                $key = $u->id . '|' . $date->toDateString();
                $roster = $rosterEntries->get($key)?->first();

                // Jika ada roster Weekly Roster, gunakan status roster sebagai dasar
                if ($roster) {
                    $isHoliday = WorkdayService::isHolidayForUser($u, $date);
                    $isWO = $roster->status === 'off';
                } else {
                    $isHoliday = WorkdayService::isHolidayForUser($u, $date);
                    $isWO = WorkdayService::isWeeklyOff($u, $date);
                }
                // Leave dihitung per hari, meskipun ada roster
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

        $before = $attendance->only(['approval_status']);
        $attendance->update([
            'approval_status' => $request->approval_status,
        ]);
        AuditLogger::record('attendance_approval_updated', $attendance, $before, [
            'approval_status' => $attendance->approval_status,
        ]);

        return back()->with('success', 'Approval status updated successfully.');
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', Attendance::class);

        $user = auth()->user();
        $query = Attendance::with(['user.employee', 'shift', 'location']);

        // Filter by role
        if ($user->hasRole('Location Admin')) {
            $query->where('location_id', $user->location_id);
        } elseif ($user->hasRole('Employee')) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
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

    private function buildPivotFromShift(Shift $shift, ?Location $location = null): LocationShift
    {
        $pivot = new LocationShift([
            'location_id' => $location?->id,
            'shift_id' => $shift->id,
            'category' => $shift->category,
            'time_slots' => $shift->time_slots ?? [],
            'is_default' => false,
        ]);
        $pivot->setRelation('shift', $shift);
        if ($location) {
            $pivot->setRelation('location', $location);
        }

        return $pivot;
    }

    private function isLateFromIntervals(array $intervals, Carbon $now): bool
    {
        if (empty($intervals)) {
            return false;
        }

        usort($intervals, fn ($a, $b) => $a[0]->gt($b[0]) ? 1 : -1);

        return $now->gt($intervals[0][0]);
    }

    private function findActiveLocationShift(Location $location, Carbon $now): ?array
    {
        $shifts = $location->relationLoaded('shifts')
            ? $location->shifts
            : $location->shifts()->where('is_active', true)->get();

        $defaultCandidate = null;

        foreach ($shifts as $shift) {
            if (!$shift->is_active) {
                continue;
            }

            $pivot = $shift->pivot instanceof LocationShift ? $shift->pivot : null;
            if (!$pivot) {
                continue;
            }

            if ($pivot->is_default && !$defaultCandidate) {
                $defaultCandidate = [$pivot, $shift];
            }

            $intervals = $pivot->slotIntervalsForDate($now);
            foreach ($intervals as [$start, $end]) {
                if ($now->between($start, $end)) {
                    return [
                        'pivot' => $pivot,
                        'shift' => $shift,
                        'start' => $start,
                        'intervals' => $intervals,
                    ];
                }
            }
        }

        if ($defaultCandidate) {
            [$pivot, $shift] = $defaultCandidate;
            return [
                'pivot' => $pivot,
                'shift' => $shift,
                'start' => $pivot->earliestStartForDate($now),
                'intervals' => $pivot->slotIntervalsForDate($now),
            ];
        }

        return null;
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

    /**
     * Resolve shift assignment and slot intervals for current time (includes overnight from previous day).
     */
    private function resolveAssignmentForNow($user, Carbon $nowInLocation): ?array
    {
        $dates = [
            $nowInLocation->toDateString() => 'today',
            $nowInLocation->copy()->subDay()->toDateString() => 'yesterday',
        ];

        $fallback = null;
        foreach ($dates as $dateStr => $label) {
            $assignment = ShiftAssignment::with(['locationShift.location', 'locationShift.shift', 'shift', 'location'])
                ->where('user_id', $user->id)
                ->whereDate('date', $dateStr)
                ->orderByDesc('id')
                ->first();
            if (!$assignment) {
                continue;
            }

            $pivot = $assignment->locationShift;
            if (!$pivot && $assignment->shift) {
                $pivot = $this->buildPivotFromShift($assignment->shift, $assignment->location);
            }
            if (!$pivot) {
                if (!$fallback) {
                    $fallback = ['assignment' => $assignment, 'pivot' => null, 'intervals' => []];
                }
                continue;
            }

            $baseDate = Carbon::parse($dateStr, $nowInLocation->timezone);
            $intervals = $pivot->slotIntervalsForDate($baseDate);
            $coversNow = false;
            foreach ($intervals as [$start, $end]) {
                $startWithGrace = $start->copy()->subMinutes(60);
                if ($nowInLocation->betweenIncluded($startWithGrace, $end)) {
                    $coversNow = true;
                    break;
                }
            }

            if ($coversNow) {
                return [
                    'assignment' => $assignment,
                    'pivot' => $pivot,
                    'intervals' => $intervals,
                ];
            }

            if (!$fallback) {
                $fallback = [
                    'assignment' => $assignment,
                    'pivot' => $pivot,
                    'intervals' => $intervals,
                ];
            }
        }

        return $fallback;
    }

    private function resolveMaxAccuracy(?Location $location): float
    {
        $default = self::MAX_GPS_ACCURACY_M;
        if (!$location || !is_array($location->settings)) {
            return $default;
        }
        if (!empty($location->settings['max_gps_accuracy_m'])) {
            return max(10, (float) $location->settings['max_gps_accuracy_m']);
        }
        return $default;
    }

    private function storeSelfieImage(string $dataUrl, string $dir): ?string
    {
        if (!str_starts_with($dataUrl, 'data:image/')) {
            return null;
        }

        [$meta, $encoded] = array_pad(explode(',', $dataUrl, 2), 2, null);
        if (!$encoded) {
            return null;
        }

        $binary = base64_decode($encoded, true);
        if ($binary === false) {
            return null;
        }

        $maxBytes = 2 * 1024 * 1024;
        if (strlen($binary) > $maxBytes) {
            return null;
        }

        $ext = 'jpg';
        if (str_contains($meta, 'image/png')) {
            $ext = 'png';
        } elseif (str_contains($meta, 'image/webp')) {
            $ext = 'webp';
        }

        $filename = Str::uuid()->toString() . '.' . $ext;
        $path = trim($dir, '/') . '/' . $filename;
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $binary);

        return $path;
    }
}
