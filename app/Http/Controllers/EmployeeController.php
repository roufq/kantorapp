<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Division;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with('division')->get();
        return view('karyawans.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $divisions = Division::all();
        return view('karyawans.create', compact('divisions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:employees',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'jabatan' => 'nullable|string|max:255',
            'departemen' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'divisi_id' => 'required|exists:divisions,id',
        ]);

        Employee::create($request->all());

        return redirect()->route('karyawans.index')->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $karyawan)
    {
        $karyawan->load('division');
        return view('karyawans.show', compact('karyawan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $karyawan)
    {
        $divisions = Division::all();
        return view('karyawans.edit', compact('karyawan', 'divisions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $karyawan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:employees,email,' . $karyawan->id,
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'jabatan' => 'nullable|string|max:255',
            'departemen' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'divisi_id' => 'required|exists:divisions,id',
        ]);

        $karyawan->update($request->all());

        return redirect()->route('karyawans.index')->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $karyawan)
    {
        $karyawan->delete();

        return redirect()->route('karyawans.index')->with('success', 'Employee deleted successfully.');
    }
}
