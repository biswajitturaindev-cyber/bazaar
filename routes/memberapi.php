<?php
use App\Http\Controllers\Api\v1\Member\BusinessCategoryController;
use App\Http\Controllers\Api\v1\Member\BusinessSubCategoryController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Business Category Master
|--------------------------------------------------------------------------
*/
Route::prefix('member')->middleware('member.api')->group(function () {
    
    Route::post('register', [AuthController::class, 'register']);
    Route::apiResource('business-categories', BusinessCategoryController::class);
    Route::apiResource('business-subcategories', BusinessSubCategoryController::class);

});


