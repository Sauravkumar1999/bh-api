<?php

use App\Http\Controllers\V1\PermissionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::prefix('permissions')->controller(PermissionController::class)->group(function () {
        Route::get('/', 'index')->name('permissions.index')->middleware(['permission:view-permission']);
        Route::post('create', 'create')->name('permissions.create')->middleware(['permission:create-permission']);
        Route::patch('{id}/update', 'update')->name('permissions.update')->middleware(['permission:update-permission']);
        Route::delete('{id}/delete', 'destroy')->name('permissions.delete')->middleware(['permission:delete-permission']);
    });
});