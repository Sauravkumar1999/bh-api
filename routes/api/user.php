<?php

use App\Http\Controllers\V1\MediaViewController;
use App\Http\Controllers\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/find-password/verify', [UserController::class, 'verify']);
Route::get('/user/{user_code}', [UserController::class, 'getUser']);

Route::group(['prefix' => 'auth'], function () {
    Route::post('/find-password/{id}/reset', [UserController::class, 'reset'])->name('user.reset-password');
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('users')->controller(UserController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/test-user-upload', 'testUpload');
        Route::get('/get-channel/{user_id}', 'getChannel')->name('user.get-channel');
        Route::post('/update-channel/{user_id}', 'updateChannelSettings');
    });
    Route::prefix('user')->controller(UserController::class)->group(function () {
        Route::get('/', 'index')->name('users')->middleware(['permission:view-user']);
        Route::delete('/delete', 'delete')->name('user.delete');
        Route::get('/{user_id}/single', 'singleUser')->name('user.single');
        Route::post('/create', 'create')->name('user.create');
        Route::post('/{user_id}/update', 'update')->name('user.update');
        Route::post('/activate-deleted', 'activateDeleted')->name('user.activate-deleted');
    });
    Route::get('/image/{media}', [MediaViewController::class, 'getMediaURL'])
        ->name('media.image.display');
});
