<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeTransfer;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeTransfer::with(['employee', 'fromLocation', 'toLocation'])
            ->orderByDesc('effective_date');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $transfers = $query->paginate(20)->withQueryString();
        $employees = Employee::orderBy('nama')->get();
        $locations = Location::orderBy('name')->get();

        return view('employee-transfers.index', compact('transfers', 'employees', 'locations'));
    }

    public function create()
    {
        $employees = Employee::orderBy('nama')->get();
        $locations = Location::orderBy('name')->get();
        return view('employee-transfers.create', compact('employees', 'locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'from_location_id' => 'nullable|exists:locations,id',
            'to_location_id' => 'nullable|exists:locations,id',
            'effective_date' => 'nullable|date',
            'reason' => 'nullable|string',
        ]);

        $data['created_by'] = Auth::id();
        EmployeeTransfer::create($data);

        return redirect()->route('employee-transfers.index')->with('success', 'Mutasi karyawan disimpan.');
    }

    public function edit(EmployeeTransfer $employee_transfer)
    {
        $employees = Employee::orderBy('nama')->get();
        $locations = Location::orderBy('name')->get();
        return view('employee-transfers.edit', compact('employee_transfer', 'employees', 'locations'));
    }

    public function update(Request $request, EmployeeTransfer $employee_transfer)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'from_location_id' => 'nullable|exists:locations,id',
            'to_location_id' => 'nullable|exists:locations,id',
            'effective_date' => 'nullable|date',
            'reason' => 'nullable|string',
        ]);

        $employee_transfer->update($data);

        return redirect()->route('employee-transfers.index')->with('success', 'Mutasi karyawan diperbarui.');
    }

    public function destroy(EmployeeTransfer $employee_transfer)
    {
        $employee_transfer->delete();
        return redirect()->route('employee-transfers.index')->with('success', 'Mutasi karyawan dihapus.');
    }
}
