<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function search(Request $request)
    {
        $user = $request->user();

        // Only Super Admin can search users
        if (!$user->hasRole('Super Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'q' => 'required|string|min:2|max:50',
            'page' => 'nullable|integer|min:1'
        ]);

        $query = User::select('id', 'name', 'email', 'location_id')
                    ->with('location:id,name')
                    ->where(function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->q . '%')
                          ->orWhere('email', 'like', '%' . $request->q . '%');
                    })
                    ->whereHas('roles', function ($q) {
                        $q->whereIn('name', ['Karyawan', 'Admin Lokasi']);
                    })
                    ->orderBy('name');

        $perPage = 20;
        $page = $request->page ?? 1;

        $results = $query->paginate($perPage, ['*'], 'page', $page);

        // Format results for Select2
        $formattedResults = $results->getCollection()->map(function ($user) {
            return [
                'id' => $user->id,
                'text' => $user->name . ' (' . $user->email . ') - ' . ($user->location->name ?? 'No Location')
            ];
        });

        return response()->json([
            'results' => $formattedResults,
            'pagination' => [
                'more' => $results->hasMorePages()
            ]
        ]);
    }
}
