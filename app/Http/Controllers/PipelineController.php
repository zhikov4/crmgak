<?php

namespace App\Http\Controllers;

use App\Models\Pipeline;
use App\Models\Lead;
use Illuminate\Http\Request;

class PipelineController extends Controller
{
    public function index()
    {
        $stages = [
            'new'         => 'New',
            'contacted'   => 'Contacted',
            'survey'      => 'Survey',
            'proposal'    => 'Proposal',
            'negotiation' => 'Negotiation',
            'won'         => 'Won',
            'lost'        => 'Lost',
        ];

        $pipelines = Pipeline::with('lead', 'assignedTo')
            ->orderBy('order')
            ->get()
            ->groupBy('stage');

        return view('pipeline.index', compact('stages', 'pipelines'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id'             => 'required|exists:leads,id',
            'stage'               => 'required|in:new,contacted,survey,proposal,negotiation,won,lost',
            'value'               => 'nullable|numeric|min:0',
            'expected_close_date' => 'nullable|date',
            'notes'               => 'nullable|string',
        ]);

        $validated['assigned_to'] = auth()->id();
        $validated['order'] = Pipeline::where('stage', $validated['stage'])->count();

        Pipeline::create($validated);

        return redirect()->route('pipeline.index')
            ->with('success', 'Deal berhasil ditambahkan ke pipeline!');
    }

    public function updateStage(Request $request, Pipeline $pipeline)
    {
        $request->validate([
            'stage' => 'required|in:new,contacted,survey,proposal,negotiation,won,lost',
        ]);

        $pipeline->update(['stage' => $request->stage]);

        return response()->json(['success' => true]);
    }

    public function destroy(Pipeline $pipeline)
    {
        $pipeline->delete();

        return redirect()->route('pipeline.index')
            ->with('success', 'Deal dihapus dari pipeline!');
    }
}