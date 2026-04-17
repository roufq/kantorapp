<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeWorkRecap;
use App\Models\Location;
use App\Models\LocationWorkTarget;
use App\Models\TaskSlot;
use App\Models\User;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkRecapController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        $locationFilter = $request->get('location_id');
        $employeeId = $request->get('employee_id');
        $export = $request->boolean('export', false);

        $locations = $user->hasRole('Super Admin')
            ? Location::all()
            : Location::where('id', $user->location_id)->get();

        $employees = Employee::when(!$user->hasRole('Super Admin'), function ($q) use ($user) {
            $q->where('location_id', $user->location_id);
        })->when($locationFilter, function ($q) use ($locationFilter) {
            $q->where('location_id', $locationFilter);
        })->orderBy('nama')->get();

        // Summary & detail (live calc from slot approved + attendance)
        $slotSummary = [
            'target_minutes' => null,
            'slot_minutes' => 0,
            'attendance_minutes' => 0,
            'remaining' => null,
        ];
        $slotDetails = collect();
        $attendanceDetails = collect();
        $employee = null;
        $employeeName = null;

        if ($employeeId) {
            $employee = Employee::find($employeeId);
            $employeeName = $employee?->nama ?? "Employee {$employeeId}";
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            // user ids untuk employee ini
            $userIds = User::where('employee_id', $employeeId)->pluck('id');
            $locationScoped = $locationFilter ?? ($user->hasRole('Location Admin') ? $user->location_id : null);
            $locationScoped = $locationScoped ?: $user->location_id;

            // Slot approved (berdasarkan assignee user_id)
            $slotQuery = TaskSlot::where('status', 'approved')
                ->whereBetween('approved_at', [$start, $end])
                ->whereHas('task', function ($q) use ($userIds, $locationScoped, $user) {
                    $q->whereIn('assigned_to', $userIds);
                    if ($user->hasRole('Location Admin')) {
                        $q->whereHas('assignee', function ($sq) use ($user) {
                            $sq->where('location_id', $user->location_id);
                        });
                    } elseif ($locationScoped) {
                        $q->whereHas('assignee', function ($sq) use ($locationScoped) {
                            $sq->where('location_id', $locationScoped);
                        });
                    }
                });

            $slotSummary['slot_minutes'] = (int) $slotQuery->sum('minutes');
            $slotDetails = TaskSlot::where('status', 'approved')
                ->whereBetween('approved_at', [$start, $end])
                ->whereHas('task', function ($q) use ($userIds, $locationScoped, $user) {
                    $q->whereIn('assigned_to', $userIds);
                    if ($user->hasRole('Location Admin')) {
                        $q->whereHas('assignee', function ($sq) use ($user) {
                            $sq->where('location_id', $user->location_id);
                        });
                    } elseif ($locationScoped) {
                        $q->whereHas('assignee', function ($sq) use ($locationScoped) {
                            $sq->where('location_id', $locationScoped);
                        });
                    }
                })
                ->with(['task'])
                ->orderByDesc('approved_at')
                ->limit(50)
                ->get(['id', 'task_id', 'name', 'minutes', 'approved_at']);

            // Attendance (durasi check-in/out)
            $attendanceDetails = Attendance::with([
                    'shift',
                    'shiftAssignment.locationShift.location',
                    'shiftAssignment.shift',
                ])
                ->whereIn('user_id', $userIds)
                ->whereBetween('check_in_time', [$start, $end])
                ->when($locationScoped, function ($q, $loc) {
                    $q->where('location_id', $loc);
                })
                ->orderByDesc('check_in_time')
                ->get()
                ->map(function ($att) {
                    $att->duration_minutes = $this->calculateAttendanceDuration($att);
                    return $att;
                });

            $slotSummary['attendance_minutes'] = (int) $attendanceDetails->sum('duration_minutes');

            // Target per bulan (pakai bulan dari start_date), prioritas target employee, lalu target lokasi
            $startCarbon = Carbon::parse($startDate)->startOfMonth();
            $year = (int) $startCarbon->year;
            $month = (int) $startCarbon->month;

            $target = LocationWorkTarget::where('year', $year)
                ->where('month', $month)
                ->where(function ($q) use ($employeeId, $locationScoped) {
                    $q->where(function ($qq) use ($employeeId) {
                        $qq->where('employee_id', $employeeId);
                    })->orWhere(function ($qq) use ($locationScoped) {
                        if ($locationScoped) {
                            $qq->whereNull('employee_id')->where('location_id', $locationScoped);
                        }
                    });
                })
                ->orderByRaw('employee_id is null') // prefer specific employee
                ->first();

            $slotSummary['target_minutes'] = $target?->target_minutes;
            if ($slotSummary['target_minutes'] !== null) {
                // Sisa target hanya dikurangi oleh slot task yang disetujui (kehadiran tidak memotong target)
                $slotSummary['remaining'] = max(0, $slotSummary['target_minutes'] - $slotSummary['slot_minutes']);
            }
        }

        if ($export && $employeeId) {
            return $this->exportPdf($slotSummary, $slotDetails, $attendanceDetails, $startDate, $endDate, $employeeId, $employeeName);
        }

        return view('work-recaps.index', [
            'locations' => $locations,
            'employees' => $employees,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'locationFilter' => $locationFilter,
            'employeeId' => $employeeId,
            'slotSummary' => $slotSummary,
            'slotDetails' => $slotDetails,
            'attendanceDetails' => $attendanceDetails,
            'employeeName' => $employeeName,
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        $monthParam = now()->format('Y-m');

        $locations = $user->hasRole('Super Admin')
            ? Location::all()
            : Location::where('id', $user->location_id)->get();

        $employees = Employee::when(!$user->hasRole('Super Admin'), function ($q) use ($user) {
            $q->where('location_id', $user->location_id);
        })->orderBy('nama')->get();

        return view('work-recaps.create', compact('locations', 'employees', 'monthParam'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'location_id' => 'nullable|exists:locations,id',
            'month' => 'required|date_format:Y-m',
            'slot_minutes_approved' => 'required|integer|min:0',
            'attendance_minutes' => 'required|integer|min:0',
        ]);

        [$year, $month] = explode('-', $data['month']);
        $locationId = $user->hasRole('Location Admin') ? $user->location_id : ($data['location_id'] ?? null);

        $exists = EmployeeWorkRecap::where('employee_id', $data['employee_id'])
            ->where('location_id', $locationId)
            ->where('year', (int) $year)
            ->where('month', (int) $month)
            ->exists();

        if ($exists) {
            return back()->withErrors(['month' => 'Data rekap untuk employee, lokasi, dan bulan tersebut sudah ada.'])->withInput();
        }

        $total = (int) $data['slot_minutes_approved'] + (int) $data['attendance_minutes'];

        EmployeeWorkRecap::create([
            'employee_id' => $data['employee_id'],
            'location_id' => $locationId,
            'year' => (int) $year,
            'month' => (int) $month,
            'slot_minutes_approved' => (int) $data['slot_minutes_approved'],
            'attendance_minutes' => (int) $data['attendance_minutes'],
            'total_minutes' => $total,
        ]);

        return redirect()->route('work-recaps.index')->with('success', 'Rekap berhasil dibuat.');
    }

    public function edit(EmployeeWorkRecap $work_recap)
    {
        $user = Auth::user();
        if ($user->hasRole('Location Admin') && $work_recap->location_id !== $user->location_id) {
            abort(403);
        }

        $monthParam = sprintf('%04d-%02d', $work_recap->year, $work_recap->month);

        $locations = $user->hasRole('Super Admin')
            ? Location::all()
            : Location::where('id', $user->location_id)->get();

        $employees = Employee::when(!$user->hasRole('Super Admin'), function ($q) use ($user) {
            $q->where('location_id', $user->location_id);
        })->orderBy('nama')->get();

        return view('work-recaps.edit', [
            'recap' => $work_recap,
            'locations' => $locations,
            'employees' => $employees,
            'monthParam' => $monthParam,
        ]);
    }

    public function update(Request $request, EmployeeWorkRecap $work_recap)
    {
        $user = Auth::user();
        if ($user->hasRole('Location Admin') && $work_recap->location_id !== $user->location_id) {
            abort(403);
        }

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'location_id' => 'nullable|exists:locations,id',
            'month' => 'required|date_format:Y-m',
            'slot_minutes_approved' => 'required|integer|min:0',
            'attendance_minutes' => 'required|integer|min:0',
        ]);

        [$year, $month] = explode('-', $data['month']);
        $locationId = $user->hasRole('Location Admin') ? $user->location_id : ($data['location_id'] ?? null);

        $exists = EmployeeWorkRecap::where('employee_id', $data['employee_id'])
            ->where('location_id', $locationId)
            ->where('year', (int) $year)
            ->where('month', (int) $month)
            ->where('id', '!=', $work_recap->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['month' => 'Data rekap untuk employee, lokasi, dan bulan tersebut sudah ada.'])->withInput();
        }

        $total = (int) $data['slot_minutes_approved'] + (int) $data['attendance_minutes'];

        $work_recap->update([
            'employee_id' => $data['employee_id'],
            'location_id' => $locationId,
            'year' => (int) $year,
            'month' => (int) $month,
            'slot_minutes_approved' => (int) $data['slot_minutes_approved'],
            'attendance_minutes' => (int) $data['attendance_minutes'],
            'total_minutes' => $total,
        ]);

        return redirect()->route('work-recaps.index')->with('success', 'Rekap diperbarui.');
    }

    public function destroy(EmployeeWorkRecap $work_recap)
    {
        $user = Auth::user();
        if ($user->hasRole('Location Admin') && $work_recap->location_id !== $user->location_id) {
            abort(403);
        }

        $work_recap->delete();

        return redirect()->route('work-recaps.index')->with('success', 'Rekap dihapus.');
    }

    /**
     * Hitung durasi kehadiran (menit) dengan fallback:
     * - check-in/out langsung
     * - jadwal shift assignment (pivot slots)
     * - master shift duration
     */
    private function calculateAttendanceDuration(?Attendance $attendance): int
    {
        if (!$attendance) {
            return 0;
        }

        // Standar hitung 1 hari = 8 jam (480 menit). Overtime di luar 8 jam tidak dihitung.
        $maxDailyMinutes = 480;

        if ($attendance->check_in_time && $attendance->check_out_time) {
            return min($maxDailyMinutes, $attendance->check_in_time->diffInMinutes($attendance->check_out_time));
        }

        $baseDate = $attendance->check_in_time ?? $attendance->created_at;
        if (!$baseDate) {
            return 0;
        }

        // Gunakan slot dari shift assignment jika tersedia
        if ($attendance->shiftAssignment && $attendance->shiftAssignment->locationShift) {
            $intervals = $attendance->shiftAssignment->locationShift->slotIntervalsForDate(Carbon::parse($baseDate));
            $duration = 0;
            foreach ($intervals as [$start, $end]) {
                $duration += $start->diffInMinutes($end);
            }
            if ($duration > 0) {
                return min($maxDailyMinutes, $duration);
            }
        }

        // Fallback ke durasi master shift
        if ($attendance->shift) {
            return min($maxDailyMinutes, (int) $attendance->shift->getDurationInMinutes());
        }

        // Jika tidak ada checkout maupun jadwal, asumsikan 1 hari kerja penuh (8 jam)
        return $maxDailyMinutes;
    }

    private function exportPdf(array $slotSummary, $slotDetails, $attendanceDetails, string $startDate, string $endDate, $employeeId, ?string $employeeName = null)
    {
        $safeName = $employeeName ? Str::slug($employeeName, '-') : $employeeId;
        $pdf = Pdf::loadView('work-recaps.pdf', [
            'slotSummary' => $slotSummary,
            'slotDetails' => $slotDetails,
            'attendanceDetails' => $attendanceDetails,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'employeeId' => $employeeId,
            'employeeName' => $employeeName,
        ])->setPaper('A4', 'landscape');

        $filename = "rekap-jam-kerja-{$safeName}-{$startDate}-{$endDate}.pdf";
        return $pdf->download($filename);
    }
}
