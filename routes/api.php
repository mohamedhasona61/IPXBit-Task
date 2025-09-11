<?php

use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/tenants', [TenantController::class, 'store']);
    Route::get('/tenants', [TenantController::class, 'index']);
});
