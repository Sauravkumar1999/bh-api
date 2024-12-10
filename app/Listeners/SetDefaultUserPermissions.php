<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\Role;

class SetDefaultUserPermissions
{

    /**
     * Handle the event.
     *
     * @param UserCreated $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $user = $event->user;
        $data = $event->data;

        if (isset($data['biz-planner-reg'])) {
            $na = Role::whereName(setting('default_user_role'))->first();
            $user->attachRole($na);
        }

    }
}
