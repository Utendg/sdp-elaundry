<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPermission
{
    /**
     * Allow the request only if the admin has been given this responsibility
     * by the Head of Residence Life (who holds every responsibility).
     *
     * Usage in routes: ->middleware('admin.permission:complaints')
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! $request->user()?->hasAdminPermission($permission)) {
            abort(403, 'This responsibility has not been assigned to your admin account.');
        }

        return $next($request);
    }
}
