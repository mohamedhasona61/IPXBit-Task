<?php

namespace App\Services;

use Exception;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class TenantService
{
    public function createTenant(array $data): Tenant
    {
        $tenant = null;
        try {
            $tenant = Tenant::create($data);
            DB::statement("CREATE DATABASE `{$tenant->db_name}`");
            DB::statement("DROP USER IF EXISTS '{$tenant->db_user}'@'%'");
            DB::statement("CREATE USER '{$tenant->db_user}'@'%' IDENTIFIED BY '{$tenant->db_pass}'");
            DB::statement("GRANT ALL PRIVILEGES ON `{$tenant->db_name}`.* TO '{$tenant->db_user}'@'%'");
            DB::statement("FLUSH PRIVILEGES");
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
            // Artisan::call('migrate', [
            //     '--path' => '/database/migrations/tenant',
            //     '--database' => 'tenant',
            //     '--force' => true,
            // ]);
            // Artisan::call('db:seed', [
            //     '--class' => 'TenantAdminSeeder',
            //     '--database' => 'tenant',
            //     '--force' => true,
            // ]);
            return $tenant;
        } catch (Exception $e) {
            DB::connection('mysql')->rollBack();
            if ($tenant && !empty($tenant->db_name)) {
                DB::statement("DROP DATABASE IF EXISTS `{$tenant->db_name}`");
                DB::statement("DROP USER IF EXISTS '{$tenant->db_user}'@'%'");
            }
            throw new Exception("Tenant creation failed: " . $e->getMessage(), 0, $e);
        }
    }
}
