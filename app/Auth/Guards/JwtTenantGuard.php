<?php

namespace App\Auth\Guards;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use App\Services\JWTService;
use App\Models\Tenant;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Support\Facades\DB;

class JwtTenantGuard implements Guard
{
    protected ?UserContract $user = null;
    protected Request $request;


    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    public function user()
    {
        if ($this->user) return $this->user;


        $token = $this->getTokenFromRequest();
        if (! $token) return null;


        try {
            $claims = JWTService::decode($token);
        } catch (\Exception $e) {
            return null;
        }


        // basic claims validation
        if (empty($claims->tenant_id) || empty($claims->sub)) {
            return null;
        }


        // find tenant in system DB
        $tenant = Tenant::find($claims->tenant_id);
        if (! $tenant || $tenant->status !== 'active') {
            return null;
        }


        // configure tenant connection dynamically
        DB::purge('tenant');
        config()->set('database.connections.tenant', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $tenant->db_name,
            'username' => $tenant->db_user ?? env('DB_USERNAME'),
            'password' => $tenant->db_pass ?? env('DB_PASSWORD'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
        ]);
        DB::reconnect('tenant');


        // fetch user from tenant DB
        $userModel = config('auth.providers.users.model');
        $user = (new $userModel)->setConnection('tenant')::where('id', $claims->sub)->first();
        if (! $user) return null;


        $this->user = $user;
        return $this->user;
    }
    protected function getTokenFromRequest(): ?string
    {
        $header = $this->request->header('Authorization', '') ?: $this->request->bearerToken();
        if (! $header) return null;
        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }
        return $header;
    }
    public function validate(array $credentials = [])
    {
        return (bool) $this->user();
    }
    public function check()
    {
        return $this->user() !== null;
    }


    public function guest()
    {
        return ! $this->check();
    }
    public function id()
    {
        return $this->user()?->getAuthIdentifier();
    }
    public function setUser(UserContract $user)
    {
        $this->user = $user;
        return $this;
    }


    public function hasUser() {}
}
