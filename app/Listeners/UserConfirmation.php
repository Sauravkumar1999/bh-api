<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Events\UserUpdated;

class UserConfirmation
{
    /**
     * Handle the event.
     *
     * @param UserCreated|UserUpdated $event
     * @return void
     */
    public function handle(UserCreated|UserUpdated $event)
    {
        $user = $event->user;
        if ($user) {
            $user->update([
                'final_confirmation' => $user->status ? date('Y-m-d H:i:s') : null
            ]);
        }

    }
}
