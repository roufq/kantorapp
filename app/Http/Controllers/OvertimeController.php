<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Overtime;
use App\Models\OvertimeApproval;
use App\Models\User;
use Spatie\Permission\Models\Role;

class OvertimeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            // Super Admins can see all overtime requests
            $query = Overtime::with('user', 'approvals.master');
        } elseif ($user->hasRole('Admin Lokasi')) {
            // Admin Lokasi can see overtime requests for users in their location
            $loc = $user->location_id;
            $query = Overtime::whereHas('user', function ($q) use ($loc) {
                $q->where('location_id', $loc);
            })->with('user', 'approvals.master');
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
        $user = Auth::user();

        if (!$user->hasRole('Super Admin') && !$user->hasRole('Karyawan')) {
            abort(403, 'Not authorized to request overtime');
        }

        $masters = collect();
        if ($superAdminRole = $this->findRole('Super Admin')) {
            $masters = User::whereHas('roles', function ($query) use ($superAdminRole) {
                $query->where('id', $superAdminRole->id);
            })->orderBy('id')->get();
        }
        $autoApprovers = collect();

        if ($user->hasRole('Karyawan')) {
            $autoApprovers = $this->getApproverUsersForEmployee($user);

            if ($autoApprovers->isEmpty()) {
                return redirect()->route('overtime.index')->withErrors('Tidak ada approver yang tersedia. Hubungi administrator.');
            }
        }

        return view('overtime.create', compact('masters', 'autoApprovers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('Super Admin') && !$user->hasRole('Karyawan')) {
            abort(403, 'Not authorized to request overtime');
        }

        $rules = [
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:1000',
        ];

        if ($user->hasRole('Super Admin')) {
            $rules['selected_masters'] = 'required|array|min:1|max:2';
            $rules['selected_masters.*'] = 'exists:users,id';
        }

        $request->validate($rules);

        // Parse times as WIB (Asia/Jakarta) and convert to UTC for storage
        $date = $request->date;
        $startTimeWib = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $request->start_time, 'Asia/Jakarta');
        $endTimeWib = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $request->end_time, 'Asia/Jakarta');

        // Convert to UTC for storage
        $startTimeUtc = $startTimeWib->utc();
        $endTimeUtc = $endTimeWib->utc();

        // Calculate duration in hours and minutes
        $durationMinutes = $startTimeWib->diffInMinutes($endTimeWib);
        $durationHours = $durationMinutes / 60;

        if ($user->hasRole('Super Admin')) {
            $selectedMasterIds = array_map('intval', $request->selected_masters);
        } else {
            $selectedMasterIds = $this->resolveApproverIdsForEmployee($user);

            if (empty($selectedMasterIds)) {
                return back()->withErrors('Tidak ada approver yang tersedia. Hubungi administrator.');
            }
        }

        // For Super Admins, auto-approve the overtime request
        $status = $user->hasRole('Super Admin') ? 'approved' : 'pending';

        $overtime = Overtime::create([
            'user_id' => $user->id,
            'date' => $request->date,
            'start_time' => $startTimeUtc->format('H:i:s'),
            'end_time' => $endTimeUtc->format('H:i:s'),
            'duration_hours' => $durationHours,
            'reason' => $request->reason,
            'selected_masters' => $selectedMasterIds,
            'status' => $status,
        ]);

        // Create approval records for selected masters
        foreach ($selectedMasterIds as $masterId) {
            OvertimeApproval::create([
                'overtime_request_id' => $overtime->id,
                'master_id' => $masterId,
                'status' => $user->hasRole('Super Admin') ? 'approved' : 'pending',
                'approved_at' => $user->hasRole('Super Admin') ? now() : null,
            ]);
        }

        return redirect()->route('overtime.index')->with('success', 'Overtime request submitted successfully!');
    }

    public function edit(Overtime $overtime)
    {
        $this->authorizeEmployeeAccess($overtime);

        if ($overtime->status !== 'pending') {
            return redirect()->route('overtime.show', $overtime)->withErrors('Only pending requests can be edited.');
        }

        $masters = collect();
        if ($superAdminRole = $this->findRole('Super Admin')) {
            $masters = User::whereHas('roles', function ($query) use ($superAdminRole) {
                $query->where('id', $superAdminRole->id);
            })->orderBy('id')->get();
        }
        $autoApprovers = collect();

        if ($overtime->user && $overtime->user->hasRole('Karyawan')) {
            $autoApprovers = $this->getApproverUsersForEmployee($overtime->user);
        }

        return view('overtime.edit', compact('overtime', 'masters', 'autoApprovers'));
    }

    public function update(Request $request, Overtime $overtime)
    {
        $this->authorizeEmployeeAccess($overtime);

        if ($overtime->status !== 'pending') {
            return redirect()->route('overtime.show', $overtime)->withErrors('Only pending requests can be updated.');
        }

        $user = Auth::user();

        $rules = [
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:1000',
        ];

        if ($user->hasRole('Super Admin')) {
            $rules['selected_masters'] = 'required|array|min:1|max:2';
            $rules['selected_masters.*'] = 'exists:users,id';
        }

        $request->validate($rules);

        $date = $request->date;
        $startTimeWib = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $request->start_time, 'Asia/Jakarta');
        $endTimeWib = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $request->end_time, 'Asia/Jakarta');

        $startTimeUtc = $startTimeWib->clone()->utc();
        $endTimeUtc = $endTimeWib->clone()->utc();

        $durationHours = $startTimeWib->diffInMinutes($endTimeWib) / 60;

        if ($user->hasRole('Super Admin')) {
            $selectedMasterIds = array_map('intval', $request->selected_masters);
        } else {
            $selectedMasterIds = $this->resolveApproverIdsForEmployee($overtime->user);

            if (empty($selectedMasterIds)) {
                return back()->withErrors('Tidak ada approver yang tersedia. Hubungi administrator.');
            }
        }

        $overtime->update([
            'date' => $request->date,
            'start_time' => $startTimeUtc->format('H:i:s'),
            'end_time' => $endTimeUtc->format('H:i:s'),
            'duration_hours' => $durationHours,
            'reason' => $request->reason,
            'selected_masters' => $selectedMasterIds,
            'status' => $user->hasRole('Super Admin') ? 'approved' : 'pending',
        ]);

        $overtime->approvals()->delete();
        foreach ($selectedMasterIds as $masterId) {
            OvertimeApproval::create([
                'overtime_request_id' => $overtime->id,
                'master_id' => $masterId,
                'status' => $user->hasRole('Super Admin') ? 'approved' : 'pending',
                'approved_at' => $user->hasRole('Super Admin') ? now() : null,
            ]);
        }

        return redirect()->route('overtime.show', $overtime)->with('success', 'Overtime request updated successfully.');
    }

    public function destroy(Overtime $overtime)
    {
        $this->authorizeEmployeeAccess($overtime);

        if ($overtime->status !== 'pending') {
            return redirect()->route('overtime.show', $overtime)->withErrors('Only pending requests can be cancelled.');
        }

        $overtime->approvals()->delete();
        $overtime->delete();

        return redirect()->route('overtime.index')->with('success', 'Overtime request cancelled.');
    }

    private function authorizeEmployeeAccess(Overtime $overtime): void
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            return;
        }

        if ($user->hasRole('Karyawan') && $overtime->user_id === $user->id) {
            return;
        }

        abort(403, 'You are not authorized to manage this overtime request');
    }

    public function show(Overtime $overtime)
    {
        $user = Auth::user();

        if ($user->hasRole('Karyawan') && $overtime->user_id !== $user->id) {
            abort(403, 'You can only view your own overtime requests');
        }
        if ($user->hasRole('Admin Lokasi')) {
            if ($overtime->user && $overtime->user->location_id !== $user->location_id) {
                abort(403, 'Not authorized to view this request');
            }
        }

        $overtime->load('user', 'approvals.master');

        return view('overtime.show', compact('overtime'));
    }

    public function report(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            // Super Admins can see all overtime requests
            $query = Overtime::with('user', 'approvals.master');
        } elseif ($user->hasRole('Admin Lokasi')) {
            $loc = $user->location_id;
            $query = Overtime::whereHas('user', function ($q) use ($loc) {
                $q->where('location_id', $loc);
            })->with('user', 'approvals.master');
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

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $overtimes = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('overtime.report', compact('overtimes'));
    }

    public function export(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            // Super Admins can export all overtime requests
            $query = Overtime::with('user', 'approvals.master');
        } elseif ($user->hasRole('Admin Lokasi')) {
            $loc = $user->location_id;
            $query = Overtime::whereHas('user', function ($q) use ($loc) {
                $q->where('location_id', $loc);
            })->with('user', 'approvals.master');
        } else {
            // Employees can only export their own requests
            $query = Overtime::where('user_id', $user->id)->with('user', 'approvals.master');
        }

        // Apply same filters as report
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $query->orderBy('created_at', 'desc');

        // Export format selection with environment fallback
        $format = strtolower((string) ($request->get('format') ?: 'auto'));
        $supportsXls = defined('Maatwebsite\\Excel\\Excel::XLS');
        $writer = null;
        $filename = null;

        if ($format === 'csv') {
            $writer = \Maatwebsite\Excel\Excel::CSV;
            $filename = 'overtime_report.csv';
        } elseif ($format === 'xls') {
            if ($supportsXls) {
                $writer = constant('Maatwebsite\\Excel\\Excel::XLS');
                $filename = 'overtime_report.xls';
            } else {
                $writer = \Maatwebsite\Excel\Excel::CSV;
                $filename = 'overtime_report.csv';
            }
        } elseif ($format === 'xlsx' || ($format === 'auto' && extension_loaded('zip'))) {
            $writer = \Maatwebsite\Excel\Excel::XLSX;
            $filename = 'overtime_report.xlsx';
        } elseif ($supportsXls) {
            $writer = constant('Maatwebsite\\Excel\\Excel::XLS');
            $filename = 'overtime_report.xls';
        } else {
            $writer = \Maatwebsite\Excel\Excel::CSV;
            $filename = 'overtime_report.csv';
        }
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\OvertimeExport($query), $filename, $writer);
    }

    public function approve(Request $request, Overtime $overtime)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['Super Admin', 'Admin Lokasi'])) {
            abort(403, 'Not authorized to approve overtime requests');
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

    private function getApproverUsersForEmployee(User $user)
    {
        $locationAdmins = collect();

        if ($user->location_id && ($locationRole = $this->findRole('Admin Lokasi'))) {
            $locationAdmins = User::where('location_id', $user->location_id)
                ->whereHas('roles', function ($query) use ($locationRole) {
                    $query->where('id', $locationRole->id);
                })
                ->orderBy('id')
                ->take(2)
                ->get();
        }

        if ($locationAdmins->isNotEmpty()) {
            return $locationAdmins;
        }

        if ($superAdminRole = $this->findRole('Super Admin')) {
            return User::whereHas('roles', function ($query) use ($superAdminRole) {
                    $query->where('id', $superAdminRole->id);
                })
                ->orderBy('id')
                ->take(2)
                ->get();
        }

        return collect();
    }

    private function resolveApproverIdsForEmployee(User $user): array
    {
        return $this->getApproverUsersForEmployee($user)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();
    }

    private function roleExists(string $roleName): bool
    {
        return $this->findRole($roleName) !== null;
    }

    private function findRole(string $roleName): ?Role
    {
        return Role::where('name', $roleName)->first();
    }
}
