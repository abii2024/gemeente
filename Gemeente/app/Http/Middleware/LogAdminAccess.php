<?php

namespace App\Http\Middleware;

use App\Services\PrivacyLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log admin page access for audit purposes
        if (auth()->check() && auth()->user()->can('access admin')) {
            PrivacyLogger::logUserAction('admin_page_access', [
                'route' => $request->route()->getName(),
                'method' => $request->method(),
                'path' => $request->path(),
            ]);
        }

        return $next($request);
    }
}
