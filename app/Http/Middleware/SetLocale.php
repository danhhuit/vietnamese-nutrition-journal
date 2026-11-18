<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $locale = $user instanceof User ? $user->locale : session('locale', config('app.locale'));
        app()->setLocale(in_array($locale, ['vi', 'en'], true) ? $locale : 'vi');

        return $next($request);
    }
}
