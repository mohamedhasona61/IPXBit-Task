<?php

namespace App\Http\Controllers;

use App\Services\TenantService;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\Admin\CreateTenantRequest;
use App\Http\Requests\Admin\SuspendTenantRequest;

class AdminTenantController extends Controller
{
    use ApiResponseTrait;
    protected TenantService $tenantService;
    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }
    public function store(CreateTenantRequest $request)
    {
        try {
            $tenant = $this->tenantService->createTenant($request->validated());
            return $this->successResponse(201, 'Tenant created successfully', $tenant);
        } catch (\Exception $e) {
            return $this->errorResponse(500, 'Tenant creation failed: ' . $e->getMessage());
        }
    }
    public function index()
    {
        $tenants = $this->tenantService->listTenants();
        return $this->successResponse(200, 'Tenants retrieved successfully', $tenants);
    }
    public function suspend(int $id, SuspendTenantRequest $request)
    {
        try {
            $tenant = $this->tenantService->suspendTenant($id, $request->input('status', 'suspended'));
            return $this->successResponse(200, "Tenant {$tenant->name} status updated", $tenant);
        } catch (\Exception $e) {
            return $this->errorResponse(500, 'Failed to update tenant status: ' . $e->getMessage());
        }
    }
}
