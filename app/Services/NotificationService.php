<?php

namespace App\Services;

use App\Http\Resources\NotificationResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\RedirectUrlsResource;
use App\Models\Notifiable;
use Illuminate\Support\Facades\Auth;
use App\Models\PushNotification as NotificationEntity;
use App\Models\User;
use App\Models\RedirectUrls;
use App\Http\Requests\AddInternalUrlRequest;


class NotificationService
{

  public function getAllNotifications($request)
  {
    try {
      $filters = $request->filters();
      $user_id = auth::user()->id;
      $user = User::with('roles')->find($user_id);
      $role = $user->roles->isEmpty() ? null : $user->roles->first()->id;

      if (is_admin_user()) {
        $notifications = NotificationEntity::orderBy('id', 'DESC')
          ->filterAndPaginate($filters);
      } else {
        $notifications = NotificationEntity::orderBy('id', 'DESC')
          ->join('notifiables', 'push_notifications.id', 'notifiables.push_notification_id')
          ->whereIn('notifiables.notifiable_id', [$user->id, $role])
          ->filterAndPaginate($filters);
      }

      return [
        'success'   => true,
        'message'   => 'messages.notification_found',
        'data'      =>  $notifications
      ];
    } catch (\Exception $e) {
      return [
        'success'  => false,
        'message'  => $e->getMessage()
      ];
    }
  }

  public function addUrl(AddInternalUrlRequest $request)
  {
      try {
          $validatedData = $request->validated();

          $attributes = [
            'url' => $validatedData['url'],
            'type' => $validatedData['type'],
          ];

          $values = [
              'name' => $validatedData['name'] ?? null,
          ];
          // Create a new RedirectUrl entry with the validated data
          RedirectUrls::updateOrInsert($attributes, $values);
          
          $redirectUrl = new RedirectUrlsResource(RedirectUrls::where($attributes)->first());
          
          // Return success
          return [
              'success' => true,
              'message' => __('messages.redirect_url_created'),
              'data' => $redirectUrl
          ];
      } catch (\Exception $e) {
          // Handle errors
          return [
              'success' => false,
              'message' => __('messages.error_creating_redirect_url'),
              'error' => $e->getMessage()
          ];
      }
  }


  public function getNotification($id)
  {
    try {
      $userId = auth()->user()->id;

      // Fetch the user with roles
      $user = User::with('roles')->find($userId);

      // Get the first role ID if the user has roles
      $role = $user->roles->isEmpty() ? null : $user->roles->first()->id;

      // Check if the user is an admin
      if (is_admin_user()) {
        $notification = NotificationEntity::find($id);
        if ($notification) {
          return [
            'success' => true,
            'data' => new NotificationResource($notification),
            'message' => __('messages.notification_found')
          ];
        } else {
          return [
            'success' => false,
            'message' => __('messages.not_found')
          ];
        }
      }

      // Fetch notification IDs for the current user and their role
      $notificationIds = Notifiable::whereIn('notifiable_id', [$userId, $role])->pluck('push_notification_id')->toArray();

      // Check if the requested notification ID exists in the user's notifications
      if (in_array($id, $notificationIds)) {
        $notification = NotificationEntity::find($id);
        if ($notification) {
          return [
            'success' => true,
            'data' => new NotificationResource($notification),
            'message' => __('messages.notification_found')
          ];
        }
      }

      // If notification not found, return failure message
      return [
        'success' => false,
        'message' => __('messages.not_found')
      ];
    } catch (\Exception $e) {
      // Catch any exceptions and return an error response
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }
  }
}
