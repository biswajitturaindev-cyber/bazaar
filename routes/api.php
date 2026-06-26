<?php

use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\AttributeMasterController;
use App\Http\Controllers\Api\AttributeValueController;
use App\Http\Controllers\Api\BusinessCategoryMappingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankDetailController;
use App\Http\Controllers\Api\BusinessCategoryController;
use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\BusinessSubCategoryController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommissionReportController;
use App\Http\Controllers\Api\HsnController;
use App\Http\Controllers\Api\KycDetailController;
use App\Http\Controllers\Api\MasterProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductReviewController;
use App\Http\Controllers\Api\RedemptionCancelReasonController;
use App\Http\Controllers\Api\StateController;
use App\Http\Controllers\Api\StoreOperationalController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\SubCategoryItemController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VendorBannerController;
use App\Http\Controllers\Api\VendorProductController;
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

    $code = strtoupper(Str::random(6));

    return response()->json([
        'image' => '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="40">
                        <rect width="100%" height="100%" fill="#f2f2f2"/>
                        <text x="10" y="25" font-size="20" fill="#333">'.$code.'</text>
                    </svg>',
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
    Route::get('/states', [StateController::class, 'index']);
    Route::apiResource('kyc-details', KycDetailController::class);
    Route::post('kyc-details/update/shop-image', [KycDetailController::class, 'updateShopImage']);
    Route::post('/kyc-details-commission-destribution/{kyc_id}', [KycDetailController::class, 'updateCommissionDistribution']);
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
    | Attribute & Attribute Master & Attribute values
    |--------------------------------------------------------------------------
    */
    Route::apiResource('attributes', AttributeController::class);
    Route::apiResource('attribute-master', AttributeMasterController::class);
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
     Route::patch('product-variants/{variant_id}/status', [ProductController::class, 'updateVariantStatus']);
     Route::delete('delete-product-image/{id}', [ProductController::class, 'deleteProductImage']);
     Route::get(
        'categories/{category_id}/attribute-exists',
        [ProductController::class, 'checkAttributeExists']
     );
    /*
    |--------------------------------------------------------------------------
    | Review Products
    |--------------------------------------------------------------------------
    */
    Route::apiResource('review-products', ProductReviewController::class);

    /*
    |--------------------------------------------------------------------------
    | Master Products
    |--------------------------------------------------------------------------
    */
    Route::apiResource('master-products', MasterProductController::class);

    /*
    |--------------------------------------------------------------------------
    | Business Lists
    |--------------------------------------------------------------------------
    */
    Route::apiResource('vendor-business', BusinessController::class);

    /*
    |--------------------------------------------------------------------------
    | Vendor Product Master
    |--------------------------------------------------------------------------
    */
    Route::post('add-master-product-to-vendor-products', [VendorProductController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | Vendor Banners Master
    |--------------------------------------------------------------------------
    */
    Route::resource('vendor-banners', VendorBannerController::class);
    /*
    |--------------------------------------------------------------------------
    | Orders Master
    |--------------------------------------------------------------------------
    */
    Route::resource('orders', OrderController::class);
    Route::post('orders/modify-item-quantity', [OrderController::class, 'modifyItemQuantity']);
    Route::post('orders/cancel-item',[OrderController::class, 'cancelItem']);
    Route::get('orders/{encoded_id}/invoice', [OrderController::class, 'invoice']);
    /*
    |--------------------------------------------------------------------------
    | Get redemption cancel reasons
    |--------------------------------------------------------------------------
    */
    Route::resource(
        'cancel-reasons',
        RedemptionCancelReasonController::class
    )->only(['index']);

    /*
    |--------------------------------------------------------------------------
    | Commission Report
    |--------------------------------------------------------------------------
    */
    Route::resource(
        'commission-reports',
        CommissionReportController::class
    )->only(['index', 'show']);


});

Route::prefix('main')->middleware('main.api')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::apiResource('business-categories', BusinessCategoryController::class);
    Route::apiResource('business-subcategories', BusinessSubCategoryController::class);

});
