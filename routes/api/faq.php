<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\FAQController;

Route::middleware('auth:api')->group(function (){
  Route::get('/faq', [FAQController::class, 'index'])->name('faq')->middleware(['permission:view-faq']);
  Route::post('/faq/create',[FAQController::class, 'create'])->name('create_faq')->middleware(['permission:create-faq']);
  Route::get('/faq/{id}', [FAQController::class, 'view'])->name('view_faq')->middleware(['permission:view-faq']);
  Route::put('/faq/{id}/update', [FAQController::class, 'update'])->name('update_faq')->middleware(['permission:update-faq']);
  Route::delete('/faq/{id}/delete', [FAQController::class, 'delete'])->name('delete_faq')->middleware(['permission:delete-faq']);
});