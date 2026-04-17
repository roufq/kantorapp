<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Location;
use App\Models\LocationWorkTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationWorkTargetController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = LocationWorkTarget::with(['location', 'employee'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        if ($user->hasRole('Super Admin')) {
            if ($request->filled('location_id')) {
                $query->where('location_id', $request->location_id);
            }
        } elseif ($user->hasRole('Location Admin')) {
            $query->where('location_id', $user->location_id);
        } else {
            abort(403);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('month')) {
            [$year, $month] = array_pad(explode('-', $request->month), 2, null);
            $query->where('year', (int) $year)->where('month', (int) $month);
        }

        $targets = $query->paginate(20)->withQueryString();

        $locations = $user->hasRole('Super Admin')
            ? Location::all()
            : Location::where('id', $user->location_id)->get();

        $employees = Employee::when(!$user->hasRole('Super Admin'), function ($q) use ($user) {
            $q->where('location_id', $user->location_id);
        })->orderBy('nama')->get();

        $monthParam = $request->get('month', now()->format('Y-m'));

        return view('work-targets.index', compact('targets', 'locations', 'employees', 'monthParam'));
    }

    public function create()
    {
        $user = Auth::user();
        $monthParam = now()->format('Y-m');

        $locations = $user->hasRole('Super Admin')
            ? Location::all()
            : Location::where('id', $user->location_id)->get();

        $employees = Employee::when(!$user->hasRole('Super Admin'), function ($q) use ($user) {
            $q->where('location_id', $user->location_id);
        })->orderBy('nama')->get();

        return view('work-targets.create', compact('locations', 'employees', 'monthParam'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'location_id' => 'nullable|exists:locations,id',
            'employee_id' => 'nullable|exists:employees,id',
            'month' => 'required|date_format:Y-m',
            'target_minutes' => 'required|integer|min:0',
        ]);

        [$year, $month] = explode('-', $data['month']);
        $locationId = $user->hasRole('Location Admin') ? $user->location_id : ($data['location_id'] ?? null);

        $exists = LocationWorkTarget::where('location_id', $locationId)
            ->where('employee_id', $data['employee_id'])
            ->where('year', (int) $year)
            ->where('month', (int) $month)
            ->exists();
        if ($exists) {
            return back()->withErrors(['month' => 'Target untuk kombinasi lokasi/employee/bulan sudah ada.'])->withInput();
        }

        LocationWorkTarget::create([
            'location_id' => $locationId,
            'employee_id' => $data['employee_id'],
            'year' => (int) $year,
            'month' => (int) $month,
            'target_minutes' => (int) $data['target_minutes'],
        ]);

        return redirect()->route('work-targets.index')->with('success', 'Target jam kerja berhasil dibuat.');
    }

    public function edit(LocationWorkTarget $work_target)
    {
        $user = Auth::user();
        if ($user->hasRole('Location Admin') && $work_target->location_id !== $user->location_id) {
            abort(403);
        }

        $monthParam = sprintf('%04d-%02d', $work_target->year, $work_target->month);

        $locations = $user->hasRole('Super Admin')
            ? Location::all()
            : Location::where('id', $user->location_id)->get();

        $employees = Employee::when(!$user->hasRole('Super Admin'), function ($q) use ($user) {
            $q->where('location_id', $user->location_id);
        })->orderBy('nama')->get();

        return view('work-targets.edit', [
            'target' => $work_target,
            'locations' => $locations,
            'employees' => $employees,
            'monthParam' => $monthParam,
        ]);
    }

    public function update(Request $request, LocationWorkTarget $work_target)
    {
        $user = Auth::user();
        if ($user->hasRole('Location Admin') && $work_target->location_id !== $user->location_id) {
            abort(403);
        }

        $data = $request->validate([
            'location_id' => 'nullable|exists:locations,id',
            'employee_id' => 'nullable|exists:employees,id',
            'month' => 'required|date_format:Y-m',
            'target_minutes' => 'required|integer|min:0',
        ]);

        [$year, $month] = explode('-', $data['month']);
        $locationId = $user->hasRole('Location Admin') ? $user->location_id : ($data['location_id'] ?? null);

        $exists = LocationWorkTarget::where('location_id', $locationId)
            ->where('employee_id', $data['employee_id'])
            ->where('year', (int) $year)
            ->where('month', (int) $month)
            ->where('id', '!=', $work_target->id)
            ->exists();
        if ($exists) {
            return back()->withErrors(['month' => 'Target untuk kombinasi lokasi/employee/bulan sudah ada.'])->withInput();
        }

        $work_target->update([
            'location_id' => $locationId,
            'employee_id' => $data['employee_id'],
            'year' => (int) $year,
            'month' => (int) $month,
            'target_minutes' => (int) $data['target_minutes'],
        ]);

        return redirect()->route('work-targets.index')->with('success', 'Target jam kerja diperbarui.');
    }

    public function destroy(LocationWorkTarget $work_target)
    {
        $user = Auth::user();
        if ($user->hasRole('Location Admin') && $work_target->location_id !== $user->location_id) {
            abort(403);
        }

        $work_target->delete();

        return redirect()->route('work-targets.index')->with('success', 'Target jam kerja dihapus.');
    }
}
