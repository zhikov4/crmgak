<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Lead;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('lead', 'assignedTo')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $leads = Lead::orderBy('name')->get();
        return view('projects.create', compact('leads'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'lead_id'     => 'nullable|exists:leads,id',
            'status'      => 'required|in:planning,in_progress,on_hold,completed,cancelled',
            'priority'    => 'required|in:low,medium,high',
            'value'       => 'nullable|numeric|min:0',
            'progress'    => 'nullable|numeric|min:0|max:100',
            'start_date'  => 'nullable|date',
            'due_date'    => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $validated['created_by']  = auth()->id();
        $validated['assigned_to'] = auth()->id();

        Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil ditambahkan!');
    }

    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $leads = Lead::orderBy('name')->get();
        return view('projects.edit', compact('project', 'leads'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'lead_id'     => 'nullable|exists:leads,id',
            'status'      => 'required|in:planning,in_progress,on_hold,completed,cancelled',
            'priority'    => 'required|in:low,medium,high',
            'value'       => 'nullable|numeric|min:0',
            'progress'    => 'nullable|numeric|min:0|max:100',
            'start_date'  => 'nullable|date',
            'due_date'    => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        if ($validated['status'] === 'completed' && !$project->completed_date) {
            $validated['completed_date'] = now();
        }

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil diupdate!');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil dihapus!');
    }
}