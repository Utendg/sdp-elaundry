<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Dorm;
use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'dorm_id' => $validated['dorm_id'],
            'password' => Hash::make($validated['password']),
        ]);

        // Workers start unapproved and must be vetted by an admin before taking orders.
        if ($user->isWorker()) {
            WorkerProfile::create([
                'user_id' => $user->id,
                'is_approved' => false,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
