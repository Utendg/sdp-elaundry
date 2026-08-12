<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkerProfile;
use App\Notifications\WorkerApproved;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkerController extends Controller
{
    /** List workers, separating those awaiting approval from approved ones. */
    public function index(): View
    {
        $workers = User::where('role', User::ROLE_WORKER)
            ->with(['workerProfile', 'dorm'])
            ->orderBy('name')
            ->get();

        return view('admin.workers.index', [
            'pending' => $workers->filter(fn ($w) => ! ($w->workerProfile?->is_approved))->values(),
            'approved' => $workers->filter(fn ($w) => $w->workerProfile?->is_approved)->values(),
        ]);
    }

    /** Approve a worker so they can start receiving orders. */
    public function approve(Request $request, User $worker): RedirectResponse
    {
        abort_unless($worker->isWorker(), 404);

        $profile = $worker->workerProfile
            ?? WorkerProfile::create(['user_id' => $worker->id, 'is_approved' => false]);

        $profile->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
        ]);

        $worker->notify(new WorkerApproved());

        return back()->with('status', "{$worker->name} approved.");
    }

    /** Revoke a worker's approval. */
    public function revoke(User $worker): RedirectResponse
    {
        abort_unless($worker->isWorker(), 404);

        $worker->workerProfile?->update([
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return back()->with('status', "{$worker->name}'s approval revoked.");
    }
}
