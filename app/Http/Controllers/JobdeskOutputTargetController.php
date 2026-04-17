<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Jobdesk;
use App\Models\JobdeskOutputTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobdeskOutputTargetController extends Controller
{
    public function index(Request $request)
    {
        $query = JobdeskOutputTarget::with(['jobdesk', 'employee'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        if ($request->filled('jobdesk_id')) {
            $query->where('jobdesk_id', $request->jobdesk_id);
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('month')) {
            [$year, $month] = array_pad(explode('-', $request->month), 2, null);
            $query->where('year', (int) $year)->where('month', (int) $month);
        }

        $targets = $query->paginate(20)->withQueryString();
        $jobdesks = Jobdesk::orderBy('name')->get();
        $employees = Employee::orderBy('nama')->get();
        $monthParam = $request->get('month', now()->format('Y-m'));

        return view('jobdesk-targets.index', compact('targets', 'jobdesks', 'employees', 'monthParam'));
    }

    public function create()
    {
        $jobdesks = Jobdesk::orderBy('name')->get();
        $employees = Employee::orderBy('nama')->get();
        $monthParam = now()->format('Y-m');

        return view('jobdesk-targets.create', compact('jobdesks', 'employees', 'monthParam'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jobdesk_id' => 'required|exists:jobdesks,id',
            'employee_id' => 'nullable|exists:employees,id',
            'month' => 'required|date_format:Y-m',
            'unit' => 'required|in:minutes,points,weight',
            'target_value' => 'required|integer|min:0',
        ]);

        [$year, $month] = explode('-', $data['month']);

        $exists = JobdeskOutputTarget::where('jobdesk_id', $data['jobdesk_id'])
            ->where('employee_id', $data['employee_id'])
            ->where('year', (int) $year)
            ->where('month', (int) $month)
            ->exists();
        if ($exists) {
            return back()->withErrors(['month' => 'Target untuk kombinasi jobdesk/employee/bulan sudah ada.'])->withInput();
        }

        JobdeskOutputTarget::create([
            'jobdesk_id' => $data['jobdesk_id'],
            'employee_id' => $data['employee_id'],
            'year' => (int) $year,
            'month' => (int) $month,
            'unit' => $data['unit'],
            'target_value' => (int) $data['target_value'],
        ]);

        return redirect()->route('jobdesk-targets.index')->with('success', 'Target output berhasil dibuat.');
    }

    public function edit(JobdeskOutputTarget $jobdesk_target)
    {
        $jobdesks = Jobdesk::orderBy('name')->get();
        $employees = Employee::orderBy('nama')->get();
        $monthParam = sprintf('%04d-%02d', $jobdesk_target->year, $jobdesk_target->month);

        return view('jobdesk-targets.edit', [
            'target' => $jobdesk_target,
            'jobdesks' => $jobdesks,
            'employees' => $employees,
            'monthParam' => $monthParam,
        ]);
    }

    public function update(Request $request, JobdeskOutputTarget $jobdesk_target)
    {
        $data = $request->validate([
            'jobdesk_id' => 'required|exists:jobdesks,id',
            'employee_id' => 'nullable|exists:employees,id',
            'month' => 'required|date_format:Y-m',
            'unit' => 'required|in:minutes,points,weight',
            'target_value' => 'required|integer|min:0',
        ]);

        [$year, $month] = explode('-', $data['month']);

        $exists = JobdeskOutputTarget::where('jobdesk_id', $data['jobdesk_id'])
            ->where('employee_id', $data['employee_id'])
            ->where('year', (int) $year)
            ->where('month', (int) $month)
            ->where('id', '!=', $jobdesk_target->id)
            ->exists();
        if ($exists) {
            return back()->withErrors(['month' => 'Target untuk kombinasi jobdesk/employee/bulan sudah ada.'])->withInput();
        }

        $jobdesk_target->update([
            'jobdesk_id' => $data['jobdesk_id'],
            'employee_id' => $data['employee_id'],
            'year' => (int) $year,
            'month' => (int) $month,
            'unit' => $data['unit'],
            'target_value' => (int) $data['target_value'],
        ]);

        return redirect()->route('jobdesk-targets.index')->with('success', 'Target output diperbarui.');
    }

    public function destroy(JobdeskOutputTarget $jobdesk_target)
    {
        $jobdesk_target->delete();

        return redirect()->route('jobdesk-targets.index')->with('success', 'Target output dihapus.');
    }
}
