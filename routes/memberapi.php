<?php

use App\Http\Controllers\Api\v1\Member\BusinessCategoryController;
use App\Http\Controllers\Api\v1\Member\BusinessSubCategoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\v1\Member\VendorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Business Category Master
|--------------------------------------------------------------------------
*/

Route::prefix('member')->group(function () {

    // Public
    Route::get('business-categories', [BusinessCategoryController::class, 'index']);
    Route::get('business-subcategories', [BusinessSubCategoryController::class, 'index']);
    Route::get('vendors', [VendorController::class, 'index']);

    // Protected
    Route::middleware('member.api')->group(function () {
        Route::post('business-subcategories', [BusinessSubCategoryController::class, 'store']);
    });
});
