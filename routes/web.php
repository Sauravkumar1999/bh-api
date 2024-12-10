<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health', function () {

    return response([
        'healthy' => true,
    ])
        ->header('Content-Type', 'application/json')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
});
