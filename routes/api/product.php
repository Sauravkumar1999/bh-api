<?php
    use App\Http\Controllers\V1\ProductController;
    use Illuminate\Support\Facades\Route;


    // Route::get('product', [ProductController::class, 'index']);

    Route::middleware('auth:api')->group(function () {
        Route::prefix('products')->controller(ProductController::class)->group(function () {
            Route::get('/', 'index')->name('products.index')->middleware(['permission:view-product']);
            Route::post('create', 'create')->name('products.create')->middleware(['permission:create-product']);
            Route::post('{id}/update', 'update')->name('products.update')->middleware(['permission:update-product']);
            Route::delete('{id}/delete', 'destroy')->name('products.delete')->middleware(['permission:delete-product']);
        });
    });
