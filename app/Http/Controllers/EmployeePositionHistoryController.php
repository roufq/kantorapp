<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeePositionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeePositionHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeePositionHistory::with('employee')->orderByDesc('start_date');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $histories = $query->paginate(20)->withQueryString();
        $employees = Employee::orderBy('nama')->get();

        return view('employee-positions.index', compact('histories', 'employees'));
    }

    public function create()
    {
        $employees = Employee::orderBy('nama')->get();
        return view('employee-positions.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $data['created_by'] = Auth::id();
        EmployeePositionHistory::create($data);

        return redirect()->route('employee-positions.index')->with('success', 'Histori jabatan disimpan.');
    }

    public function edit(EmployeePositionHistory $employee_position)
    {
        $employees = Employee::orderBy('nama')->get();
        return view('employee-positions.edit', compact('employee_position', 'employees'));
    }

    public function update(Request $request, EmployeePositionHistory $employee_position)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $employee_position->update($data);

        return redirect()->route('employee-positions.index')->with('success', 'Histori jabatan diperbarui.');
    }

    public function destroy(EmployeePositionHistory $employee_position)
    {
        $employee_position->delete();
        return redirect()->route('employee-positions.index')->with('success', 'Histori jabatan dihapus.');
    }
}
