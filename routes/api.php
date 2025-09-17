<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DealController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminTenantController;
use App\Http\Controllers\Tenant\AuthController;

Route::prefix('v1')->group(function () {
    Route::post('/tenants', [TenantController::class, 'store']);
    Route::get('/tenants', [TenantController::class, 'index']);
    Route::post('/tenant/login', [AuthController::class, 'login']);


    Route::prefix('admin')->group(function () {
        Route::post('/tenants', [AdminTenantController::class, 'store']);
        Route::get('/tenants', [AdminTenantController::class, 'index']);
        Route::patch('/tenants/{id}/suspend', [AdminTenantController::class, 'suspend']);
    });
    Route::middleware(['tenant.auth'])->prefix('tenant')->group(function () {
        Route::get('/contacts', [ContactController::class, 'index']);
        Route::post('/contacts', [ContactController::class, 'store']);
        Route::post('/deals', [DealController::class, 'store']);
        Route::get('/reports/deals', [ReportController::class, 'dealsSummary']);
        Route::post('/refresh-token', [AuthController::class, 'refresh']);
    });
});
