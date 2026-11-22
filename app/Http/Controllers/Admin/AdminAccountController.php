<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminPermission;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * Head of Residence Life only: create admin accounts and decide which
 * responsibilities each admin has.
 */
class AdminAccountController extends Controller
{
    public function index(): View
    {
        return view('admin.admins.index', [
            'admins' => User::where('role', User::ROLE_ADMIN)->with('adminPermissions')->orderBy('name')->get(),
            'responsibilities' => AdminPermission::GRANTABLE,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'permissions' => ['array'],
            'permissions.*' => [Rule::in(array_keys(AdminPermission::GRANTABLE))],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $admin = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'role' => User::ROLE_ADMIN,
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(),
            ]);
            $this->syncPermissions($admin, $validated['permissions'] ?? [], $request->user());
        });

        return back()->with('status', "Admin account created for {$validated['name']}.");
    }

    /** Replace an admin's responsibilities. */
    public function update(Request $request, User $admin): RedirectResponse
    {
        $this->ensureManageable($request, $admin);

        $validated = $request->validate([
            'permissions' => ['array'],
            'permissions.*' => [Rule::in(array_keys(AdminPermission::GRANTABLE))],
        ]);

        $this->syncPermissions($admin, $validated['permissions'] ?? [], $request->user());

        return back()->with('status', "Responsibilities updated for {$admin->name}.");
    }

    /** Deactivate or reactivate an admin account. A deactivated admin cannot sign in. */
    public function toggle(Request $request, User $admin): RedirectResponse
    {
        $this->ensureManageable($request, $admin);

        $admin->update(['is_active' => ! $admin->is_active]);

        return back()->with('status', $admin->is_active
            ? "{$admin->name}'s account was reactivated."
            : "{$admin->name}'s account was deactivated.");
    }

    /** Only other, ordinary admins can be changed: never yourself or another Head of Residence Life. */
    private function ensureManageable(Request $request, User $admin): void
    {
        abort_unless($admin->isAdmin(), 404);
        abort_if($admin->is($request->user()) || $admin->isHeadAdmin(), 403, 'This admin account cannot be changed here.');
    }

    /** @param  array<int, string>  $permissions */
    private function syncPermissions(User $admin, array $permissions, User $grantedBy): void
    {
        $permissions = array_values(array_unique($permissions));

        $admin->adminPermissions()->whereNotIn('permission', $permissions)->delete();
        foreach ($permissions as $permission) {
            $admin->adminPermissions()->firstOrCreate(
                ['permission' => $permission],
                ['granted_by' => $grantedBy->id],
            );
        }
        $admin->unsetRelation('adminPermissions');
    }
}
