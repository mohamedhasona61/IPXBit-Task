<?php

namespace App\Http\Controllers\Tenant;


use App\Http\Controllers\Controller;
use App\Models\RefreshToken;
use Illuminate\Http\Request;
use App\Services\JWTService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;


class AuthController extends Controller
{
    public function login(Request $request, JwtService $jwt)
    {
        $request->validate([
            'tenant'   => 'required|string',
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);
        $tenant = Tenant::where('slug', $request->tenant)
            ->orWhere('id', $request->tenant)
            ->first();
        if (!$tenant || $tenant->status !== 'active') {
            return response()->json(['error' => 'Tenant not found or inactive'], 404);
        }
        DB::purge('tenant');
        config()->set('database.connections.tenant', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $tenant->db_name,
            'username' => $tenant->db_user,
            'password' => $tenant->db_pass,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);
        DB::reconnect('tenant');
        $user = DB::connection('tenant')->table('users')->where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        // ✅ Issue both tokens (access + refresh)
        $tokens = $jwt->generateTokens([
            'sub'       => $user->id,
            'tenant_id' => $tenant->id,
            'role'      => $user->role,
        ]);

        return response()->json($tokens);
    }

    public function refresh(Request $request, JwtService $jwt)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['error' => 'No refresh token provided'], 401);
        }
        try {
            $payload = $jwt->decode($token);
            if (($payload->type ?? '') !== 'refresh') {
                return response()->json(['error' => 'Invalid token type'], 401);
            }
            $stored = RefreshToken::where('token', $token)->first();
            if (!$stored || $stored->revoked || $stored->expires_at->isPast()) {
                return response()->json(['error' => 'Refresh token revoked or expired'], 401);
            }
            $stored->update(['revoked' => true]);
            $newTokens = $jwt->generateTokens([
                'sub'       => $payload->sub,
                'tenant_id' => $payload->tenant_id,
                'role'      => $payload->role ?? 'user'
            ]);
            return response()->json($newTokens);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid or expired refresh token'], 401);
        }
    }
    public function logout()
    {
        $userId = auth('jwt-tenant')->id();
        $tenantId = auth('jwt-tenant')->user()?->tenant_id;
        RefreshToken::where('user_id', $userId)
            ->where('tenant_id', $tenantId)
            ->update(['revoked' => true]);

        return response()->json(['message' => 'Logged out successfully']);
    }
}
