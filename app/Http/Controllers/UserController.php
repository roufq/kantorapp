<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;
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
        $employees = $query->paginate(20);
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
            'password' => ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'karyawan_id' => 'required|exists:employees,id',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        // Paksa Admin Lokasi hanya bisa membuat user untuk lokasinya sendiri,
        // dan hanya untuk karyawan yang berada di lokasi yang sama.
        if ($authUser->hasRole('Admin Lokasi')) {
            $targetLocation = $authUser->location_id; // override input lokasi
        } else {
            $targetLocation = $request->location_id;
        }
        Gate::authorize('create-user', $targetLocation);

        $karyawan = Employee::find($request->karyawan_id);
        if ($authUser->hasRole('Admin Lokasi') && (int) $karyawan->location_id !== (int) $authUser->location_id) {
            return back()->withErrors(['karyawan_id' => 'Karyawan yang dipilih bukan dari lokasi Anda.'])->withInput();
        }
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

        // Optionally send email verification only if enabled
        if (env('EMAIL_VERIFICATION_ENABLED', false)) {
            try {
                if (method_exists($user, 'sendEmailVerificationNotification')) {
                    $user->sendEmailVerificationNotification();
                }
            } catch (\Throwable $e) {
                // Silently ignore to avoid blocking creation; surfaced via logs
            }
        }

        return redirect()->route('users.index')->with('success', 'Employee user created successfully.');
    }

    public function show(User $user)
    {
        Gate::authorize('manage-user', $user);
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
            'password' => ['nullable', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'location_id' => 'nullable|exists:locations,id',
        ]);

        // Enforce location boundary for Admin Lokasi: they cannot change a user to a different location
        $authUser = Auth::user();
        if ($authUser->hasRole('Admin Lokasi') && $request->filled('location_id')) {
            if ((int)$request->location_id !== (int)$authUser->location_id) {
                abort(403, 'Unauthorized location change.');
            }
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'location_id' => $request->location_id,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);

            // Invalidate all sessions for this user to force re-login
            \Illuminate\Support\Facades\DB::table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)
                ->delete();
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

    /**
     * Show authenticated user's own profile.
     */
    public function profile()
    {
        $user = Auth::user()->load(['location', 'employee', 'karyawan']);
        $roles = $user->getRoleNames();
        $sessions = \Illuminate\Support\Facades\DB::table(config('session.table', 'sessions'))
            ->where('user_id', $user->id)
            ->latest('last_activity')
            ->get();

        return view('profile.show', compact('user', 'roles', 'sessions'));
    }

    public function updatePhoto(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $current = $user->profile_photo_path;
        if ($current && \Storage::disk('public')->exists($current)) {
            \Storage::disk('public')->delete($current);
        }

        $path = $request->file('photo')->store('avatars', 'public');
        $user->update(['profile_photo_path' => $path]);

        return redirect()->route('profile.show')->with('success', 'Foto profil diperbarui.');
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
        }
        
        // It is recommended to use logoutOtherDevices before password update
        Auth::logoutOtherDevices($request->current_password);

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return redirect()->route('profile.show')->with('success', 'Password updated successfully and other sessions have been logged out.');
    }

    public function logoutSession(Request $request, $sessionId)
    {
        if ($sessionId === $request->session()->getId()) {
            return back()->withErrors(['error' => "You cannot log out your current session."]);
        }

        \Illuminate\Support\Facades\DB::table(config('session.table', 'sessions'))
            ->where('id', $sessionId)
            ->where('user_id', Auth::user()->id) // ensure we can only delete our own sessions
            ->delete();

        return redirect()->route('profile.show')->with('success', 'Session logged out.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $page = $request->get('page', 1);
        $perPage = 10;

        $users = User::role('Karyawan')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->with('location')
            ->paginate($perPage, ['*'], 'page', $page);

        $results = $users->getCollection()->map(function ($user) {
            return [
                'id' => $user->id,
                'text' => $user->name . ' (' . $user->email . ') - ' . ($user->location ? $user->location->name : 'No Location'),
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $users->hasMorePages(),
            ],
        ]);
    }
}
