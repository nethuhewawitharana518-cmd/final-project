<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BusinessApprovedMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->isBusinessOwner()) {
            $business = $user->business;
            if (!$business || !$business->isApproved()) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Your business is pending admin approval.'], 403);
                }
                Auth::logout();
                return redirect()->route('login')->with('error', 'Your business account is pending admin approval or has been suspended.');
            }
        }

        return $next($request);
    }
}
