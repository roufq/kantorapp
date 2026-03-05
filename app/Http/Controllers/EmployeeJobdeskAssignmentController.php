<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeJobdeskAssignment;
use App\Models\Jobdesk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeJobdeskAssignmentController extends Controller
{
    public function index(Jobdesk $jobdesk)
    {
        $assignments = $jobdesk->assignments()
            ->with(['employee.location'])
            ->orderByDesc('is_primary')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $employees = Employee::with('location')->orderBy('nama')->get();

        return view('jobdesks.assignments', compact('jobdesk', 'assignments', 'employees'));
    }

    public function store(Request $request, Jobdesk $jobdesk)
    {
        $data = $request->validate([
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'required|exists:employees,id',
            'is_primary' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $isPrimary = (bool) ($data['is_primary'] ?? true);
        $startDate = $data['start_date'] ?? null;
        $endDate = $data['end_date'] ?? null;

        foreach ($data['employee_ids'] as $employeeId) {
            if ($isPrimary) {
                // Ensure only one primary jobdesk per employee
                EmployeeJobdeskAssignment::where('employee_id', $employeeId)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            $assignment = EmployeeJobdeskAssignment::where('employee_id', $employeeId)
                ->where('jobdesk_id', $jobdesk->id)
                ->whereNull('end_date')
                ->first();

            if ($assignment) {
                $assignment->update([
                    'is_primary' => $isPrimary,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);
            } else {
                EmployeeJobdeskAssignment::create([
                    'employee_id' => $employeeId,
                    'jobdesk_id' => $jobdesk->id,
                    'is_primary' => $isPrimary,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'created_by' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('jobdesks.assignments.index', $jobdesk)->with('success', 'Assignment jobdesk berhasil disimpan.');
    }

    public function destroy(Jobdesk $jobdesk, EmployeeJobdeskAssignment $assignment)
    {
        if ($assignment->jobdesk_id !== $jobdesk->id) {
            abort(404);
        }

        $assignment->delete();

        return redirect()->route('jobdesks.assignments.index', $jobdesk)->with('success', 'Assignment dihapus.');
    }
}
