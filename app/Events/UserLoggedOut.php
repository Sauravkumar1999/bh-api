<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class UserLoggedOut
{
    use SerializesModels;

    public $user;

    /**
     * Create a new event instance.
     *
     * @param  $user
     * @param $notification
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}
