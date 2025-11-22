<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $auth = Auth::user();
        $q = Holiday::query();
        // Scope by role
        if ($auth->hasRole('Admin Lokasi')) {
            $q->where(function($qq) use ($auth){
                $qq->where('is_national', true)->orWhere('location_id', $auth->location_id);
            });
        }
        // Filters
        if ($request->filled('type') && in_array($request->type, ['national','local'])) {
            if ($request->type === 'national') {
                $q->where('is_national', true);
            } else {
                $q->where('is_national', false);
                if ($auth->hasRole('Super Admin') && $request->filled('location_id')) {
                    $q->where('location_id', $request->location_id);
                }
            }
        } elseif ($auth->hasRole('Super Admin') && $request->filled('location_id')) {
            // filter lokasi untuk super admin tanpa membatasi nasional
            $q->where(function($qq) use ($request){
                $qq->where('is_national', true)->orWhere(function($qqq) use ($request){ $qqq->where('is_national', false)->where('location_id', $request->location_id); });
            });
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $q->whereBetween('date', [$request->date_from, $request->date_to]);
        } elseif ($request->filled('date_from')) {
            $q->whereDate('date', '>=', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $q->whereDate('date', '<=', $request->date_to);
        }

        $holidays = $q->orderBy('date','asc')->paginate(10)->withQueryString();
        return view('holidays.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $auth = Auth::user();
        $request->validate([
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            // Checkbox HTML bisa mengirim 'on', tidak cocok dengan rule 'boolean'.
            // Biarkan nullable; kita konversi manual via $request->boolean('is_national').
            'is_national' => 'nullable',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        $data = $request->only('date','name');
        $isNational = $request->boolean('is_national');
        if ($auth->hasRole('Admin Lokasi')) {
            // Admin lokasi hanya boleh buat untuk lokasinya (non nasional)
            $data['is_national'] = false;
            $data['location_id'] = $auth->location_id;
        } else {
            // Super Admin: jika non-nasional, pastikan lokasi dipilih
            if (!$isNational && !$request->filled('location_id')) {
                return back()->withErrors(['location_id' => 'Pilih lokasi untuk libur non-nasional.'])->withInput();
            }
            $data['is_national'] = $isNational;
            $data['location_id'] = $isNational ? null : $request->location_id;
        }
        $data['is_active'] = true;

        // Cegah duplikasi
        $exists = Holiday::whereDate('date', $data['date'])
            ->where(function($q) use ($data) {
                if ($data['is_national']) {
                    $q->where('is_national', true);
                } else {
                    $q->where('is_national', false)->where('location_id', $data['location_id']);
                }
            })->exists();
        if ($exists) {
            return back()->withErrors(['date' => 'Holiday pada tanggal tersebut sudah ada.'])->withInput();
        }

        $holiday = Holiday::create($data);

        // Kirim pemberitahuan ke user terkait (pesan langsung)
        try {
            $messageText = $holiday->is_national
                ? ('Pemberitahuan: Libur Nasional pada ' . $holiday->date->format('Y-m-d') . ' - ' . $holiday->name)
                : ('Pemberitahuan: Libur Lokasi pada ' . $holiday->date->format('Y-m-d') . ' - ' . $holiday->name);

            if ($holiday->is_national) {
                // Semua user
                \App\Models\User::query()->chunk(500, function($users) use ($auth, $messageText) {
                    foreach ($users as $u) {
                        \App\Models\Message::create([
                            'sender_id' => $auth->id,
                            'receiver_id' => $u->id,
                            'message' => $messageText,
                        ]);
                    }
                });
            } else {
                // Hanya user di lokasi tersebut
                \App\Models\User::where('location_id', $holiday->location_id)->chunk(500, function($users) use ($auth, $messageText) {
                    foreach ($users as $u) {
                        \App\Models\Message::create([
                            'sender_id' => $auth->id,
                            'receiver_id' => $u->id,
                            'message' => $messageText,
                        ]);
                    }
                });
            }
        } catch (\Throwable $e) {
            // Diamkan agar tidak menggagalkan penyimpanan libur
        }

        return back()->with('success','Holiday saved dan notifikasi dikirim.');
    }

    public function destroy(Holiday $holiday)
    {
        $auth = Auth::user();
        if ($auth->hasRole('Admin Lokasi')) {
            abort_unless(!$holiday->is_national && $holiday->location_id === $auth->location_id, 403);
        }
        $holiday->delete();
        return back()->with('success','Holiday deleted');
    }
}
