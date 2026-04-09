<?php

use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BusinessCategoryController;
use App\Http\Controllers\Admin\BusinessCategoryMappingController;
use App\Http\Controllers\Admin\BusinessSubCategoryController;
use App\Http\Controllers\Admin\HsnController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\ProductController;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('admin')->group(function () {

    Route::get('/login', [AuthController::class, 'adminLoginForm'])->name('admin.login.form');
    Route::post('/login', [AuthController::class, 'adminLogin'])->name('admin.login');
    Route::get('/change-password', [AuthController::class, 'change_password'])->name('change.password');
    Route::post('change-password', [AuthController::class,'updateadminPassword'])->name('admin.password.update');
    Route::post('/admin/send-password-otp',[AuthController::class,'sendPasswordOtp'])->name('admin.password.send.otp');
    Route::post('/admin/verify-password-otp',[AuthController::class,'verifyPasswordOtp'])->name('admin.password.verify.otp');


    Route::middleware('auth:admin')->group(function () {

        // Use DashboardController instead
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');


        /*
        |--------------------------------------------------------------------------
        | Business Category Management
        |--------------------------------------------------------------------------
        */
        Route::resource('business-categories', BusinessCategoryController::class);
        Route::resource('business-sub-categories', BusinessSubCategoryController::class);

        /*
        |--------------------------------------------------------------------------
        | Product Management
        |--------------------------------------------------------------------------
        */

        //product category
        Route::get('/product-category-list', [ProductController::class, 'productCategoryList'])->name('admin.product.category.list');
        Route::get('/product-category', [ProductController::class, 'productCategory'])->name('admin.product.category');
        Route::post('/product-category-store', [ProductController::class, 'productCategoryStore'])->name('admin.product.category.store');
        Route::get('/product-category-edit/{id}', [ProductController::class, 'productCategoryEdit'])->name('admin.product.category.edit');
        Route::put('/product-category-update/{id}',[ProductController::class, 'productCategoryUpdate'])->name('admin.product.category.update');
        Route::delete('/product-category-delete/{id}', [ProductController::class, 'productCategoryDelete'])->name('admin.product.category.delete');
        Route::post('/product-category-status/{id}', [ProductController::class, 'productCategoryStatus'])->name('admin.product.category.status');
        //end product category


        //product sub category
        Route::get('/product-sub-category-list', [ProductController::class, 'productSubCategoryList'])->name('admin.product.sub.category.list');
        Route::get('/product-sub-category', [ProductController::class, 'productSubCategory'])->name('admin.product.sub.category');
        Route::post('/product-sub-category-store', [ProductController::class, 'productSubCategoryStore'])->name('admin.product.sub.category.store');
        Route::get('/product-sub-category-edit/{id}', [ProductController::class, 'productSubCategoryEdit'])->name('admin.product.sub.category.edit');
        Route::post('/product-sub-category-update/{id}', [ProductController::class, 'productSubCategoryUpdate'])->name('admin.product.sub.category.update');
        Route::delete('admin/product-sub-category-delete/{id}', [ProductController::class, 'productSubCategoryDelete'])->name('admin.product.sub.category.delete');
        //end product category

        //product sub category item
        Route::get('/product-sub-category-item-list', [ProductController::class, 'productSubCategoryItemList'])->name('admin.product.sub.category.item.list');
        Route::get('/product-sub-category-item', [ProductController::class, 'productSubCategoryItem'])->name('admin.product.sub.category.item');
        Route::post('/product-sub-category-item-store', [ProductController::class, 'productSubCategoryItemStore'])->name('admin.product.sub.category.item.store');
        Route::get('/product-sub-category-item-edit/{id}', [ProductController::class, 'productSubCategoryItemEdit'])->name('admin.product.sub.category.item.edit');
        Route::post('/product-sub-category-item-update/{id}', [ProductController::class, 'productSubCategoryItemUpdate'])->name('admin.product.sub.category.item.update');
        Route::delete('admin/product-sub-category-item-delete/{id}', [ProductController::class, 'productSubCategoryItemDelete'])->name('admin.product.sub.category.item.delete');
        //end product category item

        //product
        Route::get('/product-list', [ProductController::class, 'productList'])->name('admin.product.list');
        Route::get('/product', [ProductController::class, 'productView'])->name('admin.product.view');
        Route::post('/product', [ProductController::class, 'productAdd'])->name('admin.product.add');
        Route::get('/product-edit/{id}', [ProductController::class, 'productEdit'])->name('admin.product.edit');
        Route::put('/product-update/{id}', [ProductController::class, 'productUpdate'])->name('admin.product.update');
        Route::delete('/admin/product/delete/{id}', [ProductController::class, 'productdelete'])->name('admin.product.delete');
        Route::get('/products/{id}', [ProductController::class, 'viewProductDetails'])->name('products.view');
        //end product

        /*
        |--------------------------------------------------------------------------
        | Business Category Mapping
        |--------------------------------------------------------------------------
        */
        Route::resource('business-category-mapping', BusinessCategoryMappingController::class);
        Route::get('get-subcategories/{id}', [BusinessCategoryMappingController::class, 'getSubCategories'])->name('get.subcategories');

        //Ajax

        //Route::post('/check-username', [MemberController::class, 'checkUsername'])->name('admin.check.username');
        //Route::post('/check-phone', [MemberController::class, 'checkPhone'])->name('check.phone');
        Route::post('/product/category/check', [ProductController::class, 'checkCategory'])->name('product.category.check');
        Route::post('/admin/product/sub-category/check-name', [ProductController::class, 'checkName'])->name('admin.product.sub.category.check.name');
        Route::post('/admin/product/sub-category-item/check-name', [ProductController::class, 'checksubcatitemName'])->name('admin.product.sub.category.item.check.name');
        Route::get('/get-subcategories/{category_id}', [ProductController::class, 'getSubCategories'])->name('get.subcategories');
        Route::get('/getsubcategorieslist/{category_id}', [ProductController::class, 'getsubcategorieslist'])->name('get.subcategories.list');
        //Route::get('/get-subcategories/tohfa/{category_id}', [TohfaController::class, 'gettohfaSubCategories'])->name('get.tohfa.subcategories');
        Route::get('/load/product-list', [ProductController::class, 'loadProductList']) ->name('admin.load.product.list');
        Route::post('/admin/product/delete-image', [ProductController::class, 'productDeleteImage'])->name('admin.product.delete.image');

        //End Ajax


        /*
        |--------------------------------------------------------------------------
        | Attributes & Attributes Values
        |--------------------------------------------------------------------------
        */
        Route::resource('attributes', AttributeController::class);
        Route::resource('attribute-values', AttributeValueController::class);

        /*
        |--------------------------------------------------------------------------
        | HSN
        |--------------------------------------------------------------------------
        */
        Route::resource('hsns', HsnController::class);

        /*
        |--------------------------------------------------------------------------
        | Package Master
        |--------------------------------------------------------------------------
        */
        Route::resource('packages', PackageController::class);


    });
});
