<?php

namespace Database\Seeders;

use App\Models\TenantUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class TenantAdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::connection('tenant')->table('users')->insertGetId([
            'name' => 'Tenant Admin',
            'email' => 'admin@tenant.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $admin = TenantUser::on('tenant')->find($adminId);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Role::on('tenant')->firstOrCreate(['name' => 'admin', 'guard_name' => 'jwt-tenant']);
        Role::on('tenant')->firstOrCreate(['name' => 'user', 'guard_name' => 'jwt-tenant']);
        Permission::on('tenant')->firstOrCreate(['name' => 'manage contacts', 'guard_name' => 'jwt-tenant']);
        $admin->assignRole('admin');
    }
}
