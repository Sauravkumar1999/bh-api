<?php
    use App\Http\Controllers\V1\ChannelController;
    use Illuminate\Support\Facades\Route;


    // Route::get('product', [ProductController::class, 'index']);

    Route::middleware('auth:api')->group(function () {
        Route::prefix('channels')->controller(ChannelController::class)->group(function () {
            Route::get('/', 'index')->name('channels.index')->middleware(['permission:view-channel']);
            Route::post('create', 'create')->name('channels.create')->middleware(['permission:create-channel']);
            Route::post('{id}/update', 'update')->name('channels.update')->middleware(['permission:update-channel']);
            Route::delete('{id}/delete', 'destroy')->name('channels.delete')->middleware(['permission:delete-channel']);
        });
    });
