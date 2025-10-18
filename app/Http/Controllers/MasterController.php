<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Employee;

class MasterController extends Controller
{
    public function index()
    {
        $masters = User::where('role', 'master')->with('karyawan')->get();
        return view('masters.index', compact('masters'));
    }

    public function create()
    {
        $linkedEmployeeIds = User::whereNotNull('employee_id')->pluck('employee_id');
        $karyawans = Employee::whereNotIn('id', $linkedEmployeeIds)->get();
        return view('masters.create', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'employee_id' => 'required|exists:employees,id',
        ]);

        $employee = Employee::find($request->employee_id);
        if ($employee->users()->where('role', 'master')->exists()) {
            return back()->withErrors(['employee_id' => 'This karyawan already has a master account.']);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'master',
            'employee_id' => $employee->id,
        ]);

        return redirect()->route('masters.index')->with('success', 'Master created successfully.');
    }

    public function show(User $master)
    {
        if ($master->role !== 'master') {
            abort(404);
        }
        $master->load('karyawan');
        return view('masters.show', compact('master'));
    }

    public function edit(User $master)
    {
        if ($master->role !== 'master') {
            abort(404);
        }
        $karyawans = Employee::all();
        return view('masters.edit', compact('master', 'karyawans'));
    }

    public function update(Request $request, User $master)
    {
        if ($master->role !== 'master') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $master->id,
            'password' => 'nullable|string|min:8|confirmed',
            'employee_id' => 'required|exists:employees,id',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'employee_id' => $request->employee_id,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $master->update($updateData);

        return redirect()->route('masters.index')->with('success', 'Master updated successfully.');
    }

    public function destroy(User $master)
    {
        if ($master->role !== 'master') {
            abort(404);
        }

        $master->delete();

        return redirect()->route('masters.index')->with('success', 'Master deleted successfully.');
    }
}
