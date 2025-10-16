<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\MasterTask;
use App\Models\User;

class MasterTaskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        // Masters see only master tasks assigned to them
        $query = MasterTask::where('assigned_to', $user->id)->with('assignee', 'assigner');

        // Search by task title
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('title', 'like', '%' . $search . '%');
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('master-tasks.index', compact('tasks'));
    }

    public function create()
    {
        $users = User::whereIn('role', ['employee', 'master'])->get();

        return view('master-tasks.create', compact('users'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date|after:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt|max:5120',
        ]);

        $photoPath = null;
        $documentPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('master-tasks/photos', 'public');
        }

        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('master-tasks/documents', 'public');
        }

        MasterTask::create([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_by' => $user->id,
            'assigned_to' => $request->assigned_to,
            'due_date' => $request->due_date,
            'photo_path' => $photoPath,
            'document_path' => $documentPath,
        ]);

        return redirect()->route('master-tasks.index')->with('success', 'Master task created!');
    }

    public function show(MasterTask $masterTask)
    {
        $user = Auth::user();
        if ($masterTask->assigned_to !== $user->id) {
            abort(403);
        }

        return view('master-tasks.show', compact('masterTask'));
    }

    public function edit(MasterTask $masterTask)
    {
        $user = Auth::user();
        if ($masterTask->assigned_to !== $user->id) {
            abort(403);
        }

        $users = User::whereIn('role', ['employee', 'master'])->get();

        return view('master-tasks.edit', compact('masterTask', 'users'));
    }

    public function update(Request $request, MasterTask $masterTask)
    {
        $user = Auth::user();
        if ($masterTask->assigned_to !== $user->id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt|max:5120',
        ]);

        $photoPath = $masterTask->photo_path;
        $documentPath = $masterTask->document_path;

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('master-tasks/photos', 'public');
        }

        if ($request->hasFile('document')) {
            // Delete old document if exists
            if ($documentPath) {
                Storage::disk('public')->delete($documentPath);
            }
            $documentPath = $request->file('document')->store('master-tasks/documents', 'public');
        }

        $masterTask->update([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'photo_path' => $photoPath,
            'document_path' => $documentPath,
        ]);

        return redirect()->route('master-tasks.index')->with('success', 'Master task updated!');
    }

    public function destroy(MasterTask $masterTask)
    {
        $user = Auth::user();
        if ($masterTask->assigned_to !== $user->id) {
            abort(403);
        }

        // Delete associated files
        if ($masterTask->photo_path) {
            Storage::disk('public')->delete($masterTask->photo_path);
        }
        if ($masterTask->document_path) {
            Storage::disk('public')->delete($masterTask->document_path);
        }

        $masterTask->delete();

        return redirect()->route('master-tasks.index')->with('success', 'Master task deleted!');
    }

    public function downloadPhoto(MasterTask $masterTask)
    {
        $user = Auth::user();
        if ($masterTask->assigned_to !== $user->id) {
            abort(403);
        }

        if (!$masterTask->photo_path || !Storage::disk('public')->exists($masterTask->photo_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($masterTask->photo_path);
    }

    public function downloadDocument(MasterTask $masterTask)
    {
        $user = Auth::user();
        if ($masterTask->assigned_to !== $user->id) {
            abort(403);
        }

        if (!$masterTask->document_path || !Storage::disk('public')->exists($masterTask->document_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($masterTask->document_path);
    }

    public function createSelf()
    {
        return view('master-tasks.create-self');
    }

    public function storeSelf(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt|max:5120',
        ]);

        $photoPath = null;
        $documentPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('master-tasks/photos', 'public');
        }

        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('master-tasks/documents', 'public');
        }

        MasterTask::create([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_by' => $user->id,
            'assigned_to' => $user->id,
            'due_date' => $request->due_date,
            'photo_path' => $photoPath,
            'document_path' => $documentPath,
        ]);

        return redirect()->route('master-tasks.index')->with('success', 'Master task created for yourself!');
    }
}
