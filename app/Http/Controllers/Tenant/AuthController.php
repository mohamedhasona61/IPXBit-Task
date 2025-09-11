<?php

namespace App\Http\Controllers\Tenant;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\JWTService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $payload = $request->validate([
            'tenant_id' => 'required|integer|exists:tenants,id',
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        $tenant = Tenant::find($payload['tenant_id']);
        if (! $tenant || $tenant->status !== 'active') {
            return response()->json(['message' => 'Tenant not active'], 403);
        }
        DB::purge('tenant');
        config()->set('database.connections.tenant', [
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => $tenant->db_name,
            'username' => $tenant->db_user ?? env('DB_USERNAME'),
            'password' => $tenant->db_pass ?? env('DB_PASSWORD'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
        ]);
        DB::reconnect('tenant');
        $userModel = config('auth.providers.users.model');
        $user = (new $userModel)->setConnection('tenant')::where('email', $payload['email'])->first();
        if (! $user || ! Hash::check($payload['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $token = JWTService::sign([
            'sub' => $user->id,
            'tenant_id' => $tenant->id,
            'role' => $user->role ?? 'user',
        ]);
        return response()->json(['token' => $token]);
    }
}
