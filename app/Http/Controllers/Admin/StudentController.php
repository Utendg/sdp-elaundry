<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dorm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * All students, grouped by residence hall (halls in their usual order: Dorm AA to Volpi Girls),
     * with an optional hall filter and name/email search.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'dorm' => ['nullable', 'integer'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $students = User::where('role', User::ROLE_STUDENT)
            ->with('dorm')
            ->withCount('ordersAsStudent')
            ->when($filters['dorm'] ?? null, fn ($q, $dorm) => $q->where('dorm_id', $dorm))
            ->when($filters['q'] ?? null, fn ($q, $term) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%'.$term.'%')
                ->orWhere('email', 'like', '%'.$term.'%')))
            ->orderBy('name')
            ->get();

        $dorms = Dorm::orderBy('id')->get();

        // One group per hall, in hall order; students without a hall come last.
        $groups = $dorms
            ->map(fn (Dorm $dorm) => ['dorm' => $dorm, 'students' => $students->where('dorm_id', $dorm->id)->values()])
            ->filter(fn ($g) => $g['students']->isNotEmpty())
            ->values();
        $noHall = $students->whereNull('dorm_id')->values();
        if ($noHall->isNotEmpty()) {
            $groups->push(['dorm' => null, 'students' => $noHall]);
        }

        return view('admin.students.index', [
            'groups' => $groups,
            'dorms' => $dorms,
            'total' => $students->count(),
            'filters' => $filters,
        ]);
    }
}
