<?php

use App\Http\Controllers\V1\BankController;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:api')->group(function () {
    Route::get('banks', [BankController::class, 'bankListing'])->name('bank');
});
