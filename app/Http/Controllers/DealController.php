<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tenant\StoreDealRequest;
use App\Services\DealService;
use App\Traits\ApiResponseTrait;

class DealController extends Controller
{
    use ApiResponseTrait;

    protected DealService $dealService;

    public function __construct(DealService $dealService)
    {
        $this->dealService = $dealService;
    }

    public function store(StoreDealRequest $request)
    {
        try {
            $deal = $this->dealService->createDeal($request->validated());
            return $this->successResponse(201, 'Deal created successfully', $deal);
        } catch (\Exception $e) {

            return $this->errorResponse(500, 'Failed to create deal: ' . $e->getMessage());
        }
    }
}
