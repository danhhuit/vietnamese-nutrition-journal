<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

final class EnsureApplicationAvailable
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Schema::hasTable('settings') || ! Setting::boolean('maintenance_enabled')) {
            return $next($request);
        }

        if ($request->user()?->isAdmin() || $request->is('admin/*') || $request->routeIs('login', 'logout', 'password.*')) {
            return $next($request);
        }

        return response()->view('errors.maintenance', [
            'message' => Setting::text('maintenance_message', 'Hệ thống đang bảo trì. Vui lòng quay lại sau.'),
        ], 503)->header('Retry-After', '600');
    }
}
