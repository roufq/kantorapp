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
        $masters = User::role('Super Admin')->with('employee')->get();
        return view('masters.index', compact('masters'));
    }

    public function create()
    {
        $linkedEmployeeIds = User::whereNotNull('karyawan_id')->pluck('karyawan_id');
        $karyawans = Employee::whereNotIn('id', $linkedEmployeeIds)->get();
        return view('masters.create', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'karyawan_id' => 'required|exists:employees,id',
        ]);

        $karyawan = Employee::find($request->karyawan_id);
        if ($karyawan->users()->whereHas('roles', function ($q) { $q->where('name', 'Super Admin'); })->exists()) {
            return back()->withErrors(['karyawan_id' => 'This employee already has a Super Admin account.']);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'karyawan_id' => $karyawan->id,
        ]);

        $user->assignRole('Super Admin');

        return redirect()->route('masters.index')->with('success', 'Master created successfully.');
    }

    public function show(User $master)
    {
        if (!$master->hasRole('Super Admin')) {
            abort(404);
        }
        $master->load('karyawan');
        return view('masters.show', compact('master'));
    }

    public function edit(User $master)
    {
        if (!$master->hasRole('Super Admin')) {
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
            'karyawan_id' => 'required|exists:employees,id',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'karyawan_id' => $request->karyawan_id,
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
