<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorkerApproved
{
    /**
     * Block worker actions until an admin has approved the worker's profile.
     * Read-only pages remain accessible so the worker can see their status.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isWorker() && ! ($user->workerProfile?->is_approved)) {
            return redirect()
                ->route('dashboard')
                ->withErrors(['approval' => 'Your worker account is awaiting admin approval.']);
        }

        return $next($request);
    }
}
