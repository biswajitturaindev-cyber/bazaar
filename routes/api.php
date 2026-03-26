<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BusinessCategoryController;
use App\Http\Controllers\Api\BusinessSubCategoryController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\SubCategoryItemController;

/*
|--------------------------------------------------------------------------
| Public Routes (No Auth)
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // 🔐 Auth
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Business Category Master
    |--------------------------------------------------------------------------
    */
    Route::apiResource('business-categories', BusinessCategoryController::class);
    Route::apiResource('business-sub-categories', BusinessSubCategoryController::class);

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */
    Route::apiResource('categories', CategoryController::class);
    Route::get('/categoriesdropdown', [CategoryController::class, 'dropdown']);

    /*
    |--------------------------------------------------------------------------
    | Sub Categories
    |--------------------------------------------------------------------------
    */
    Route::apiResource('sub-categories', SubCategoryController::class);
    Route::get('/subcategoriesdropdown', [SubCategoryController::class, 'dropdown']);

    /*
    |--------------------------------------------------------------------------
    | Sub Category Items
    |--------------------------------------------------------------------------
    */
    Route::apiResource('sub-category-items', SubCategoryItemController::class);
    Route::get('/subcategoryitemsdropdown', [SubCategoryItemController::class, 'dropdown']);

});
