<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\NotificationController;


Route::middleware('auth:api')->prefix('notifications')->group(function (){
  Route::get('/',[NotificationController::class , 'index'])->name('notifications');
  Route::get('/{id}/view',[NotificationController::class, 'view'])->name('notifications.view');
  Route::post('/internal-url/add',[NotificationController::class, 'addRedirectUrl'])->name('notifications.add-redirect-url');
});