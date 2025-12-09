<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeWorkRecap;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkRecapController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $monthParam = $request->get('month', now()->format('Y-m'));
        [$year, $month] = array_pad(explode('-', $monthParam), 2, null);
        $year = (int) $year;
        $month = (int) $month;

        $query = EmployeeWorkRecap::with(['employee', 'location'])
            ->where('year', $year)
            ->where('month', $month);

        // Role filter
        if ($user->hasRole('Super Admin')) {
            if ($request->filled('location_id')) {
                $query->where('location_id', $request->location_id);
            }
        } elseif ($user->hasRole('Admin Lokasi')) {
            $query->where('location_id', $user->location_id);
        } else {
            abort(403);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $recaps = $query->orderBy('employee_id')->paginate(20)->withQueryString();

        $locations = $user->hasRole('Super Admin')
            ? Location::all()
            : Location::where('id', $user->location_id)->get();

        $employees = Employee::when(!$user->hasRole('Super Admin'), function ($q) use ($user) {
            $q->where('location_id', $user->location_id);
        })->orderBy('nama')->get();

        return view('work-recaps.index', compact('recaps', 'locations', 'employees', 'monthParam'));
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

        return view('work-recaps.create', compact('locations', 'employees', 'monthParam'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'location_id' => 'nullable|exists:locations,id',
            'month' => 'required|date_format:Y-m',
            'slot_minutes_approved' => 'required|integer|min:0',
            'attendance_minutes' => 'required|integer|min:0',
        ]);

        [$year, $month] = explode('-', $data['month']);
        $locationId = $user->hasRole('Admin Lokasi') ? $user->location_id : ($data['location_id'] ?? null);

        $exists = EmployeeWorkRecap::where('employee_id', $data['employee_id'])
            ->where('location_id', $locationId)
            ->where('year', (int) $year)
            ->where('month', (int) $month)
            ->exists();

        if ($exists) {
            return back()->withErrors(['month' => 'Data rekap untuk karyawan, lokasi, dan bulan tersebut sudah ada.'])->withInput();
        }

        $total = (int) $data['slot_minutes_approved'] + (int) $data['attendance_minutes'];

        EmployeeWorkRecap::create([
            'employee_id' => $data['employee_id'],
            'location_id' => $locationId,
            'year' => (int) $year,
            'month' => (int) $month,
            'slot_minutes_approved' => (int) $data['slot_minutes_approved'],
            'attendance_minutes' => (int) $data['attendance_minutes'],
            'total_minutes' => $total,
        ]);

        return redirect()->route('work-recaps.index')->with('success', 'Rekap berhasil dibuat.');
    }

    public function edit(EmployeeWorkRecap $work_recap)
    {
        $user = Auth::user();
        if ($user->hasRole('Admin Lokasi') && $work_recap->location_id !== $user->location_id) {
            abort(403);
        }

        $monthParam = sprintf('%04d-%02d', $work_recap->year, $work_recap->month);

        $locations = $user->hasRole('Super Admin')
            ? Location::all()
            : Location::where('id', $user->location_id)->get();

        $employees = Employee::when(!$user->hasRole('Super Admin'), function ($q) use ($user) {
            $q->where('location_id', $user->location_id);
        })->orderBy('nama')->get();

        return view('work-recaps.edit', [
            'recap' => $work_recap,
            'locations' => $locations,
            'employees' => $employees,
            'monthParam' => $monthParam,
        ]);
    }

    public function update(Request $request, EmployeeWorkRecap $work_recap)
    {
        $user = Auth::user();
        if ($user->hasRole('Admin Lokasi') && $work_recap->location_id !== $user->location_id) {
            abort(403);
        }

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'location_id' => 'nullable|exists:locations,id',
            'month' => 'required|date_format:Y-m',
            'slot_minutes_approved' => 'required|integer|min:0',
            'attendance_minutes' => 'required|integer|min:0',
        ]);

        [$year, $month] = explode('-', $data['month']);
        $locationId = $user->hasRole('Admin Lokasi') ? $user->location_id : ($data['location_id'] ?? null);

        $exists = EmployeeWorkRecap::where('employee_id', $data['employee_id'])
            ->where('location_id', $locationId)
            ->where('year', (int) $year)
            ->where('month', (int) $month)
            ->where('id', '!=', $work_recap->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['month' => 'Data rekap untuk karyawan, lokasi, dan bulan tersebut sudah ada.'])->withInput();
        }

        $total = (int) $data['slot_minutes_approved'] + (int) $data['attendance_minutes'];

        $work_recap->update([
            'employee_id' => $data['employee_id'],
            'location_id' => $locationId,
            'year' => (int) $year,
            'month' => (int) $month,
            'slot_minutes_approved' => (int) $data['slot_minutes_approved'],
            'attendance_minutes' => (int) $data['attendance_minutes'],
            'total_minutes' => $total,
        ]);

        return redirect()->route('work-recaps.index')->with('success', 'Rekap diperbarui.');
    }

    public function destroy(EmployeeWorkRecap $work_recap)
    {
        $user = Auth::user();
        if ($user->hasRole('Admin Lokasi') && $work_recap->location_id !== $user->location_id) {
            abort(403);
        }

        $work_recap->delete();

        return redirect()->route('work-recaps.index')->with('success', 'Rekap dihapus.');
    }
}
