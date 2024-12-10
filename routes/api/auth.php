<?php

use App\Http\Controllers\V1\CompanyController;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\RegisterController;
use App\Http\Controllers\V1\SnapshotController;
use Illuminate\Support\Facades\Route;



Route::post('/register', RegisterController::class)->name('register');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login/refresh', [AuthController::class, 'refresh'])->name('login.refresh');

Route::post('/email_available/check', [AuthController::class, 'emailAvailable'])->name('email_available.check');
Route::post('/referral_code/check', [AuthController::class, 'verifyReferralCode'])->name('referral_code.check');

Route::middleware('auth:api')->group(function () {
    Route::post('/update/fcm-token', [AuthController::class, 'updateFCMToken'])->name('update_fcm_token');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::post('/snapshot/create', [SnapshotController::class, 'makeSnapshot'])->name('make-snapshot');
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/send_otp', [AuthController::class, 'sendOTP'])->name('auth.send_otp');
    Route::post('/verify_user', [AuthController::class, 'verifyUser'])->name('auth.verify_user');
    Route::get('/my_envs', function () {
        return app()->environment('production') ? [] : response()->json([
            'db_host'     => env('DB_HOST'),
            'db_port'     => env('DB_PORT'),
            'database'    => env('DB_DATABASE'),
            'db_username' => env('DB_USERNAME'),
            'db_pass'     => env('DB_PASSWORD'),
        ]);
    });
    // Route::post('/verify_otp', [AuthController::class, 'verifyOTP'])->name('auth.verify_otp');
    // Route::post('/find_user_id', [AuthController::class, 'findUserId'])->name('auth.find_user_id');
//    Route::post('/verify_phone', [AuthController::class, 'verifyPhone'])->name('auth.verify_phone');
});
