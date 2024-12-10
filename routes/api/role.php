<?php

use App\Http\Controllers\V1\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->prefix('/role')->controller(RoleController::class)->group(function(){
    Route::post('/create', 'store')->name('role.create')->middleware(['permission:create-user-role|update-user-role|delete-user-role']);
    Route::put('/{id}/update', 'update')->name('role.update')->middleware(['permission:update-user-role']);
    Route::delete('/{id}/delete', 'destroy')->name('role.delete')->middleware(['permission:delete-user-role']);
    Route::get('/', [RoleController::class, 'index'])->name('role.index')->middleware(['permission:view-user-role']);
    Route::get('/{id}', [RoleController::class, 'show'])->name('role.show')->middleware(['permission:view-user-role']);
});
