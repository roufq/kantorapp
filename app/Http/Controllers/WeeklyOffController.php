<?php

namespace App\Http\Controllers;

use App\Models\WeeklyOff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeeklyOffController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $auth = Auth::user();
        $q = WeeklyOff::query();
        if ($auth->hasRole('Location Admin')) {
            $q->where('location_id', $auth->location_id);
        }
        if ($request->filled('day_of_week')) {
            $q->where('day_of_week', $request->day_of_week);
        }
        $offs = $q->orderBy('day_of_week')->paginate(10)->withQueryString();
        return view('weekly-offs.index', compact('offs'));
    }

    public function store(Request $request)
    {
        $auth = Auth::user();
        $request->validate([
            'day_of_week' => 'required|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'user_id' => 'nullable|exists:users,id',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        $data = $request->only('day_of_week','user_id','location_id');
        if ($auth->hasRole('Location Admin')) {
            // Admin lokasi hanya boleh set untuk lokasi sendiri atau user di lokasi sendiri
            $data['location_id'] = $auth->location_id;
            if (!empty($data['user_id'])) {
                $target = \App\Models\User::find($data['user_id']);
                abort_unless($target && (int)$target->location_id === (int)$auth->location_id, 403, 'User bukan dari lokasi Anda');
            }
        } elseif ($auth->hasRole('Super Admin')) {
            // Jika super admin set personal off dan lokasi diisi, user (jika ada) wajib match lokasi
            if (!empty($data['user_id']) && !empty($data['location_id'])) {
                $target = \App\Models\User::find($data['user_id']);
                abort_unless($target && (int)$target->location_id === (int)$data['location_id'], 422, 'User tidak sesuai dengan lokasi yang dipilih');
            }
        }
        WeeklyOff::firstOrCreate($data);
        return back()->with('success','Weekly off saved');
    }

    public function destroy(WeeklyOff $weeklyOff)
    {
        $auth = Auth::user();
        if ($auth->hasRole('Location Admin')) {
            abort_unless($weeklyOff->location_id === $auth->location_id, 403);
        }
        $weeklyOff->delete();
        return back()->with('success','Weekly off deleted');
    }
}
