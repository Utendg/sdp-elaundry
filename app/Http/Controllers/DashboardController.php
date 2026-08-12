<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Order;
use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Render a role-appropriate dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return match ($user->role) {
            User::ROLE_ADMIN => $this->admin(),
            User::ROLE_WORKER => $this->worker($user),
            default => $this->student($user),
        };
    }

    private function student(User $user): View
    {
        $orders = $user->ordersAsStudent()
            ->with('worker')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.student', [
            'recentOrders' => $orders,
            'activeCount' => $user->ordersAsStudent()
                ->whereNotIn('status', ['completed', 'cancelled', 'rejected'])
                ->count(),
        ]);
    }

    private function worker(User $user): View
    {
        $profile = $user->workerProfile;

        $baseQuery = fn () => $user->ordersAsWorker();

        return view('dashboard.worker', [
            'profile' => $profile,
            'pendingCount' => $baseQuery()->where('status', 'pending')->count(),
            'activeCount' => $baseQuery()->whereIn('status', ['accepted', 'picked_up', 'washing', 'ironing', 'ready'])->count(),
            'completedCount' => $baseQuery()->where('status', 'completed')->count(),
            'recentOrders' => $baseQuery()->with('student')->latest()->take(5)->get(),
        ]);
    }

    private function admin(): View
    {
        return view('dashboard.admin', [
            'totalStudents' => User::where('role', User::ROLE_STUDENT)->count(),
            'totalWorkers' => User::where('role', User::ROLE_WORKER)->count(),
            'pendingWorkers' => WorkerProfile::where('is_approved', false)->count(),
            'totalOrders' => Order::count(),
            'openComplaints' => Complaint::whereIn('status', ['open', 'under_review'])->count(),
            'recentOrders' => Order::with(['student', 'worker'])->latest()->take(8)->get(),
        ]);
    }
}
