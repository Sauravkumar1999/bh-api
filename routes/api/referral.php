<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\ReferralController;


Route::middleware('auth:api')->group(function () {
    Route::get('referrals', [ReferralController::class, 'index'])->name('referrals')->middleware(['permission:view-referrals']);
    Route::get('referrals/{id}/view', [ReferralController::class, 'referralTree'])->name('referralsTree')->middleware(['permission:view-referral-detail']);
});