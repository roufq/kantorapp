<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeContractController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeContract::with('employee')->orderByDesc('start_date');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $contracts = $query->paginate(20)->withQueryString();
        $employees = Employee::orderBy('nama')->get();

        return view('employee-contracts.index', compact('contracts', 'employees'));
    }

    public function create()
    {
        $employees = Employee::orderBy('nama')->get();
        return view('employee-contracts.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'contract_type' => 'required|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,ended,terminated',
            'notes' => 'nullable|string',
        ]);

        $data['created_by'] = Auth::id();
        EmployeeContract::create($data);

        return redirect()->route('employee-contracts.index')->with('success', 'Kontrak employee disimpan.');
    }

    public function edit(EmployeeContract $employee_contract)
    {
        $employees = Employee::orderBy('nama')->get();
        return view('employee-contracts.edit', compact('employee_contract', 'employees'));
    }

    public function update(Request $request, EmployeeContract $employee_contract)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'contract_type' => 'required|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,ended,terminated',
            'notes' => 'nullable|string',
        ]);

        $employee_contract->update($data);

        return redirect()->route('employee-contracts.index')->with('success', 'Kontrak employee diperbarui.');
    }

    public function destroy(EmployeeContract $employee_contract)
    {
        $employee_contract->delete();
        return redirect()->route('employee-contracts.index')->with('success', 'Kontrak employee dihapus.');
    }
}
