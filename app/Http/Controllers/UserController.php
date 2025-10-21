<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Employee;

class UserController extends Controller
{
    public function index()
    {
        $employees = User::where('role', 'employee')->with('karyawan', 'location')->get();
        return view('users.index', compact('employees'));
    }

    public function create()
    {
        $linkedEmployeeIds = User::whereNotNull('karyawan_id')->pluck('karyawan_id');
        $karyawans = Employee::whereNotIn('id', $linkedEmployeeIds)->get();
        $locations = \App\Models\Location::all();
        return view('users.create', compact('karyawans', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'karyawan_id' => 'required|exists:employees,id',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        $karyawan = Employee::find($request->karyawan_id);
        if ($karyawan->users()->where('role', 'employee')->exists()) {
            return back()->withErrors(['karyawan_id' => 'This karyawan already has an employee user account.']);
        }

        $user = User::create([
            'name' => $karyawan->nama,
            'email' => $karyawan->email,
            'password' => Hash::make($request->password),
            'role' => 'employee',
            'karyawan_id' => $karyawan->id,
            'location_id' => $request->location_id,
        ]);

        return redirect()->route('users.index')->with('success', 'Employee user created successfully.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        if ($user->role !== 'employee') {
            abort(403, 'Only employees can be edited.');
        }
        $locations = \App\Models\Location::all();
        return view('users.edit', compact('user', 'locations'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role !== 'employee') {
            abort(403, 'Only employees can be updated.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'location_id' => $request->location_id,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('users.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->role !== 'employee') {
            abort(403, 'Only employees can be deleted.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Employee deleted successfully.');
    }
}
