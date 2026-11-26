<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\AvatarController;
use App\Http\Controllers\Controller;
use App\Models\AdminPermission;
use App\Models\Dorm;
use App\Models\User;
use App\Models\WorkerProfile;
use App\Notifications\WorkerRegistered;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Only students and workers may self-register; admins are seeded/created internally.
        return view('auth.register', [
            'dorms' => Dorm::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', Rule::in([User::ROLE_STUDENT, User::ROLE_WORKER])],
            'phone' => ['nullable', 'string', 'max:30'],
            'dorm_id' => ['required', Rule::exists('dorms', 'id')],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Workers must show a real photo of themselves; students may add one if they like.
            'avatar' => AvatarController::rules($request->input('role') === User::ROLE_WORKER),
        ], [
            'avatar.required' => 'Laundry agents must upload a clear photo of their face.',
            'avatar.dimensions' => 'The photo must be at least 200 by 200 pixels.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'dorm_id' => $validated['dorm_id'],
            'password' => Hash::make($validated['password']),
        ]);

        if ($request->hasFile('avatar')) {
            $user->setAvatar($request->file('avatar'));
        }

        // Workers start unapproved and must be vetted by an admin before taking orders.
        if ($user->isWorker()) {
            WorkerProfile::create([
                'user_id' => $user->id,
                'is_approved' => false,
            ]);

            // Let every admin responsible for laundry workers know there is an application to review.
            $reviewers = User::where('role', User::ROLE_ADMIN)->where('is_active', true)->with('adminPermissions')->get()
                ->filter(fn (User $admin) => $admin->hasAdminPermission(AdminPermission::WORKERS));
            Notification::send($reviewers, new WorkerRegistered($user));
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
