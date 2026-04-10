<?php

use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\AttributeValueController;
use App\Http\Controllers\Api\BusinessCategoryMappingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankDetailController;
use App\Http\Controllers\Api\BusinessCategoryController;
use App\Http\Controllers\Api\BusinessSubCategoryController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\HsnController;
use App\Http\Controllers\Api\KycDetailController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StoreOperationalController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\SubCategoryItemController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Public Routes (No Auth)
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Captcha
|--------------------------------------------------------------------------
*/
Route::get('/captcha', function () {
    $code = strtoupper(Str::random(5));

    return response()->json([
        'image' => captcha_img($code),
        'key' => encrypt([
            'code' => $code,
            'time' => now()->timestamp
        ])
    ]);
});



/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Users+ Business+  Master
    |--------------------------------------------------------------------------
    */
    Route::apiResource('users', UserController::class);
    Route::get('/users/gst/check', [UserController::class, 'checkGst']);
    Route::get('/users/category/dropdown', [UserController::class, 'CategoryDropdown']);
    Route::apiResource('bank-details', BankDetailController::class);
    Route::apiResource('kyc-details', KycDetailController::class);
    Route::apiResource('store-operational', StoreOperationalController::class);

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
    Route::get('/subcategoriesdropdown/{category_id?}', [SubCategoryController::class, 'dropdown']);

    /*
    |--------------------------------------------------------------------------
    | Sub Category Items
    |--------------------------------------------------------------------------
    */
    Route::apiResource('sub-category-items', SubCategoryItemController::class);
    Route::get('/subcategoryitemsdropdown/{category_id?}/{sub_category_id?}', [SubCategoryItemController::class, 'dropdown']);

    /*
    |--------------------------------------------------------------------------
    | Business Category Mappings
    |--------------------------------------------------------------------------
    */
    Route::apiResource('business-category-mappings', BusinessCategoryMappingController::class);

    /*
    |--------------------------------------------------------------------------
    | Attribute & Attribute values
    |--------------------------------------------------------------------------
    */
    Route::apiResource('attributes', AttributeController::class);
    Route::apiResource('attribute-values', AttributeValueController::class);

    /*
    |--------------------------------------------------------------------------
    | HSN MASTER
    |--------------------------------------------------------------------------
    */
    Route::apiResource('hsns', HsnController::class);
    Route::get('/hsndropdown', [HsnController::class, 'dropdown']);

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */
    Route::apiResource('products', ProductController::class);
    Route::delete('delete-product-image/{id}', [ProductController::class, 'deleteProductImage']);



});
