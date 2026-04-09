<?php
use App\Http\Controllers\Api\v1\Member\BusinessCategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Business Category Master
|--------------------------------------------------------------------------
*/
Route::prefix('v1/member')->group(function () {

    Route::apiResource('business-categories', BusinessCategoryController::class);

});


