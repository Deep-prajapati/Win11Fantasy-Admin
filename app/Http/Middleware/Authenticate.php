<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticate extends BaseAuthenticate
{
    /**
     * Handle an unauthenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array<int, string>  $guards
     */
    protected function unauthenticated($request, array $guards): Response
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthorized access.',
                'status' => 403
            ], 403);
        }

        flash()->error('Unauthorized access.');
        return redirect()->route('admin.login');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        // Check if authenticated user has admin role
        if (Auth::check() && Auth::user()->role == 1) {
            return $next($request);
        }
        Auth::logout();
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthorized access.',
                'status' => 403
            ], 403);
        }
        // flash()->error('Unauthorized access.');
        return redirect()->route('admin.login');
    }
}
