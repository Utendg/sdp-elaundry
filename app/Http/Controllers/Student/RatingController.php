<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Rating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    /** Student rates the worker after a completed order. */
    public function store(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->student_id === $request->user()->id, 403);

        if (! $order->isCompleted()) {
            return back()->withErrors(['rating' => 'You can only rate a completed order.']);
        }

        if (! $order->worker_id) {
            return back()->withErrors(['rating' => 'This order has no assigned worker to rate.']);
        }

        // One rating per direction per order.
        $already = Rating::where('order_id', $order->id)
            ->where('direction', Rating::STUDENT_TO_WORKER)
            ->exists();
        if ($already) {
            return back()->withErrors(['rating' => 'You have already rated this order.']);
        }

        $validated = $request->validate([
            'stars' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($order, $request, $validated) {
            Rating::create([
                'order_id' => $order->id,
                'rater_id' => $request->user()->id,
                'ratee_id' => $order->worker_id,
                'direction' => Rating::STUDENT_TO_WORKER,
                'stars' => $validated['stars'],
                'comment' => $validated['comment'] ?? null,
            ]);

            $this->recomputeWorkerRating($order->worker_id);
        });

        return back()->with('status', 'Thanks — your rating has been recorded.');
    }

    /** Recompute a worker's cached rating average and count. */
    private function recomputeWorkerRating(int $workerId): void
    {
        $stats = Rating::where('ratee_id', $workerId)
            ->where('direction', Rating::STUDENT_TO_WORKER)
            ->selectRaw('COUNT(*) as c, AVG(stars) as a')
            ->first();

        $profile = \App\Models\User::find($workerId)?->workerProfile;
        if ($profile) {
            $profile->update([
                'ratings_count' => (int) $stats->c,
                'rating_avg' => round((float) $stats->a, 2),
            ]);
        }
    }
}
