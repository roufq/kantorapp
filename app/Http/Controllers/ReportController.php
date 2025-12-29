<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportApproval;
use App\Models\ReportAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Services\AuditLogger;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            $query = Report::with(['reporter', 'assignedAdmin', 'approvals.approver']);
        } elseif ($user->hasRole('Admin Lokasi')) {
            $locId = $user->location_id;
            $query = Report::where(function ($q) use ($user, $locId) {
                $q->where('location_id', $locId)
                    ->orWhere('assigned_admin_id', $user->id);
            })->with(['reporter', 'assignedAdmin', 'approvals.approver']);
        } else {
            $query = Report::where('reporter_id', $user->id)->with(['reporter', 'assignedAdmin', 'approvals.approver']);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('ticket_number', 'like', '%' . $term . '%')
                    ->orWhere('title', 'like', '%' . $term . '%')
                    ->orWhere('description', 'like', '%' . $term . '%');
            });
        }

        $reports = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        $user = Auth::user();
        abort_unless($user->hasRole(['Super Admin', 'Admin Lokasi', 'Karyawan']), 403);

        $locations = collect();
        if ($user->hasRole('Super Admin')) {
            $locations = \App\Models\Location::orderBy('name')->get();
        }

        return view('reports.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        abort_unless($user->hasRole(['Super Admin', 'Admin Lokasi', 'Karyawan']), 403, 'Tidak diizinkan membuat laporan.');

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location_id' => 'nullable|exists:locations,id',
            'attachments.*' => 'file|max:10240', // 10 MB per file
        ];

        if ($user->hasRole('Karyawan') || $user->hasRole('Admin Lokasi')) {
            // force to their location
            $request->merge(['location_id' => $user->location_id]);
        }

        $validated = $request->validate($rules);

        $locationId = $validated['location_id'] ?? $user->location_id;
        $assignedAdmin = null;
        if ($locationId) {
            $assignedAdmin = User::where('location_id', $locationId)
                ->role('Admin Lokasi')
                ->first();
        }

        $report = Report::create([
            'ticket_number' => Report::generateTicketNumber(),
            'reporter_id' => $user->id,
            'location_id' => $locationId,
            'assigned_admin_id' => $assignedAdmin?->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        // Build approval chain
        $step = 1;
        if ($user->hasRole('Karyawan') && $assignedAdmin) {
            ReportApproval::create([
                'report_id' => $report->id,
                'approver_id' => $assignedAdmin->id,
                'approver_role' => 'admin_lokasi',
                'step_order' => $step++,
            ]);
        }

        // Super Admin approval always required
        ReportApproval::create([
            'report_id' => $report->id,
            'approver_id' => null,
            'approver_role' => 'super_admin',
            'step_order' => $step,
        ]);

        // Attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('reports', 'public');
                ReportAttachment::create([
                    'report_id' => $report->id,
                    'uploaded_by' => $user->id,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('reports.show', $report)->with('success', 'Laporan berhasil dibuat.');
    }

    public function show(Report $report)
    {
        $this->authorizeView($report);

        $report->load(['attachments', 'approvals.approver', 'reporter', 'assignedAdmin']);
        $pendingApproval = $report->currentPendingApproval();

        return view('reports.show', compact('report', 'pendingApproval'));
    }

    public function edit(Report $report)
    {
        $this->authorizeManage($report);

        if ($report->status !== 'pending') {
            return redirect()->route('reports.show', $report)->withErrors('Laporan hanya bisa diedit saat status pending.');
        }

        return view('reports.edit', compact('report'));
    }

    public function update(Request $request, Report $report)
    {
        $this->authorizeManage($report);

        if ($report->status !== 'pending') {
            return redirect()->route('reports.show', $report)->withErrors('Laporan hanya bisa diedit saat status pending.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'attachments.*' => 'file|max:10240',
        ]);

        $report->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('reports', 'public');
                ReportAttachment::create([
                    'report_id' => $report->id,
                    'uploaded_by' => Auth::id(),
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('reports.show', $report)->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(Report $report)
    {
        $this->authorizeManage($report);

        if ($report->status !== 'pending') {
            return redirect()->route('reports.show', $report)->withErrors('Laporan hanya bisa dihapus saat status pending.');
        }

        $report->delete();

        return redirect()->route('reports.index')->with('success', 'Laporan dihapus.');
    }

    public function approve(Request $request, Report $report)
    {
        $user = Auth::user();
        $this->authorizeApprove($report, $user);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'notes' => 'nullable|string|max:1000',
        ]);

        $role = $user->hasRole('Admin Lokasi') ? 'admin_lokasi' : 'super_admin';
        $approval = ReportApproval::where('report_id', $report->id)
            ->where('approver_role', $role)
            ->where('status', 'pending')
            ->orderBy('step_order')
            ->firstOrFail();

        if ($role === 'admin_lokasi' && $approval->approver_id && (int)$approval->approver_id !== (int)$user->id) {
            abort(403, 'Anda bukan approver untuk laporan ini.');
        }

        $before = $approval->only(['status', 'notes', 'decided_at', 'approver_id']);
        $approval->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'decided_at' => now(),
            'approver_id' => $approval->approver_id ?: $user->id,
        ]);

        $report->recalcStatus();
        AuditLogger::record('report_approval_updated', $approval, $before, [
            'status' => $approval->status,
            'notes' => $approval->notes,
            'decided_at' => $approval->decided_at,
            'approver_id' => $approval->approver_id,
        ], [
            'report_id' => $report->id,
        ]);

        return redirect()->route('reports.show', $report)->with('success', 'Status laporan diperbarui.');
    }

    public function downloadAttachment(ReportAttachment $attachment)
    {
        $report = $attachment->report;
        $this->authorizeView($report);

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->original_name);
    }

    private function authorizeView(Report $report): void
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            return;
        }

        if ($user->hasRole('Admin Lokasi')) {
            if ($report->location_id && (int)$report->location_id === (int)$user->location_id) {
                return;
            }
            if ($report->assigned_admin_id && (int)$report->assigned_admin_id === (int)$user->id) {
                return;
            }
        }

        if ($report->reporter_id === $user->id) {
            return;
        }

        abort(403, 'Tidak diizinkan mengakses laporan ini.');
    }

    private function authorizeManage(Report $report): void
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            return;
        }

        if ($user->hasRole('Admin Lokasi')) {
            if (($report->location_id && (int)$report->location_id === (int)$user->location_id) ||
                ($report->assigned_admin_id && (int)$report->assigned_admin_id === (int)$user->id)) {
                return;
            }
        }

        abort(403, 'Tidak diizinkan mengelola laporan ini.');
    }

    private function authorizeApprove(Report $report, $user): void
    {
        $pending = $report->currentPendingApproval();
        if (!$pending) {
            abort(400, 'Tidak ada persetujuan yang menunggu.');
        }

        if ($pending->approver_role === 'admin_lokasi') {
            abort_unless($user->hasRole('Admin Lokasi'), 403, 'Hanya Admin Lokasi yang dapat ACC tahap ini.');
            if ($pending->approver_id && (int)$pending->approver_id !== (int)$user->id) {
                abort(403, 'Anda bukan approver tahap ini.');
            }
        } elseif ($pending->approver_role === 'super_admin') {
            abort_unless($user->hasRole('Super Admin'), 403, 'Hanya Super Admin yang dapat ACC tahap ini.');
        }
    }
}
