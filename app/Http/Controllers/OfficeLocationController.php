<?php

namespace App\Http\Controllers;

use App\Models\OfficeLocation;
use Illuminate\Http\Request;

class OfficeLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = OfficeLocation::all();
        return view('office-locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('office-locations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|numeric|min:1|max:10000',
        ]);

        OfficeLocation::create($request->all());

        return redirect()->route('office-locations.index')->with('success', 'Office location created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OfficeLocation $officeLocation)
    {
        return view('office-locations.show', compact('officeLocation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OfficeLocation $officeLocation)
    {
        return view('office-locations.edit', compact('officeLocation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OfficeLocation $officeLocation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|numeric|min:1|max:10000',
        ]);

        $officeLocation->update($request->all());

        return redirect()->route('office-locations.index')->with('success', 'Office location updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OfficeLocation $officeLocation)
    {
        $officeLocation->delete();

        return redirect()->route('office-locations.index')->with('success', 'Office location deleted successfully.');
    }
}
