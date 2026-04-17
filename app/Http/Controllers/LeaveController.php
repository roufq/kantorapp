<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLeave;
use App\Models\EmployeeLeaveBalance;
use App\Services\WorkdayService;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index()
    {
        $auth = Auth::user();
        $q = EmployeeLeave::with(['user','approver']);
        if ($auth->hasRole('Location Admin')) {
            $q->where('location_id', $auth->location_id);
        } elseif ($auth->hasRole('Employee')) {
            $q->where('user_id', $auth->id);
        }
        $leaves = $q->orderBy('start_date','desc')->paginate(15);
        $balanceSummary = $this->buildAnnualBalanceSummary($auth, (int) now()->format('Y'));
        return view('leaves.index', compact('leaves', 'balanceSummary'));
    }

    public function store(Request $request)
    {
        $auth = Auth::user();
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:sick,annual,unpaid,other',
            'reason' => 'nullable|string|max:255',
        ]);
        // Cegah overlap dengan pengajuan/izin yang sudah ada (pending/approved)
        $overlap = EmployeeLeave::where('user_id', $auth->id)
            ->whereIn('status', ['pending','approved'])
            ->whereDate('start_date', '<=', $request->end_date)
            ->whereDate('end_date', '>=', $request->start_date)
            ->exists();
        if ($overlap) {
            return back()->withErrors(['start_date' => 'Rentang tanggal bertabrakan dengan pengajuan lain.'])->withInput();
        }

        if ($request->type === 'annual') {
            $startYear = Carbon::parse($request->start_date)->year;
            $endYear = Carbon::parse($request->end_date)->year;
            if ($startYear !== $endYear) {
                return back()->withErrors(['end_date' => 'Cuti tahunan harus berada dalam tahun yang sama.'])->withInput();
            }
            $year = $startYear;
            $balanceSummary = $this->buildAnnualBalanceSummary($auth, $year);
            $requestedDays = $this->countChargeableLeaveDays($auth, $request->start_date, $request->end_date);
            if ($requestedDays <= 0) {
                return back()->withErrors(['start_date' => 'Tidak ada hari kerja dalam rentang tanggal yang dipilih.'])->withInput();
            }
            if ($requestedDays > $balanceSummary['remaining']) {
                return back()->withErrors(['start_date' => 'Saldo cuti tahunan tidak cukup. Sisa: ' . $balanceSummary['remaining'] . ' hari.'])->withInput();
            }
        }

        EmployeeLeave::create([
            'user_id' => $auth->id,
            'location_id' => $auth->location_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);
        return back()->with('success', 'Leave request submitted');
    }

    public function updateStatus(Request $request, EmployeeLeave $leave)
    {
        $auth = Auth::user();
        abort_unless($auth->hasRole('Super Admin') || ($auth->hasRole('Location Admin') && $auth->location_id === $leave->location_id), 403);
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);
        $before = $leave->only(['status', 'approved_by']);
        $leave->update([
            'status' => $request->status,
            'approved_by' => $request->status === 'approved' ? $auth->id : null,
        ]);
        AuditLogger::record('leave_status_updated', $leave, $before, [
            'status' => $leave->status,
            'approved_by' => $leave->approved_by,
        ]);
        // Kirim notifikasi ke employee terkait
        try {
            $msg = 'Status Izin/Cuti Anda (' . $leave->start_date->format('Y-m-d') . ' s/d ' . $leave->end_date->format('Y-m-d') . ', ' . $leave->type . ') berubah menjadi: ' . $request->status;
            \App\Models\Message::create([
                'sender_id' => $auth->id,
                'receiver_id' => $leave->user_id,
                'message' => $msg,
            ]);
        } catch (\Throwable $e) {}

        return back()->with('success','Leave status updated');
    }

    private function buildAnnualBalanceSummary($user, int $year): array
    {
        $balance = EmployeeLeaveBalance::firstOrCreate(
            ['user_id' => $user->id, 'year' => $year],
            ['annual_quota' => 12, 'carry_over' => 0]
        );

        $used = $this->countApprovedAnnualLeaveDays($user, $year);
        $totalQuota = $balance->annual_quota + $balance->carry_over;
        $remaining = max(0, $totalQuota - $used);

        return [
            'year' => $year,
            'quota' => $balance->annual_quota,
            'carry_over' => $balance->carry_over,
            'used' => $used,
            'remaining' => $remaining,
        ];
    }

    private function countApprovedAnnualLeaveDays($user, int $year): int
    {
        $yearStart = Carbon::create($year, 1, 1)->startOfDay();
        $yearEnd = Carbon::create($year, 12, 31)->endOfDay();

        $leaves = EmployeeLeave::where('user_id', $user->id)
            ->where('type', 'annual')
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $yearEnd->toDateString())
            ->whereDate('end_date', '>=', $yearStart->toDateString())
            ->get();

        $total = 0;
        foreach ($leaves as $leave) {
            $start = Carbon::parse($leave->start_date)->max($yearStart);
            $end = Carbon::parse($leave->end_date)->min($yearEnd);
            $total += $this->countChargeableLeaveDays($user, $start, $end);
        }

        return $total;
    }

    private function countChargeableLeaveDays($user, $startDate, $endDate): int
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();
        if ($start->gt($end)) {
            [$start, $end] = [$end, $start];
        }

        $count = 0;
        foreach (CarbonPeriod::create($start, $end) as $date) {
            if (WorkdayService::isWeeklyOff($user, $date)) {
                continue;
            }
            if (WorkdayService::isHolidayForUser($user, $date)) {
                continue;
            }
            $count++;
        }
        return $count;
    }
}
