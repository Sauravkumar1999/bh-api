<?php
    use App\Http\Controllers\V1\PromotionController;
    use Illuminate\Support\Facades\Route;


    Route::prefix('promotions')->controller(PromotionController::class)->group(function () {
        Route::get('/', 'index')->name('promotions.index');
    });
