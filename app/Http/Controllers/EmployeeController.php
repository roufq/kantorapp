<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Division;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        // Authorize all resource methods using the EmployeePolicy.
        // The 'karyawan' parameter name must match the route parameter name.
        $this->authorizeResource(Employee::class, 'karyawan');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Employee::with(['division', 'location']);

        // If the user is an Admin Lokasi, only show employees from their location.
        // Super Admins will not be affected by this and will see all.
        if ($user->hasRole('Admin Lokasi')) {
            $query->where('location_id', $user->location_id);
        }

        $employees = $query->get();

        return view('karyawans.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $divisions = Division::all();
        $locations = Location::active()->orderBy('name')->get();
        return view('karyawans.create', compact('divisions', 'locations'));
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
            'location_id' => 'nullable|exists:locations,id',
        ]);

        // Enforce Admin Lokasi boundary on location_id
        $auth = Auth::user();
        $data = $request->all();
        if ($auth->hasRole('Admin Lokasi')) {
            // If not provided, default to admin's location; if provided, must match
            $data['location_id'] = $auth->location_id;
        }

        Employee::create($data);

        return redirect()->route('karyawans.index')->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $karyawan)
    {
        $karyawan->load(['division', 'location']);
        return view('karyawans.show', compact('karyawan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $karyawan)
    {
        $divisions = Division::all();
        $locations = Location::active()->orderBy('name')->get();
        return view('karyawans.edit', compact('karyawan', 'divisions', 'locations'));
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
            'location_id' => 'nullable|exists:locations,id',
        ]);

        // Enforce Admin Lokasi boundary on location_id
        $auth = Auth::user();
        $data = $request->all();
        if ($auth->hasRole('Admin Lokasi')) {
            $data['location_id'] = $auth->location_id;
        }

        $karyawan->update($data);

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
