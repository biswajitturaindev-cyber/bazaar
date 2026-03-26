<?php

use App\Http\Controllers\Api\BusinessCategoryController;
use App\Http\Controllers\Api\BusinessSubCategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\SubCategoryItemController;

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

/*
|--------------------------------------------------------------------------
| Vendors
|--------------------------------------------------------------------------
*/
