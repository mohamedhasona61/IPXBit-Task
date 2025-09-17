<?php

namespace App\Auth\Guards;

use App\Models\Tenant;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use App\Services\JwtService;
use Exception;
use App\Models\TenantUser;

class JwtTenantGuard implements Guard
{
    protected ?Authenticatable $user = null;
    protected JwtService $jwt;

    public function __construct(JwtService $jwt)
    {
        $this->jwt = $jwt;
    }

    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        $token = request()->bearerToken();
        if (!$token) {
            return null;
        }

        try {
            $payload = $this->jwt->decode($token);
            request()->attributes->set('jwt_payload', $payload);
            $tenant = Tenant::find($payload->tenant_id);
            if (!$tenant || $tenant->status !== 'active') {
                return null;
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
            $user = TenantUser::on('tenant')->find($payload->sub);
            if ($user) {
                $this->user = $user;
                return $this->user;
            }
        } catch (Exception $e) {
            return null;
        }

        return null;
    }
    public function check()
    {
        return $this->user() !== null;
    }
    public function guest()
    {
        return $this->user() === null;
    }
    public function id()
    {
        return $this->user?->id;
    }
    public function validate(array $credentials = [])
    {
        return false;
    }
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
        return $this;
    }
    public function hasUser()
    {
        return $this->user !== null;
    }
}
