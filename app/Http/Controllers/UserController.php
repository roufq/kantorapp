<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Employee;
use App\Models\Location;

class UserController extends Controller
{
    public function index()
    {
        $authUser = Auth::user();
        if (!$authUser->hasRole('Super Admin') && !$authUser->hasRole('Admin Lokasi')) {
            abort(403, 'Unauthorized');
        }
        $query = User::role('Karyawan')->with('karyawan', 'location');
        if (!$authUser->hasRole('Super Admin')) {
            $query->where('location_id', $authUser->location_id);
        }
        $employees = $query->get();
        return view('users.index', compact('employees'));
    }

    public function create()
    {
        $authUser = Auth::user();
        Gate::authorize('create-user', $authUser->location_id);
        $linkedEmployeeIds = User::whereNotNull('karyawan_id')->pluck('karyawan_id');
        $karyawans = Employee::whereNotIn('id', $linkedEmployeeIds)
            ->when($authUser->hasRole('Admin Lokasi'), function ($q) use ($authUser) {
                $q->where('location_id', $authUser->location_id);
            })
            ->get();
        $locations = \App\Models\Location::when($authUser->hasRole('Admin Lokasi'), function ($q) use ($authUser) {
                $q->where('id', $authUser->location_id);
            })->get();
        return view('users.create', compact('karyawans', 'locations'));
    }

    public function store(Request $request)
    {
        $authUser = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'karyawan_id' => 'required|exists:employees,id',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        $targetLocation = $request->location_id ?: ($authUser->hasRole('Admin Lokasi') ? $authUser->location_id : null);
        Gate::authorize('create-user', $targetLocation);

        $karyawan = Employee::find($request->karyawan_id);
        if ($karyawan->users()->exists()) {
            return back()->withErrors(['karyawan_id' => 'This karyawan already has an employee user account.']);
        }

        $user = User::create([
            'name' => $karyawan->nama,
            'email' => $karyawan->email,
            'password' => Hash::make($request->password),
            // Maintain both legacy and new linkage for compatibility
            'karyawan_id' => $karyawan->id,
            'employee_id' => $karyawan->id,
            'location_id' => $targetLocation,
        ]);

        // Assign application role
        $user->assignRole('Karyawan');

        return redirect()->route('users.index')->with('success', 'Employee user created successfully.');
    }

    public function show(User $user)
    {
        $locations = Location::all();
        return view('users.show', compact('user', 'locations'));
    }

    public function edit(User $user)
    {
        Gate::authorize('manage-user', $user);
        $locations = \App\Models\Location::all();
        return view('users.edit', compact('user', 'locations'));
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('manage-user', $user);

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
        Gate::authorize('manage-user', $user);

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Employee deleted successfully.');
    }

    public function transfer(Request $request, User $user)
    {
        Gate::authorize('update-user-location', $user);

        $request->validate([
            'location_id' => 'required|exists:locations,id',
        ]);

        $user->update([
            'location_id' => $request->location_id,
        ]);

        return redirect()->route('users.show', $user)->with('success', 'User transferred to new location.');
    }

    public function promoteToLocationAdmin(Request $request, User $user)
    {
        // Only Super Admin via route middleware; ensure location selected
        $request->validate([
            'location_id' => 'required|exists:locations,id',
        ]);

        $user->update(['location_id' => $request->location_id]);
        if (!$user->hasRole('Admin Lokasi')) {
            $user->assignRole('Admin Lokasi');
        }
        // Optional: ensure no duplicate Karyawan role confusion
        if ($user->hasRole('Karyawan')) {
            $user->removeRole('Karyawan');
        }

        return redirect()->route('users.show', $user)->with('success', 'User promoted to Location Admin.');
    }

    public function demoteToEmployee(Request $request, User $user)
    {
        // Only Super Admin via route middleware
        if ($user->hasRole('Admin Lokasi')) {
            $user->removeRole('Admin Lokasi');
        }
        if (!$user->hasRole('Karyawan')) {
            $user->assignRole('Karyawan');
        }
        return redirect()->route('users.show', $user)->with('success', 'User demoted to Employee.');
    }
}
