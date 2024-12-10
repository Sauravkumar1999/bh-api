<?php

use App\Http\Controllers\V1\MonthlyNewsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->prefix('/monthly-news')->controller(MonthlyNewsController::class)->group(function(){
    Route::get('/', 'index')->name('monthlyNews.index');
    Route::post('/create', 'store')->name('monthlyNews.create');
    Route::put('/{news_id}/update', 'update')->name('monthlyNews.update');
    Route::delete('/{news_id}/delete', 'destroy')->name('monthlyNews.delete');
    Route::get('/{news_id}/single', 'getSingle')->name('monthlyNews.getsingle');
});
