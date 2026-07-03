<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        // Strict admin check: Only nthewawitharana@gmail.com is allowed as admin
        if ($role === 'admin' && Auth::user()->email !== 'nthewawitharana@gmail.com') {
            Auth::logout();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized admin access.'], 403);
            }
            abort(403, 'Unauthorized access.');
        }

        if (Auth::user()->role !== $role) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized role access.'], 403);
            }
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
