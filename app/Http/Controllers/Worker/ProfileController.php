<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\WorkerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /** Show the worker's editable profile. */
    public function edit(Request $request): View
    {
        $profile = $request->user()->workerProfile
            ?? WorkerProfile::create(['user_id' => $request->user()->id, 'is_approved' => false]);

        return view('worker.profile.edit', ['profile' => $profile]);
    }

    /** Update bio and phone. */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bio' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $request->user()->update(['phone' => $validated['phone'] ?? null]);
        $request->user()->workerProfile->update(['bio' => $validated['bio'] ?? null]);

        return back()->with('status', 'Profile updated.');
    }

    /** Toggle whether the worker is currently accepting orders. */
    public function toggleAvailability(Request $request): RedirectResponse
    {
        $profile = $request->user()->workerProfile;
        $profile->update(['is_available' => ! $profile->is_available]);

        return back()->with('status', $profile->is_available
            ? 'You are now available for orders.'
            : 'You are now marked as busy.');
    }
}
