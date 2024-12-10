<?php

use App\Http\Controllers\V1\User\MyInfoController;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:api')->group(function () {
    Route::post('my-info/manage/update', [MyInfoController::class, 'manageUpdate'])->name('myinfo.update');
    Route::post('my-info/update-channel-order', [MyInfoController::class, 'updateChannelOrder'])->name('myinfo.update_channel_order');
});

