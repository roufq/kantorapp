<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRule;
use Illuminate\Http\Request;

class ApprovalRuleController extends Controller
{
    public function index(Request $request)
    {
        $query = ApprovalRule::orderBy('scope')->orderByDesc('min_value');

        if ($request->filled('scope')) {
            $query->where('scope', $request->scope);
        }
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $rules = $query->paginate(20)->withQueryString();

        return view('approval-rules.index', compact('rules'));
    }

    public function create()
    {
        return view('approval-rules.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'scope' => 'required|string|max:50',
            'department' => 'nullable|string|max:255',
            'min_value' => 'required|integer|min:0',
            'approval_level' => 'required|in:location_admin,super_admin',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        ApprovalRule::create($data);

        return redirect()->route('approval-rules.index')->with('success', 'Approval rule berhasil dibuat.');
    }

    public function edit(ApprovalRule $approval_rule)
    {
        return view('approval-rules.edit', compact('approval_rule'));
    }

    public function update(Request $request, ApprovalRule $approval_rule)
    {
        $data = $request->validate([
            'scope' => 'required|string|max:50',
            'department' => 'nullable|string|max:255',
            'min_value' => 'required|integer|min:0',
            'approval_level' => 'required|in:location_admin,super_admin',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $approval_rule->update($data);

        return redirect()->route('approval-rules.index')->with('success', 'Approval rule diperbarui.');
    }

    public function destroy(ApprovalRule $approval_rule)
    {
        $approval_rule->delete();

        return redirect()->route('approval-rules.index')->with('success', 'Approval rule dihapus.');
    }
}
