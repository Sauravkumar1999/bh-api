<?php

use App\Http\Controllers\V1\BulletinController;
use Illuminate\Support\Facades\Route;



Route::get('bulletin', [BulletinController::class, 'bulletinListing'])->name('bulletin');
Route::get('/bulletin/{id}/view', [BulletinController::class, 'view'])->name('bulletin.view');

Route::middleware('auth:api')->group(function () {
    Route::post('bulletin/create', [BulletinController::class, 'create'])->name('bulletin.create') ->middleware(['permission:create-bulletin|update-bulletin|delete-bulletin']);
    Route::post('bulletin/{id}/update', [BulletinController::class, 'update'])->name('bulletin.update')->middleware(['permission:create-bulletin|update-bulletin|delete-bulletin']);
    Route::delete('/bulletin/{id}/delete', [BulletinController::class, 'delete'])->name('bulletin.delete');
});

