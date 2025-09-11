<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTenantJwt
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::guard('tenant-jwt')->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }


        return $next($request);
    }
}
