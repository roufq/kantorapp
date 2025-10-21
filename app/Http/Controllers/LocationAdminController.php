<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Location;

class LocationAdminController extends Controller
{
    public function index()
    {
        $this->authorizeSuperAdmin();

        $admins = User::role('Admin Lokasi')->with('location')->orderBy('name')->paginate(15);
        return view('location-admins.index', compact('admins'));
    }

    public function create()
    {
        $this->authorizeSuperAdmin();

        $locations = Location::active()->orderBy('name')->get();
        return view('location-admins.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'location_id' => 'required|exists:locations,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'location_id' => $request->location_id,
        ]);
        $user->assignRole('Admin Lokasi');

        return redirect()->route('location-admins.index')->with('success', 'Location Admin created successfully.');
    }

    public function show(User $location_admin)
    {
        $this->authorizeSuperAdmin();
        if (!$location_admin->hasRole('Admin Lokasi')) { abort(404); }
        $location_admin->load('location');
        return view('location-admins.show', ['admin' => $location_admin]);
    }

    public function edit(User $location_admin)
    {
        $this->authorizeSuperAdmin();
        if (!$location_admin->hasRole('Admin Lokasi')) { abort(404); }

        $locations = Location::active()->orderBy('name')->get();
        return view('location-admins.edit', ['admin' => $location_admin, 'locations' => $locations]);
    }

    public function update(Request $request, User $location_admin)
    {
        $this->authorizeSuperAdmin();
        if (!$location_admin->hasRole('Admin Lokasi')) { abort(404); }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $location_admin->id,
            'password' => 'nullable|string|min:8|confirmed',
            'location_id' => 'required|exists:locations,id',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'location_id' => $request->location_id,
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $location_admin->update($data);
        if (!$location_admin->hasRole('Admin Lokasi')) {
            $location_admin->assignRole('Admin Lokasi');
        }

        return redirect()->route('location-admins.index')->with('success', 'Location Admin updated successfully.');
    }

    public function destroy(User $location_admin)
    {
        $this->authorizeSuperAdmin();
        if (!$location_admin->hasRole('Admin Lokasi')) { abort(404); }

        $location_admin->delete();
        return redirect()->route('location-admins.index')->with('success', 'Location Admin deleted successfully.');
    }

    private function authorizeSuperAdmin(): void
    {
        if (!auth()->check() || !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized');
        }
    }
}

