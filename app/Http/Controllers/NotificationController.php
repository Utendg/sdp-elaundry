<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Full list of the user's notifications. Opening the list counts as reading them:
     * the page still highlights the ones that were new, then they are marked read.
     */
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()->paginate(20);
        $newIds = $notifications->getCollection()->whereNull('read_at')->pluck('id')->all();

        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return view('notifications.index', [
            'notifications' => $notifications,
            'newIds' => $newIds,
        ]);
    }

    /** Mark one notification read and redirect to its linked page. */
    public function read(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;

        return $url ? redirect()->to($url) : back();
    }

    /** Mark every unread notification as read (also called quietly when the bell menu is opened). */
    public function readAll(Request $request): RedirectResponse|JsonResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['unread' => 0]);
        }

        return back()->with('status', 'All notifications marked as read.');
    }
}
