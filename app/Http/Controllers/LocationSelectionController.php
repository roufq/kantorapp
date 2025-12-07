<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationSelectionController extends Controller
{
    public function index()
    {
        $this->authorizeSuperAdmin();
        $locations = Location::active()->orderBy('name')->get();
        $current = session('location_id');

        return view('locations.select', compact('locations', 'current'));
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();
        $request->validate([
            'location_id' => 'required|exists:locations,id',
        ]);

        session(['location_id' => $request->location_id]);

        return redirect()->back()->with('success', 'Lokasi aktif diperbarui.');
    }

    private function authorizeSuperAdmin(): void
    {
        if (!Auth::user()?->hasRole('Super Admin')) {
            abort(403);
        }
    }
}
