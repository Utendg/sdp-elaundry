<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dorm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DormController extends Controller
{
    /** List all residence halls. */
    public function index(): View
    {
        return view('admin.dorms.index', [
            'dorms' => Dorm::withCount('users')->orderBy('name')->get(),
        ]);
    }

    /** Add a residence hall. */
    public function store(Request $request): RedirectResponse
    {
        Dorm::create($this->validated($request));

        return back()->with('status', 'Residence hall added.');
    }

    /** Update a residence hall. */
    public function update(Request $request, Dorm $dorm): RedirectResponse
    {
        $dorm->update($this->validated($request, $dorm));

        return back()->with('status', 'Residence hall updated.');
    }

    /** Toggle whether the hall is active. */
    public function toggle(Dorm $dorm): RedirectResponse
    {
        $dorm->update(['is_active' => ! $dorm->is_active]);

        return back()->with('status', 'Residence hall updated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Dorm $dorm = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', Rule::unique('dorms', 'code')->ignore($dorm?->id)],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
