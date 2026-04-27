<?php

use App\Http\Controllers\Api\v1\Member\AuthController;
use App\Http\Controllers\Api\v1\Member\BusinessCategoryController;
use App\Http\Controllers\Api\v1\Member\BusinessSubCategoryController;
use App\Http\Controllers\Api\v1\Member\CategoryController;
use App\Http\Controllers\Api\v1\Member\ProductController;
use App\Http\Controllers\Api\v1\Member\VendorController;
use App\Http\Controllers\Api\v1\Member\CartController;
use App\Http\Controllers\Api\v1\Member\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;



Route::prefix('member')->group(function () {

    Route::get('/captcha', function () {

        $code = strtoupper(Str::random(6));

        return response()->json([
            'image' => '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="40">
                        <rect width="100%" height="100%" fill="#f2f2f2"/>
                        <text x="10" y="25" font-size="20" fill="#333">' . $code . '</text>
                    </svg>',
            'key' => encrypt([
                'code' => $code,
                'time' => now()->timestamp
            ])
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Public Routes
    |--------------------------------------------------------------------------
    */
    Route::get('business-categories', [BusinessCategoryController::class, 'index']);
    Route::get('business-subcategories', [BusinessSubCategoryController::class, 'index']);
    Route::get('vendors', [VendorController::class, 'index']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('products', [ProductController::class, 'index']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']); // FIXED

    // Protected
    // Route::middleware('member.api')->group(function () {
    //     Route::post('business-subcategories', [BusinessSubCategoryController::class, 'store']);
    // });

    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);

        /*
        |--------------------------------------------------------------------------
        | Cart Master
        |--------------------------------------------------------------------------
        */
        Route::get('cart', [CartController::class, 'index']);
        Route::post('cart', [CartController::class, 'store']);
        Route::put('cart/{id}', [CartController::class, 'update']);
        Route::delete('cart/{id}', [CartController::class, 'destroy']);

        /*
        |--------------------------------------------------------------------------
        | Order Master
        |--------------------------------------------------------------------------
        */
        Route::post('orders', [OrderController::class, 'store']);
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{id}', [OrderController::class, 'show']);
        Route::put('orders/{id}/status', [OrderController::class, 'updateStatus']);
    });
});
