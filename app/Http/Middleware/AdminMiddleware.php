<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route()?->getName() ?? '';
        if ($route === 'admin.dashboard') {
            abort_unless($request->user()?->isAdmin() || collect(['manage_users', 'manage_system', 'view_activity'])->contains(fn ($permission) => $request->user()?->canAccess($permission)), 403);

            return $next($request);
        }
        $permission = str_starts_with($route, 'admin.users.') ? 'manage_users'
            : (str_starts_with($route, 'admin.activity.') ? 'view_activity'
            : (str_starts_with($route, 'admin.permission-groups.') ? 'manage_users'
            : (str_starts_with($route, 'admin.settings.') ? 'manage_system' : 'manage_catalog')));
        abort_unless($request->user()?->canAccess($permission), 403);

        return $next($request);
    }
}
