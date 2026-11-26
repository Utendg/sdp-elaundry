<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkerProfile;
use App\Notifications\WorkerApproved;
use App\Notifications\WorkerDeclined;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkerController extends Controller
{
    /** List workers: awaiting approval, declined, and approved. */
    public function index(): View
    {
        $workers = User::where('role', User::ROLE_WORKER)
            ->with(['workerProfile.decliner', 'dorm'])
            ->orderBy('name')
            ->get();

        return view('admin.workers.index', [
            'pending' => $workers->filter(fn ($w) => $w->workerProfile?->isPending() ?? true)->values(),
            'declined' => $workers->filter(fn ($w) => $w->workerProfile?->isDeclined())->values(),
            'approved' => $workers->filter(fn ($w) => $w->workerProfile?->is_approved)->values(),
        ]);
    }

    /** Approve a worker so they can start receiving orders (also reverses a decline). */
    public function approve(Request $request, User $worker): RedirectResponse
    {
        abort_unless($worker->isWorker(), 404);

        // The photo is how students recognise the person collecting their clothes.
        if (! $worker->hasAvatar()) {
            return back()->withErrors(['approve' => "{$worker->name} has no profile photo yet. Ask them to upload one from their profile before approving."]);
        }

        $profile = $worker->workerProfile
            ?? WorkerProfile::create(['user_id' => $worker->id, 'is_approved' => false]);

        $profile->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
            'declined_at' => null,
            'declined_by' => null,
            'decline_reason' => null,
        ]);

        $worker->notify(new WorkerApproved());

        return back()->with('status', "{$worker->name} approved.");
    }

    /** Decline a pending application. The worker is told why and keeps no access to orders. */
    public function decline(Request $request, User $worker): RedirectResponse
    {
        abort_unless($worker->isWorker(), 404);

        $profile = $worker->workerProfile
            ?? WorkerProfile::create(['user_id' => $worker->id, 'is_approved' => false]);

        if ($profile->is_approved) {
            return back()->withErrors(['decline' => 'This agent is already approved. Revoke the approval instead.']);
        }

        $validated = $request->validate(['reason' => ['nullable', 'string', 'max:500']]);

        $profile->update([
            'declined_at' => now(),
            'declined_by' => $request->user()->id,
            'decline_reason' => $validated['reason'] ?? null,
        ]);

        $worker->notify(new WorkerDeclined($validated['reason'] ?? null));

        return back()->with('status', "{$worker->name}'s application was declined.");
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
