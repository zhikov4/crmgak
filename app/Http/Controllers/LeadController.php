<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::with('assignedTo')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('leads.index', compact('leads'));
    }

    public function create()
    {
        return view('leads.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'source'  => 'nullable|string|max:100',
            'status'  => 'required|in:new,contacted,qualified,proposal,negotiation,won,lost',
            'value'   => 'nullable|numeric|min:0',
            'notes'   => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'city'    => 'nullable|string|max:100',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['assigned_to'] = auth()->id();

        // Format wa_phone otomatis dari phone
        if (!empty($validated['phone'])) {
            $phone = preg_replace('/\D/', '', $validated['phone']);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }
            $validated['wa_phone'] = $phone;
        }

        Lead::create($validated);

        return redirect()->route('leads.index')
            ->with('success', 'Lead berhasil ditambahkan!');
    }

    public function show(Lead $lead)
    {
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        return view('leads.edit', compact('lead'));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'source'  => 'nullable|string|max:100',
            'status'  => 'required|in:new,contacted,qualified,proposal,negotiation,won,lost',
            'value'   => 'nullable|numeric|min:0',
            'notes'   => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'city'    => 'nullable|string|max:100',
        ]);

        if (!empty($validated['phone'])) {
            $phone = preg_replace('/\D/', '', $validated['phone']);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }
            $validated['wa_phone'] = $phone;
        }

        $lead->update($validated);

        return redirect()->route('leads.index')
            ->with('success', 'Lead berhasil diupdate!');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()->route('leads.index')
            ->with('success', 'Lead berhasil dihapus!');
    }
}