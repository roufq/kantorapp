<?php

namespace App\Http\Controllers;

use App\Models\Jobdesk;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobdeskController extends Controller
{
    public function index(Request $request)
    {
        $query = Jobdesk::with(['location'])->orderBy('name');

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $jobdesks = $query->paginate(20)->withQueryString();
        $locations = Location::orderBy('name')->get();

        return view('jobdesks.index', compact('jobdesks', 'locations'));
    }

    public function create()
    {
        $locations = Location::orderBy('name')->get();
        return view('jobdesks.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'role_scope' => 'nullable|string|max:100',
            'location_id' => 'nullable|exists:locations,id',
            'is_active' => 'nullable|boolean',
            'min_attendance_minutes' => 'nullable|integer|min:0',
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['created_by'] = Auth::id();

        Jobdesk::create($data);

        return redirect()->route('jobdesks.index')->with('success', 'Jobdesk created successfully.');
    }

    public function show(Jobdesk $jobdesk)
    {
        $jobdesk->load(['location', 'taskCatalogs']);
        return view('jobdesks.show', compact('jobdesk'));
    }

    public function edit(Jobdesk $jobdesk)
    {
        $locations = Location::orderBy('name')->get();
        return view('jobdesks.edit', compact('jobdesk', 'locations'));
    }

    public function update(Request $request, Jobdesk $jobdesk)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'role_scope' => 'nullable|string|max:100',
            'location_id' => 'nullable|exists:locations,id',
            'is_active' => 'nullable|boolean',
            'min_attendance_minutes' => 'nullable|integer|min:0',
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $jobdesk->update($data);

        return redirect()->route('jobdesks.index')->with('success', 'Jobdesk updated successfully.');
    }

    public function destroy(Jobdesk $jobdesk)
    {
        $jobdesk->delete();

        return redirect()->route('jobdesks.index')->with('success', 'Jobdesk deleted successfully.');
    }
}
