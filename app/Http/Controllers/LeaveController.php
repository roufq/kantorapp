<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLeave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index()
    {
        $auth = Auth::user();
        $q = EmployeeLeave::with(['user','approver']);
        if ($auth->hasRole('Admin Lokasi')) {
            $q->where('location_id', $auth->location_id);
        } elseif ($auth->hasRole('Karyawan')) {
            $q->where('user_id', $auth->id);
        }
        $leaves = $q->orderBy('start_date','desc')->paginate(15);
        return view('leaves.index', compact('leaves'));
    }

    public function store(Request $request)
    {
        $auth = Auth::user();
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:sick,annual,unpaid,other',
            'reason' => 'nullable|string|max:255',
        ]);
        // Cegah overlap dengan pengajuan/izin yang sudah ada (pending/approved)
        $overlap = EmployeeLeave::where('user_id', $auth->id)
            ->whereIn('status', ['pending','approved'])
            ->whereDate('start_date', '<=', $request->end_date)
            ->whereDate('end_date', '>=', $request->start_date)
            ->exists();
        if ($overlap) {
            return back()->withErrors(['start_date' => 'Rentang tanggal bertabrakan dengan pengajuan lain.'])->withInput();
        }

        EmployeeLeave::create([
            'user_id' => $auth->id,
            'location_id' => $auth->location_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);
        return back()->with('success', 'Leave request submitted');
    }

    public function updateStatus(Request $request, EmployeeLeave $leave)
    {
        $auth = Auth::user();
        abort_unless($auth->hasRole('Super Admin') || ($auth->hasRole('Admin Lokasi') && $auth->location_id === $leave->location_id), 403);
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);
        $leave->update([
            'status' => $request->status,
            'approved_by' => $request->status === 'approved' ? $auth->id : null,
        ]);
        // Kirim notifikasi ke karyawan terkait
        try {
            $msg = 'Status Izin/Cuti Anda (' . $leave->start_date->format('Y-m-d') . ' s/d ' . $leave->end_date->format('Y-m-d') . ', ' . $leave->type . ') berubah menjadi: ' . $request->status;
            \App\Models\Message::create([
                'sender_id' => $auth->id,
                'receiver_id' => $leave->user_id,
                'message' => $msg,
            ]);
        } catch (\Throwable $e) {}

        return back()->with('success','Leave status updated');
    }
}
