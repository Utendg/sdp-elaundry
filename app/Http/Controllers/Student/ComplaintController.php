<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    /** List complaints the student has filed. */
    public function index(Request $request): View
    {
        $complaints = Complaint::where('complainant_id', $request->user()->id)
            ->with('order', 'against')
            ->latest()
            ->paginate(10);

        return view('student.complaints.index', ['complaints' => $complaints]);
    }

    /** File a complaint, optionally tied to one of the student's orders. */
    public function store(Request $request): RedirectResponse
    {
        $student = $request->user();

        $validated = $request->validate([
            'order_id' => [
                'nullable',
                Rule::exists('orders', 'id')->where('student_id', $student->id),
            ],
            'type' => ['required', Rule::in(['damaged', 'missing', 'delayed', 'pricing', 'conduct', 'other'])],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        // If tied to an order, target that order's worker automatically.
        $againstId = null;
        if (! empty($validated['order_id'])) {
            $againstId = Order::where('id', $validated['order_id'])->value('worker_id');
        }

        Complaint::create([
            'order_id' => $validated['order_id'] ?? null,
            'complainant_id' => $student->id,
            'against_id' => $againstId,
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);

        return back()->with('status', 'Your complaint has been submitted to the administrators.');
    }
}
