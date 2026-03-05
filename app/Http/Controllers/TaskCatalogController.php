<?php

namespace App\Http\Controllers;

use App\Models\Jobdesk;
use App\Models\TaskCatalog;
use Illuminate\Http\Request;

class TaskCatalogController extends Controller
{
    public function index(Jobdesk $jobdesk)
    {
        $catalogs = $jobdesk->taskCatalogs()->orderBy('name')->paginate(20);
        return view('task-catalogs.index', compact('jobdesk', 'catalogs'));
    }

    public function create(Jobdesk $jobdesk)
    {
        return view('task-catalogs.create', compact('jobdesk'));
    }

    public function store(Request $request, Jobdesk $jobdesk)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|in:minutes,points,weight',
            'value' => 'required|integer|min:1',
            'task_type' => 'required|in:routine,project',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['jobdesk_id'] = $jobdesk->id;

        TaskCatalog::create($data);

        return redirect()->route('jobdesks.catalogs.index', $jobdesk)->with('success', 'Task catalog created successfully.');
    }

    public function edit(Jobdesk $jobdesk, TaskCatalog $catalog)
    {
        if ($catalog->jobdesk_id !== $jobdesk->id) {
            abort(404);
        }
        return view('task-catalogs.edit', compact('jobdesk', 'catalog'));
    }

    public function update(Request $request, Jobdesk $jobdesk, TaskCatalog $catalog)
    {
        if ($catalog->jobdesk_id !== $jobdesk->id) {
            abort(404);
        }
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|in:minutes,points,weight',
            'value' => 'required|integer|min:1',
            'task_type' => 'required|in:routine,project',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $catalog->update($data);

        return redirect()->route('jobdesks.catalogs.index', $jobdesk)->with('success', 'Task catalog updated successfully.');
    }

    public function destroy(Jobdesk $jobdesk, TaskCatalog $catalog)
    {
        if ($catalog->jobdesk_id !== $jobdesk->id) {
            abort(404);
        }
        $catalog->delete();

        return redirect()->route('jobdesks.catalogs.index', $jobdesk)->with('success', 'Task catalog deleted successfully.');
    }
}
