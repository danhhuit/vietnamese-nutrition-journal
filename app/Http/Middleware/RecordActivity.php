<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

final class RecordActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        if ($request->user() && Schema::hasTable('activity_logs')) {
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => $request->method(),
                'route_name' => $request->route()?->getName(),
                'path' => substr($request->path(), 0, 255),
                'ip_address' => $request->ip(),
                'status_code' => $response->getStatusCode(),
            ]);
        }

        return $response;
    }
}
