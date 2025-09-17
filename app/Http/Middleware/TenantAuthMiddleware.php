<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TenantAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        $guard = Auth::guard('jwt-tenant');
        if (!$guard->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = $guard->user();
        $payload = $request->attributes->get('jwt_payload');
        if (!$payload || !isset($payload->tenant_id)) {
            return response()->json(['error' => 'Invalid token payload'], 401);
        }
        $tenant = Tenant::find($payload->tenant_id);
        if (!$tenant || $tenant->status !== 'active') {
            return response()->json(['error' => 'Tenant suspended or not found'], 403);
        }
        try {
            DB::connection('tenant')->getPdo();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Tenant DB connection failed'], 500);
        }
        return $next($request);
    }
}
