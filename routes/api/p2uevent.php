<?php

use App\Http\Controllers\V1\P2UEventController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:api')->group(function () {
    Route::post('event/p2uevent', [P2UEventController::class, 'addDailyAttendance'])->name('p2uevent.create');
    Route::get('event/fecth-p2u-event', [P2UEventController::class, 'fetchP2UEventPoint'])->name('p2uevent.fetch');
    Route::get('event/sum-p2u-event', [P2UEventController::class, 'fetchP2UEventPointSum'])->name('p2uevent.sum');
});
