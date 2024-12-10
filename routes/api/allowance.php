<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\AllowancePaymentController;
use App\Http\Controllers\V1\AllowanceController;
use App\Http\Controllers\V1\AllowanceStatementController;

Route::middleware('auth:api')->prefix('allowance-payments')->group(function () {
    Route::post('/create', [AllowancePaymentController::class, 'store'])->name('allowance-payments.store');
    Route::patch('/{id}/update', [AllowancePaymentController::class, 'update'])->name('allowance-payments.update');
    Route::delete('/{id}/delete', [AllowancePaymentController::class, 'delete'])->name('allowance-payments.delete');
    Route::get('/', [AllowancePaymentController::class, 'index'])->name('allowance-payments.index');
    Route::get('{id}/show', [AllowancePaymentController::class, 'show'])->name('allowance-payments.show');
});


Route::middleware('auth:api')->prefix('allowances')->group(function () {
    Route::get('/', [AllowanceController::class, 'index'])->name('allowances')->middleware(['permission:create-allowance']);
    Route::get('/{id}', [AllowanceController::class, 'view'])->name('view_allowances')->middleware(['permission:create-allowance']);
    Route::post('/create', [AllowanceController::class, 'create'])->name('create_allowance')->middleware(['permission:create-allowance']);
    Route::patch('/{id}/update', [AllowanceController::class, 'update'])->name('update_allowance')->middleware(['permission:update-allowance']);
    Route::delete('/{id}/delete', [AllowanceController::class, 'delete'])->name('delete_allowance')->middleware(['permission:delete-allowance']);
});


Route::middleware('auth:api')->prefix('allowance-statements')->group(function () {
        Route::get('/', [AllowanceStatementController::class, 'index'])->name('allowance-statement.index');
        Route::get('/get-allowance/{month}', [AllowanceStatementController::class, 'get_allowance'])->name('allowance-statement.get-allowance');
});
