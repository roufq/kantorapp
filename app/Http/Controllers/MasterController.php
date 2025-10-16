<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class MasterController extends Controller
{
    public function index()
    {
        $masters = User::where('role', 'master')->get();
        return view('masters.index', compact('masters'));
    }

    public function create()
    {
        return view('masters.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'master',
        ]);

        return redirect()->route('masters.index')->with('success', 'Master created successfully.');
    }

    public function show(User $master)
    {
        if ($master->role !== 'master') {
            abort(404);
        }
        return view('masters.show', compact('master'));
    }

    public function edit(User $master)
    {
        if ($master->role !== 'master') {
            abort(404);
        }
        return view('masters.edit', compact('master'));
    }

    public function update(Request $request, User $master)
    {
        if ($master->role !== 'master') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $master->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $master->update($updateData);

        return redirect()->route('masters.index')->with('success', 'Master updated successfully.');
    }

    public function destroy(User $master)
    {
        if ($master->role !== 'master') {
            abort(404);
        }

        $master->delete();

        return redirect()->route('masters.index')->with('success', 'Master deleted successfully.');
    }
}
