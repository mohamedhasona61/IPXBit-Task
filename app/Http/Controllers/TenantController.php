<?php

namespace App\Http\Controllers;

use App\Services\TenantService;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\TenantRequest;

class TenantController extends Controller
{
    use ApiResponseTrait;
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }
    public function store(TenantRequest $request)
    {
        $data = $request->validated();
        $tenant = $this->tenantService->createTenant($data);
        return $this->successResponse(200, "Tenant Created Successfully");
    }
}
