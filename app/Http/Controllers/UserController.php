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
        $employees = User::where('role', 'employee')->with('employee')->get();
        return view('users.index', compact('employees'));
    }

    public function create()
    {
        $linkedEmployeeIds = User::whereNotNull('employee_id')->pluck('employee_id');
        $karyawans = Employee::whereNotIn('id', $linkedEmployeeIds)->get();
        return view('users.create', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'employee_id' => 'required|exists:employees,id',
        ]);

        $karyawan = Employee::find($request->karyawan_id);
        if ($karyawan->users()->exists()) {
            return back()->withErrors(['karyawan_id' => 'This karyawan already has a user account.']);
        }

        $user = User::create([
            'name' => $karyawan->nama,
            'email' => $karyawan->email,
            'password' => Hash::make($request->password),
            'role' => 'employee',
            'employee_id' => $karyawan->id,
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
        return view('users.edit', compact('user'));
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
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
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
