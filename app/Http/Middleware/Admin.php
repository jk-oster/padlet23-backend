<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use JWTAuth;

class Admin
{
    /**
     * Handle an incoming request. Check if user is admin
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user == null || $user->role != "admin") {
            return response()->json(['user not in role admin'], 401);
        }
        return $next($request);
    }
}
