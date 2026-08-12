<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    /** The complaint queue, newest first, optionally filtered by status. */
    public function index(Request $request): View
    {
        $complaints = Complaint::with(['complainant', 'against', 'order'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.complaints.index', [
            'complaints' => $complaints,
            'filters' => $request->only('status'),
        ]);
    }

    /** Update a complaint's status and optional resolution note. */
    public function update(Request $request, Complaint $complaint): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['open', 'under_review', 'resolved', 'dismissed'])],
            'resolution' => ['nullable', 'string', 'max:2000'],
        ]);

        $isClosed = in_array($validated['status'], ['resolved', 'dismissed'], true);

        $complaint->update([
            'status' => $validated['status'],
            'resolution' => $validated['resolution'] ?? $complaint->resolution,
            'resolved_by' => $isClosed ? $request->user()->id : null,
            'resolved_at' => $isClosed ? now() : null,
        ]);

        return back()->with('status', 'Complaint updated.');
    }
}
