<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Lead;
use App\Models\Project;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with('createdBy', 'subject')
            ->orderBy('scheduled_at', 'desc')
            ->paginate(20);

        return view('activities.index', compact('activities'));
    }

    public function create()
    {
        $leads    = Lead::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        return view('activities.create', compact('leads', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'         => 'required|in:call,meeting,email,whatsapp,follow_up,note,status_change',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'subject_type' => 'required|in:lead,project',
            'subject_id'   => 'required|integer',
            'status'       => 'required|in:planned,done,cancelled',
            'scheduled_at' => 'nullable|date',
        ]);

        $validated['subject_type'] = $validated['subject_type'] === 'lead'
            ? Lead::class
            : Project::class;

        $validated['created_by'] = auth()->id();

        if ($validated['status'] === 'done') {
            $validated['completed_at'] = now();
        }

        Activity::create($validated);

        return redirect()->route('activities.index')
            ->with('success', 'Aktivitas berhasil ditambahkan!');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Aktivitas berhasil dihapus!');
    }

    public function markDone(Activity $activity)
    {
        $activity->update([
            'status'       => 'done',
            'completed_at' => now(),
        ]);

        return redirect()->route('activities.index')
            ->with('success', 'Aktivitas ditandai selesai!');
    }
}