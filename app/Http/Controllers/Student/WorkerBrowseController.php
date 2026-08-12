<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkerBrowseController extends Controller
{
    /**
     * List approved, available laundry workers — those in the student's dorm first.
     */
    public function index(Request $request): View
    {
        $student = $request->user();

        $query = User::where('role', User::ROLE_WORKER)
            ->where('is_active', true)
            ->whereHas('workerProfile', fn ($q) => $q->where('is_approved', true))
            ->with(['workerProfile', 'dorm']);

        // Optional filter: only my dorm.
        $sameDormOnly = $request->boolean('my_dorm');
        if ($sameDormOnly && $student->dorm_id) {
            $query->where('dorm_id', $student->dorm_id);
        }

        $workers = $query
            ->withCount(['ordersAsWorker as completed_orders_count' => fn ($q) => $q->where('status', 'completed')])
            ->get()
            ->sortByDesc(fn ($w) => [
                $w->dorm_id === $student->dorm_id ? 1 : 0, // same-dorm workers first
                (float) ($w->workerProfile->rating_avg ?? 0),
            ])
            ->values();

        return view('student.workers.index', [
            'workers' => $workers,
            'sameDormOnly' => $sameDormOnly,
        ]);
    }

    /**
     * Show a single worker's public profile and recent reviews.
     */
    public function show(Request $request, User $worker): View
    {
        abort_unless($worker->isWorker(), 404);

        $worker->load([
            'workerProfile',
            'dorm',
            'ratingsReceived' => fn ($q) => $q->where('direction', 'student_to_worker')->latest()->with('rater')->take(10),
        ]);

        return view('student.workers.show', [
            'worker' => $worker,
        ]);
    }
}
