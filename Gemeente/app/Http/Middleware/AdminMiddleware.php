<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        // Check if user has admin role
        if (! $user->hasRole('admin')) {
            abort(403, 'Toegang geweigerd. Admin rechten vereist.');
        }

        return $next($request);
    }
}
