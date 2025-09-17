<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Traits\ApiResponseTrait;

class ReportController extends Controller
{
    use ApiResponseTrait;

    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function dealsSummary()
    {
        try {
            $summary = $this->reportService->getDealsSummary();
            return $this->successResponse(200, 'Deals summary retrieved successfully', $summary);
        } catch (\Exception $e) {
            return $this->errorResponse(500, 'Failed to retrieve report: ' . $e->getMessage());
        }
    }
}
