<?php

use App\Http\Controllers\V1\CompanyController;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:api')->group(function () {
    Route::get('companies', [CompanyController::class, 'index'])->name('companies')->middleware(['permission:view-company']);
    Route::get('companies/{id}', [CompanyController::class, 'view'])->name('view_companies')->middleware(['permission:view-company']);
    Route::post('/companies/create',[CompanyController::class, 'create'])->name('create_company')->middleware(['permission:create-company']);
    Route::put('/companies/{code}/update',[CompanyController::class, 'update'])->name('update_company')->middleware(['permission:update-company']);
    Route::delete('/companies/{code}/delete',[CompanyController::class, 'delete'])->name('delete_company')->middleware(['permission:delete-company']);
    Route::get('/companies/{company_code}/verify',[CompanyController::class, 'verify'])->name('verify_company_code');
});

