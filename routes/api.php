<?php

use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/tenants', [TenantController::class, 'store']);
    Route::get('/tenants', [TenantController::class, 'index']);
    Route::post('/tenant/login', [App\Http\Controllers\Tenant\AuthController::class, 'login']);


    Route::middleware(['tenant.jwt'])->group(function () {
        // Route::get('/contacts', [App\Http\Controllers\ContactController::class, 'index']);
        // Route::post('/contacts', [App\Http\Controllers\ContactController::class, 'store']);
        // Route::post('/deals', [App\Http\Controllers\DealController::class, 'store']);
        // Route::get('/reports/deals', [App\Http\Controllers\ReportController::class, 'deals']);
    });
});
