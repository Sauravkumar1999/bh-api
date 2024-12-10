<?php

use App\Http\Controllers\V1\SaleController;
use Illuminate\Support\Facades\Route;




Route::get('sales/my-page/{user_code?}', [SaleController::class, 'getChannels'])->name('get.channels');


Route::middleware('auth:api')->group(function () {
    Route::prefix('sales')->controller(SaleController::class)->group(function () {
        Route::get('/', 'getSales')->name('sales');
        Route::post('create', 'create')->name('sales.create');
        Route::patch('{id}/update', 'update')->name('sales.update');
        Route::delete('{id}/delete', 'destroy')->name('sales.delete');
        Route::get('{id}/detail', 'detail')->name('sales.detail');
        Route::get('{id}/product-detail', 'productDetail')->name('sales.productDetail');
    });
});
