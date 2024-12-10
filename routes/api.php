<?php

use App\Http\Controllers\V1\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


    Route::middleware('auth:api')->group(function () {

        Route::prefix('users')->controller(UserController::class)->group(function () {
            Route::get('/', 'index')->middleware(['permission:view-user']);
        });

        Route::get('/media/{media}', [\App\Http\Controllers\V1\MediaViewController::class, 'getMediaURL'])->name('media.get');
    });
